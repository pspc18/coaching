<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MultipleCronController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Helper;
use Carbon\Carbon;

class AttendanceWebhookController extends Controller
{
    const DUPLICATE_THRESHOLD_MINUTES = 10; // 10 minutes gap to consider a new punch
    const ALERT_MOBILE = '8209949186';

    public function sync(Request $request)
    {
        // ── 1. Verify HMAC signature ──────────────────────────────────────────
        $signature = hash_hmac(
            'sha256',
            json_encode(['unique_id' => $request->unique_id]),
            env('ATTENDANCE_WEBHOOK_SECRET', 'default_secret')
        );

        if ($signature !== $request->header('X-Signature')) {
            Log::warning('Biometric webhook: invalid signature', [
                'unique_id'        => $request->unique_id,
                'ip'               => $request->ip(),
                'serialNumber'     => $request->serialNumber,
                'deviceName'       => $request->deviceName,
                'deviceId'         => $request->deviceId,
                'received_signature' => $request->header('X-Signature'),
                'expected_signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }
        // ── 2. Validate required fields ───────────────────────────────────────
        $request->validate([
            'unique_id' => 'required',
            'date'      => 'required|date_format:Y-m-d',
            'time'      => 'required|date_format:H:i:s',
        ]);

        $uniqueId = $request->unique_id;
        $date     = $request->date;
        $time     = $request->time;

        // ── 3. Skip exact duplicate in biometric_attendance ───────────────────
        $exactDuplicate = DB::table('biometric_attendance')
            ->where('unique_id', $uniqueId)
            ->where('date', $date)
            ->where('time', $time)
            ->exists();

        if ($exactDuplicate) {
            // Log::info('Biometric webhook: exact duplicate skipped', [
            //     'unique_id' => $uniqueId,
            //     'date'      => $date,
            //     'time'      => $time,
            //     'serialNumber'     => $request->serialNumber,
            //     'deviceName'       => $request->deviceName,
            //     'deviceId'         => $request->deviceId,
            // ]);
            return response()->json(['status' => 'skipped', 'reason' => 'exact duplicate']);
        }

        // ── 4. Find user ──────────────────────────────────────────────────────
        $user = DB::table('users')
            ->where('attendance_unique_id', $uniqueId)
            ->orWhere(function ($query) use ($uniqueId) {
                if (stripos((string) $uniqueId, 'USR-') === 0) {
                    $query->where('id', (int) str_replace('USR-', '', (string) $uniqueId));
                }
            })
            ->first();

        $student = DB::table('admissions')
            ->where('attendance_unique_id', $uniqueId)
            ->orWhere('admissionNo', $uniqueId)
            ->first();

        if (!$user) {
            // Log::warning('Biometric webhook: user not found', [
            //     'unique_id' => $uniqueId,
            //     'date'      => $date,
            //     'time'      => $time,
            // ]);
            // Still insert raw punch — don't lose device data
        }

        // ── 5. Insert raw punch into biometric_attendance ─────────────────────
        DB::table('biometric_attendance')->insert([
            'unique_id'    => $uniqueId,
            'user_id'      => $user->id ?? null,
            'admission_id' => $student->id ?? null,
            'branch_id'    => $student->branch_id ?? $user->branch_id ?? null,
            'session_id'   => $student->session_id ?? $user->session_id ?? null,
            'entity_type'  => $student ? 'student' : ($user ? 'staff' : null),
            'date'         => $date,
            'time'         => $time,
            'in_time'      => $request->in_time,
            'out_time'     => $request->out_time,
            'source'       => 'webhook',
            'raw_data'     => json_encode($request->all()),
            'serialNumber' => $request->serialNumber,
            'deviceName'   => $request->deviceName,
            'VerifyMode'   => $request->VerifyMode,
            'deviceId'     => $request->deviceId,
            'IOMode'       => $request->IOMode,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Log::info('Biometric Webhook Inserted: ', [
            'unique_id'   => $uniqueId,
           // 'name'        => $user->name ?? 'Unknown',
            'date'        => $date,
            'time'        => $time,
            'deviceName'  => $request->deviceName,
            'serialNumber' => $request->serialNumber,
            'deviceId'     => $request->deviceId,
        ]);

        $sync = null;
        $branchId = (int) ($student->branch_id ?? $user->branch_id ?? 0);
        $sessionId = (int) ($student->session_id ?? $user->session_id ?? 0);
        if ($branchId > 0 && $sessionId > 0) {
            try {
                $syncResponse = app(MultipleCronController::class)->detectAttendanceType(
                    Request::create('/attendance/attendance-cron-manager', 'GET', [
                        'branch_id' => $branchId,
                        'session_id' => $sessionId,
                    ])
                );
                $sync = json_decode($syncResponse->getContent(), true);
            } catch (\Throwable $e) {
                Log::error('Biometric webhook sync failed.', [
                    'unique_id' => $uniqueId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ── 6. Sync into attendance_marks ─────────────────────────────────────
        // if ($user) {
        //     $this->syncAttendanceMark($uniqueId, $date, $time, $user, $request);
        // } else {
        //     // Log::warning('Biometric webhook: attendance_marks skipped — user not found', [
        //     //     'unique_id' => $uniqueId,
        //     //     'date'      => $date,
        //     //     'time'      => $time,
        //     //     'serialNumber'     => $request->serialNumber,
        //     //     'deviceName'       => $request->deviceName,
        //     //     'deviceId'         => $request->deviceId,
        //     // ]);
        // }

        return response()->json(['status' => 'inserted', 'sync' => $sync]);
    }

    // private function syncAttendanceMark(string $uniqueId, string $date, string $time, $user, $request): void
    // {
    //     $existing = DB::table('attendance_marks')
    //         ->where('unique_id', $user->unique_code)
    //         ->where('date', $date)
    //         ->first();

    //     $userName        = trim((string) ($user->name ?? 'Unknown'));
    //     $formattedDate   = Carbon::parse($date)->format('d/m/Y');
    //     $formattedTime   = Carbon::createFromFormat('H:i:s', $time)->format('h:i A');

    //     // ── Case A: No record yet → INSERT with in_time ───────────────────────
    //     if (!$existing) {
    //         DB::table('attendance_marks')->insert([
    //             'unique_id'   => $user->unique_code,
    //             'entity_type' => $this->resolveEntityType($user),
    //             'user_id'     => $user->id,
    //             'date'        => $date,
    //             'in_time'     => $time,
    //             'source'      => 'webhook',
    //             'VerifyMode'   => $request->VerifyMode,
    //             'out_time'    => null,
    //             'status'      => null,
    //             'created_at'  => now(),
    //             'updated_at'  => now(),
    //         ]);

    //         // Log::info('attendance_marks: IN punch recorded', [
    //         //     'unique_id'   => $uniqueId,
    //         //     'unique_code' => $user->unique_code,
    //         //     'name'        => $userName,
    //         //     'date'        => $date,
    //         //     'in_time'     => $time,
    //         // ]);

    //         // ✅ FIX: use $time & $date directly — $existing was null here
    //         $message = "From Biometric {$userName}\nhas punched IN at {$formattedTime} on {$formattedDate}.";
    //         Helper::sendWhatsappMessage(self::ALERT_MOBILE, $message);

    //         return;
    //     }

    //     // ── Case B: out_time already set → day complete, skip ─────────────────
    //     if (!is_null($existing->out_time)) {
    //         // Log::info('attendance_marks: day already complete, punch skipped', [
    //         //     'unique_id'   => $uniqueId,
    //         //     'unique_code' => $user->unique_code,
    //         //     'name'        => $userName,
    //         //     'date'        => $date,
    //         //     'time'        => $time,
    //         //     'in_time'     => $existing->in_time,
    //         //     'out_time'    => $existing->out_time,
    //         // ]);
    //         return;
    //     }

    //     // ── Case C: in_time exists, out_time null → check 30-min gap ──────────
    //     $inTime      = Carbon::createFromFormat('H:i:s', $existing->in_time);
    //     $currentTime = Carbon::createFromFormat('H:i:s', $time);
    //     $gapMinutes  = $inTime->diffInMinutes($currentTime, false); // signed

    //     if ($gapMinutes >= self::DUPLICATE_THRESHOLD_MINUTES) {
    //         DB::table('attendance_marks')
    //             ->where('id', $existing->id)
    //             ->update([
    //                 'out_time'   => $time,
    //                 'updated_at' => now(),
    //             ]);

    //         // Log::info('attendance_marks: OUT punch recorded', [
    //         //     'unique_id'   => $uniqueId,
    //         //     'unique_code' => $user->unique_code,
    //         //     'name'        => $userName,
    //         //     'date'        => $date,
    //         //     'in_time'     => $existing->in_time,
    //         //     'out_time'    => $time,
    //         // ]);

    //         // ✅ FIX: use $time directly — $existing->out_time was still null at this point
    //         $message = "From Biometric {$userName}\nhas punched OUT at {$formattedTime} on {$formattedDate}.";
    //         Helper::sendWhatsappMessage(self::ALERT_MOBILE, $message);

    //     } else {
    //         // Log::info('attendance_marks: punch too soon, skipped (< 30 min gap)', [
    //         //     'unique_id'   => $uniqueId,
    //         //     'unique_code' => $user->unique_code,
    //         //     'name'        => $userName,
    //         //     'date'        => $date,
    //         //     'time'        => $time,
    //         //     'in_time'     => $existing->in_time,
    //         //     'gap_minutes' => $gapMinutes,
    //         // ]);
    //     }
    // }

    // private function resolveEntityType($user): string
    // {
    //     if (!$user || empty($user->unique_code)) {
    //         return 'staff';
    //     }

    //     $code = strtoupper($user->unique_code);

    //     if (str_contains($code, '-STD-') || str_contains($code, 'STU')) {
    //         return 'student';
    //     }

    //     return 'staff';
    // }
}
