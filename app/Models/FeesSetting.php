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

        // 2. Late Fine Configuration
        'fine_mode',
        'fine_amount',
        'fine_grace_days',
        'fine_max_cap',
        'allow_fine_waiver',
        'fine_waiver_requires_remark',

        // 3. Due Date Policy (Coaching & Institute Specific)
        'due_date_policy',
        'due_day_of_month',
        'due_days_after_admission',
        'advance_payment_allowed_days',

        // 4. Payment Modes & Cashier Controls
        'allowed_payment_modes',
        'allow_partial_payment',
        'min_partial_amount',
        'min_deposit_percentage',
        'allow_manual_discount',
        'max_discount_percentage',
        'discount_requires_remark',

        // 5. Receipt Print & Letterhead Format
        'receipt_layout',
        'show_school_logo',
        'show_watermark',
        'show_signature_box',
        'show_payment_mode_details',
        'receipt_header_title',
        'receipt_terms_conditions',

        // 6. Automated Alerts & Notifications
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
                    'fine_mode'                     => 'fixed',
                    'fine_amount'                   => 0.00,
                    'fine_grace_days'               => 0,
                    'fine_max_cap'                  => null,
                    'allow_fine_waiver'             => 1,
                    'fine_waiver_requires_remark'   => 1,
                    'due_date_policy'               => 'fixed_day_monthly',
                    'due_day_of_month'              => 10,
                    'due_days_after_admission'      => 15,
                    'advance_payment_allowed_days'  => 30,
                    'allowed_payment_modes'         => '1,2,3,4',
                    'allow_partial_payment'         => 1,
                    'min_partial_amount'            => 0.00,
                    'min_deposit_percentage'        => 0.00,
                    'allow_manual_discount'         => 1,
                    'max_discount_percentage'       => 20.00,
                    'discount_requires_remark'      => 1,
                    'receipt_layout'                => 'a4_dual',
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
        $suffix = (string) ($this->receipt_suffix ?? '');
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

        return $prefix . $sessionPart . $monthPart . $paddedNumber . $suffix;
    }
}
