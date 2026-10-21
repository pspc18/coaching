<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeesSetting extends Model
{
    use SoftDeletes;

    protected $table = 'fees_settings';

    protected $fillable = [
        'branch_id',
        'session_id',
        'user_id',

        // 1. Receipt Number Generator Settings
        'receipt_prefix',
        'receipt_suffix',
        'receipt_digit_padding',
        'receipt_number_type',
        'receipt_include_session',
        'receipt_include_month',
        'receipt_starting_number',
        'receipt_reset_cycle',

        // 2. Payment Modes & Cashier Controls
        'allowed_payment_modes',
        'allow_partial_payment',
        'min_partial_amount',
        'min_deposit_percentage',
        'allow_manual_discount',
        'max_discount_percentage',
        'discount_requires_remark',

        // 3. Receipt Print & Letterhead Format
        'receipt_layout',
        'receipt_theme_color',
        'show_school_logo',
        'show_watermark',
        'show_signature_box',
        'show_payment_mode_details',
        'receipt_header_title',
        'receipt_terms_conditions',

        // 4. Automated Alerts & Notifications
        'send_receipt_whatsapp',
        'send_receipt_sms',
        'due_reminder_enabled',
        'due_reminder_days_before',
        'overdue_notice_enabled',

        'notes',
    ];

    /**
     * Get or create default settings for a given branch and session
     */
    public static function getSetting($branchId, $sessionId)
    {
        $setting = self::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$setting) {
            // Check if there is any setting in another session for this branch to copy from
            $prevSetting = self::where('branch_id', $branchId)
                ->orderByDesc('id')
                ->first();

            if ($prevSetting) {
                $attributes = $prevSetting->toArray();
                unset($attributes['id'], $attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);
                $attributes['session_id'] = $sessionId;
                $setting = self::create($attributes);
            } else {
                $setting = self::create([
                    'branch_id'                     => $branchId,
                    'session_id'                    => $sessionId,
                    'receipt_prefix'                => 'REC-',
                    'receipt_suffix'                => '',
                    'receipt_digit_padding'         => 4,
                    'receipt_number_type'           => 'both',
                    'receipt_include_session'       => 1,
                    'receipt_include_month'         => 0,
                    'receipt_starting_number'       => 1,
                    'receipt_reset_cycle'           => 'session',
                    'allowed_payment_modes'         => '1,2,3,4',
                    'allow_partial_payment'         => 1,
                    'min_partial_amount'            => 0.00,
                    'min_deposit_percentage'        => 0.00,
                    'allow_manual_discount'         => 1,
                    'max_discount_percentage'       => 20.00,
                    'discount_requires_remark'      => 1,
                    'receipt_layout'                => 'a4_dual',
                    'receipt_theme_color'           => '#8b2635',
                    'show_school_logo'              => 1,
                    'show_watermark'                => 1,
                    'show_signature_box'            => 1,
                    'show_payment_mode_details'     => 1,
                    'receipt_header_title'          => 'FEE RECEIPT',
                    'receipt_terms_conditions'      => "1. Fees once deposited are strictly non-refundable and non-transferable under any circumstances.\n2. Please preserve this receipt carefully for all future academic and examination reference.\n3. Cheque payment is subject to realization.",
                    'send_receipt_whatsapp'         => 1,
                    'send_receipt_sms'              => 0,
                    'due_reminder_enabled'          => 1,
                    'due_reminder_days_before'      => 3,
                    'overdue_notice_enabled'        => 1,
                ]);
            }
        }

        return $setting;
    }

    /**
     * Generate formatted receipt number based on settings
     */
    public function formatReceiptNumber($counter = 1, $sessionName = null)
    {
        $prefix = (string) ($this->receipt_prefix ?? '');
        $padding = max(1, min(10, (int) ($this->receipt_digit_padding ?? 4)));

        $sessionPart = '';
        if ($this->receipt_include_session) {
            if ($sessionName) {
                $cleanSession = preg_replace('/[^0-9\-]/', '', $sessionName);
                $sessionPart = $cleanSession . (!empty($cleanSession) ? '-' : '');
            } else {
                $sessionPart = date('Y') . '-';
            }
        }

        $monthPart = '';
        if ($this->receipt_include_month) {
            $monthPart = date('m') . '-';
        }

        $paddedNumber = str_pad((string) $counter, $padding, '0', STR_PAD_LEFT);

        return $prefix . $sessionPart . $monthPart . $paddedNumber;
    }

    /**
     * Get automatic session-wise receipt count directly from FeesDetailsInvoices table
     */
    public static function getSessionReceiptCount($branchId, $sessionId): int
    {
        return (int) \App\Models\fees\FeesDetailsInvoices::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->count();
    }

    /**
     * Calculate next sequential receipt counter automatically from FeesDetailsInvoices
     */
    public function getNextReceiptCounter($branchId = null, $sessionId = null, $sessionName = null): int
    {
        $bId = $branchId ?? $this->branch_id;
        $sId = $sessionId ?? $this->session_id;

        if (!$sessionName && $sId) {
            $sess = \App\Models\Sessions::find($sId);
            $sessionName = $sess ? ($sess->from_year . ($sess->to_year ? '-' . $sess->to_year : '')) : date('Y');
        }

        if ($this->receipt_reset_cycle === 'never') {
            $count = (int) \App\Models\fees\FeesDetailsInvoices::where('branch_id', $bId)->count();
        } else {
            $count = self::getSessionReceiptCount($bId, $sId);
        }

        $nextCounter = $count + 1;

        // Guard against duplicate invoice_no if any intermediate invoice was soft-deleted
        $guard = 0;
        while ($guard < 500) {
            $candidate = $this->formatReceiptNumber($nextCounter, $sessionName);
            $exists = \App\Models\fees\FeesDetailsInvoices::where('branch_id', $bId)
                ->where('session_id', $sId)
                ->where('invoice_no', $candidate)
                ->exists();
            if (!$exists) {
                break;
            }
            $nextCounter++;
            $guard++;
        }

        return $nextCounter;
    }
}
