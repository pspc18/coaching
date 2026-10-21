<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Validator; 
use App\Models\Admission;
use App\Models\NotificationToken;
use App\Models\Notification;
use App\Models\User;
use Session;
use Carbon\Carbon;
use Str;
use Helper;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StudentNotificationService;

class NotificationController extends Controller

{
        private function notificationsService(): StudentNotificationService
        {
            return app(StudentNotificationService::class);
        }

        private function normalizeCategory(Request $request): string
        {
            $category = strtolower(trim((string) $request->input('category', $request->query('category', 'all'))));
            $legacyType = strtolower(trim((string) $request->query('type', '')));

            if ($category === 'all' && $legacyType === 'notice') {
                $category = 'notice';
            }

            if (!in_array($category, $this->notificationsService()->allowedCategories(), true)) {
                $category = 'all';
            }

            return $category;
        }

        private function notificationQuery(string $category, bool $includeHidden = false)
        {
            $query = $this->notificationsService()->baseQuery((int) Session::get('id'), $includeHidden);

            return $this->notificationsService()->applyCategory($query, $category);
        }

        public function notificationFatch(Request $request)
        {
            $filter = strtolower(trim((string) $request->query('filter', 'all')));
            if (!in_array($filter, ['all', 'read', 'unread'], true)) {
                $filter = 'all';
            }

            $category = $this->normalizeCategory($request);
            $search = trim((string) $request->query('search', ''));

            $query = $this->notificationQuery($category);

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            if ($filter === 'read') {
                $query->where('message_seen', 1);
            } elseif ($filter === 'unread') {
                $query->where('message_seen', 0);
            }

            $notifications = $query
                ->select([
                    'id',
                    'admission_id',
                    'managed_notice_id',
                    'title',
                    'content',
                    'attachment_path',
                    'attachment_name',
                    'attachment_mime',
                    'type',
                    'message_seen',
                    'created_at',
                ])
                ->orderBy('message_seen', 'asc')
                ->orderByDesc('id')
                ->paginate(15)
                ->appends(array_filter([
                    'filter' => $filter !== 'all' ? $filter : null,
                    'category' => $category !== 'all' ? $category : null,
                    'search' => $search !== '' ? $search : null,
                ]));

            // Batch associate Fee Payment notifications with real-time Fee Receipt PDF routes
            $admissionId = (int) Session::get('id');
            $feeNotifs = $notifications->getCollection()->filter(function ($n) {
                return in_array($n->type, ['fee_payment', 'fee_revert'], true)
                    || stripos((string) $n->title, 'Fee Payment') !== false
                    || stripos((string) $n->content, 'Receipt No:') !== false;
            });

            if ($feeNotifs->isNotEmpty()) {
                $receiptMap = [];
                foreach ($feeNotifs as $n) {
                    $recNo = null;
                    if (preg_match('/Receipt No:\s*([^\r\n]+)/i', (string) $n->content, $m)) {
                        $recNo = trim($m[1]);
                    } elseif (preg_match('/Offline Receipt No:\s*([^\r\n]+)/i', (string) $n->content, $m)) {
                        $recNo = trim($m[1]);
                    }
                    if ($recNo !== null && $recNo !== '') {
                        $receiptMap[$n->id] = $recNo;
                    }
                }

                $candidateNos = array_values(array_unique(array_filter($receiptMap)));
                $invoices = collect();
                if (!empty($candidateNos)) {
                    $invoices = \App\Models\fees\FeesDetailsInvoices::where('admission_id', $admissionId)
                        ->where(function ($q) use ($candidateNos) {
                            $q->whereIn('invoice_no', $candidateNos)
                              ->orWhereIn('offline_receipt_no', $candidateNos);
                            foreach ($candidateNos as $cno) {
                                if (is_numeric($cno)) {
                                    $unpadded = (string) (int) $cno;
                                    $q->orWhere('invoice_no', $unpadded)
                                      ->orWhere('offline_receipt_no', $unpadded);
                                }
                            }
                        })
                        ->whereIn('status', [0, 1])
                        ->get();
                }

                foreach ($feeNotifs as $n) {
                    $recNo = $receiptMap[$n->id] ?? null;
                    $matchedInvoice = null;
                    if ($recNo !== null) {
                        $unpadded = is_numeric($recNo) ? (string) (int) $recNo : null;
                        $matchedInvoice = $invoices->first(function ($inv) use ($recNo, $unpadded) {
                            return (string) $inv->invoice_no === (string) $recNo
                                || (string) $inv->offline_receipt_no === (string) $recNo
                                || ($unpadded !== null && ((string) $inv->invoice_no === $unpadded || (string) $inv->offline_receipt_no === $unpadded));
                        });
                    }

                    if ($matchedInvoice) {
                        $n->fee_invoice_id = $matchedInvoice->id;
                        $n->fee_receipt_no = $matchedInvoice->invoice_no ?: ($matchedInvoice->offline_receipt_no ?: $recNo);
                        $n->fee_download_url = route('student.fees.receipt.download', $matchedInvoice->id);
                        $n->fee_view_url = route('student.fees.receipt.view', $matchedInvoice->id);
                    } elseif ($recNo !== null) {
                        $n->fee_receipt_no = $recNo;
                        $n->fee_download_url = route('student.fees.receipt.download', $recNo);
                        $n->fee_view_url = route('student.fees.receipt.view', $recNo);
                    }
                }
            }

            $categoryCounts = $this->notificationsService()->counts((int) Session::get('id'));
            $unreadCount = (int) ($categoryCounts[$category] ?? $categoryCounts['all'] ?? 0);

            return view('student_login.notification.notification_list', compact(
                'notifications',
                'filter',
                'unreadCount',
                'category',
                'categoryCounts',
                'search'
            ));
        }

        public function markAttendanceRead(Request $request, int $id)
        {
            $notification = $this->notificationQuery($this->normalizeCategory($request), true)
                ->where('id', $id)
                ->firstOrFail();

            if ((int) $notification->message_seen !== 1) {
                $notification->message_seen = 1;
                $notification->save();
            }

            $categoryCounts = $this->notificationsService()->counts((int) Session::get('id'));

            return response()->json([
                'ok' => true,
                'unread_count' => (int) ($categoryCounts['all'] ?? 0),
                'category_counts' => $categoryCounts,
            ]);
        }

        public function markAllAttendanceRead(Request $request)
        {
            $query = $this->notificationQuery($this->normalizeCategory($request), true);
            $updated = $query
                ->where('message_seen', 0)
                ->update([
                    'message_seen' => 1,
                    'updated_at' => now(),
                ]);

            return redirect()->back()->with(
                'message',
                ((int) $updated) > 0
                    ? 'All notifications marked as read.'
                    : 'No unread notifications found.'
            );
        }

        public function clearAllAttendanceNotifications(Request $request)
        {
            $query = $this->notificationQuery($this->normalizeCategory($request), true);
            $deleted = $query->delete();

            $redirectUrl = url('notificationFatchStudent');
            $category = $this->normalizeCategory($request);
            if ($category !== 'all') {
                $redirectUrl .= '?category='.$category;
            }

            return redirect()->to($redirectUrl)
                ->with('message', 'All notifications cleared.');
        }

}
