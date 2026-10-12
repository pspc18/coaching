@php
    $modalStudent = $dashboardStudent ?? $getUser ?? Helper::getUser();
    $modalStudentName = trim(($modalStudent->first_name ?? '') . ' ' . ($modalStudent->last_name ?? ''));
    if (empty($modalStudentName)) $modalStudentName = 'Student';
    $modalClassName = optional($modalStudent->ClassTypes)->name ?? ($modalStudent->class_name ?? '---');
    $modalAdmNo = $modalStudent->admissionNo ?? '---';
    $modalPendingFees = (float)($pendingFees ?? 0);
    $pFees = (int)$modalPendingFees;
@endphp

{{-- ==========================================================================
     ARISE ERP - NATIVE MOBILE SLIDE-UP MODAL SHEET: PHONEPE FEE PAYMENT
     Exact same design system & classes as Homework, Complaints & Mobile Shell
     ========================================================================== --}}
<div class="mob-pay-backdrop" id="phonePeBackdrop" onclick="closePhonePeModal()"></div>

<div class="mob-pay-sheet" id="phonePePaySheet" role="dialog" aria-modal="true" aria-labelledby="phonePeSheetTitle">
    
    {{-- 1. Sheet Header (Exact Theme Gradient & Close Button) --}}
    <div class="mob-sheet-header">
        <div class="mob-sheet-title" id="phonePeSheetTitle">
            <i class="fa fa-shield" style="color: #38bdf8;"></i>
            <span>Pay School Fees</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closePhonePeModal()" aria-label="Close">
            <i class="fa fa-times"></i>
        </button>
    </div>

    {{-- 2. Pinned Top Meta Strip (Exact Theme Pinned Bar) --}}
    <div class="mob-sheet-fixed-meta">
        <div class="sheet-fixed-meta-row">
            <span class="sheet-meta-pill"><i class="fa fa-id-card-o text-muted"></i> Adm: <b>{{ $modalAdmNo }}</b></span>
            <span class="sheet-meta-pill"><i class="fa fa-graduation-cap text-muted"></i> Class: <b>{{ $modalClassName }}</b></span>
            <span class="sheet-meta-pill" style="color: #0284c7; border-color: #38bdf8; background: #e0f2fe;">
                <i class="fa fa-bolt"></i> PhonePe PG
            </span>
        </div>
        <div class="sheet-student-name">
            {{ strtoupper($modalStudentName) }}
        </div>
    </div>

    {{-- 3. Scrollable Body --}}
    <div class="mob-sheet-body">

        {{-- Outstanding Balance Snapshot Card --}}
        <div class="mob-due-card">
            <div class="mob-due-left">
                <div class="mob-due-sub">OUTSTANDING DUE BALANCE</div>
                <div class="mob-due-amount" id="modalDueAmountDisplay">₹ {{ number_format($modalPendingFees, 2) }}</div>
            </div>
            <span class="mob-due-badge"><i class="fa fa-clock-o"></i> Unpaid</span>
        </div>

        {{-- Payable Amount Input --}}
        <div class="mob-form-group">
            <div class="mob-form-label-row">
                <label for="phonepePayAmountInput" class="mob-form-label">Enter Payable Amount (₹)</label>
                <a href="javascript:void(0);" class="mob-link-full" onclick="setPhonePeAmount({{ $pFees }}, null)">
                    <i class="fa fa-refresh"></i> Pay Full Due
                </a>
            </div>

            <div class="mob-input-wrap">
                <span class="mob-currency">₹</span>
                <input type="number" 
                       id="phonepePayAmountInput" 
                       class="mob-amount-input" 
                       value="{{ $pFees }}" 
                       min="1" 
                       max="{{ $pFees }}" 
                       step="1"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       oninput="onPhonePeAmountChange(this.value)"
                       placeholder="0">
            </div>
            <div class="mob-hint">
                <i class="fa fa-info-circle text-primary"></i> Partial payment allowed. Official receipt generates immediately.
            </div>
        </div>

        {{-- Quick Select Chips --}}
        <div class="mob-chips-group">
            <div class="mob-chips-label">QUICK SELECT:</div>
            <div class="mob-chips-wrap">
                <button type="button" class="mob-chip active" onclick="setPhonePeAmount({{ $pFees }}, this)">
                    <i class="fa fa-check-circle chip-check"></i> Full Due (₹{{ number_format($pFees, 0) }})
                </button>
                @if($pFees > 1000)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(1000, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹1,000
                    </button>
                @endif
                @if($pFees > 2500)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(2500, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹2,500
                    </button>
                @endif
                @if($pFees > 5000)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(5000, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹5,000
                    </button>
                @endif
                @if($pFees > 10000)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(10000, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹10,000
                    </button>
                @endif
            </div>
        </div>

        {{-- Error Banner --}}
        <div id="phonepeModalError" class="mob-alert-error" style="display: none;"></div>

        {{-- Trust & Payment Methods Strip --}}
        <div class="mob-trust-box">
            <div class="mob-trust-icons">
                <span class="mob-trust-pill"><i class="fa fa-qrcode text-primary"></i> PhonePe / UPI</span>
                <span class="mob-trust-pill"><i class="fa fa-credit-card"></i> Cards</span>
                <span class="mob-trust-pill"><i class="fa fa-bank"></i> NetBanking</span>
            </div>
            <div class="mob-trust-secure">
                <i class="fa fa-lock text-success"></i> Instant Fee Receipt &bull; 100% Encrypted &amp; Secure
            </div>
        </div>

    </div>

    {{-- 4. Fixed Sheet Footer (Exact Theme Buttons) --}}
    <div class="mob-sheet-footer">
        <button type="button" class="btn btn-secondary mob-btn-cancel" onclick="closePhonePeModal()">
            Cancel
        </button>
        <button type="button" class="btn btn-primary mob-btn-pay" id="phonepeProceedBtn" onclick="submitPhonePePayment()">
            <i class="fa fa-shield"></i> Proceed to Pay <span id="phonepeBtnAmount">₹{{ number_format($pFees, 0) }}</span>
        </button>
    </div>

</div>

<style>
/* ==========================================================================
   ARISE ERP - NATIVE SLIDE-UP MODAL SHEET STYLES (MATCHES HOMEWORK / THEME)
   ========================================================================== */
.mob-pay-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .22s ease-in-out, visibility .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-pay-backdrop.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* Slide-Up Sheet Container Bound to Mobile Shell */
.mob-pay-sheet {
    display: flex;
    position: fixed;
    bottom: -100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 3px solid #0284c7;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 88vh;
    flex-direction: column;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.4);
    visibility: hidden;
    pointer-events: none;
}
.mob-pay-sheet.show {
    bottom: 0;
    visibility: visible;
    pointer-events: auto;
}

body.mob-sheet-lock {
    overflow: hidden !important;
}

/* Sheet Header */
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 5px 5px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 14px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .12s ease;
}
.mob-sheet-close:active {
    background: rgba(255, 255, 255, 0.25);
}

/* Pinned Top Meta Strip */
.mob-sheet-fixed-meta {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 14px;
    flex-shrink: 0;
    z-index: 2;
}
.sheet-fixed-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.sheet-meta-pill {
    font-size: 9.5px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.sheet-student-name {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    margin-top: 4px;
    letter-spacing: -0.2px;
}

/* Scrollable Body */
.mob-sheet-body {
    padding: 12px 14px 16px 14px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}

/* Due Balance Snapshot Card */
.mob-due-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-left: 3px solid #f59e0b;
    border-radius: 4px;
    padding: 9px 12px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-due-sub {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
.mob-due-amount {
    font-size: 18px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.2;
    margin-top: 1px;
}
.mob-due-badge {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #b45309;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Form Group & Input */
.mob-form-group {
    margin-bottom: 12px;
}
.mob-form-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.mob-form-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin: 0;
}
.mob-link-full {
    font-size: 10px;
    font-weight: 700;
    color: #0284c7;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-link-full:hover {
    text-decoration: underline;
}
.mob-input-wrap {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 10px;
    transition: all .15s ease;
}
.mob-input-wrap:focus-within {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-currency {
    font-size: 16px;
    font-weight: 800;
    color: #002C54;
    margin-right: 6px;
}
.mob-amount-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 17px;
    font-weight: 800;
    color: #002C54;
    width: 100%;
    padding: 0;
    font-family: inherit;
    -moz-appearance: textfield;
}
.mob-amount-input::-webkit-outer-spin-button,
.mob-amount-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.mob-hint {
    font-size: 9.5px;
    color: #64748b;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Quick Chips */
.mob-chips-group {
    margin-bottom: 12px;
}
.mob-chips-label {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.4px;
    margin-bottom: 5px;
}
.mob-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.mob-chip {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 4px 9px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    user-select: none;
}
.chip-check {
    display: none;
    font-size: 9px;
}
.mob-chip:hover {
    background: #f1f5f9;
}
.mob-chip.active {
    background: #e0f2fe;
    border-color: #0284c7;
    color: #0284c7;
    font-weight: 800;
}
.mob-chip.active .chip-check {
    display: inline-block;
}

/* Error Banner */
.mob-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-size: 10.5px;
    font-weight: 600;
    padding: 6px 10px;
    border-radius: 3px;
    margin-bottom: 10px;
    line-height: 1.4;
}

/* Trust Strip */
.mob-trust-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
}
.mob-trust-icons {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-bottom: 5px;
}
.mob-trust-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-trust-secure {
    text-align: center;
    font-size: 9px;
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

/* Sheet Footer */
.mob-sheet-footer {
    padding: 9px 14px calc(9px + env(safe-area-inset-bottom, 0px));
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-shrink: 0;
}
.mob-btn-cancel {
    flex: 1;
    padding: 7px 12px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    text-align: center;
}
.mob-btn-cancel:hover {
    background: #e2e8f0;
}
.mob-btn-pay {
    flex: 2;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 800;
    border-radius: 4px;
    background: #0284c7;
    border: 1px solid #0284c7;
    color: #ffffff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all .15s ease;
}
.mob-btn-pay:hover {
    background: #0369a1;
    border-color: #0369a1;
}
.mob-btn-pay:active {
    transform: scale(0.98);
}
.mob-btn-pay:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}
</style>

<script>
var maxDueFees = {{ (float)($modalPendingFees) }};

function openPhonePeModal(due) {
    if (due !== undefined && due !== null) {
        maxDueFees = parseFloat(due) || 0;
    }
    var backdrop = document.getElementById('phonePeBackdrop');
    var sheet = document.getElementById('phonePePaySheet');
    var input = document.getElementById('phonepePayAmountInput');
    var displayDue = document.getElementById('modalDueAmountDisplay');
    var err = document.getElementById('phonepeModalError');

    if (err) err.style.display = 'none';

    if (displayDue) {
        displayDue.innerText = '₹ ' + maxDueFees.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    if (input) {
        input.value = Math.round(maxDueFees);
        input.max = Math.round(maxDueFees);
        onPhonePeAmountChange(input.value);
    }

    // Set first chip (Full Due) active
    var chips = document.querySelectorAll('.mob-chip');
    chips.forEach(function(c, idx) {
        if (idx === 0) c.classList.add('active');
        else c.classList.remove('active');
    });

    if (backdrop && sheet) {
        backdrop.classList.add('show');
        sheet.classList.add('show');
        document.body.classList.add('mob-sheet-lock');
    }
}

function closePhonePeModal() {
    var backdrop = document.getElementById('phonePeBackdrop');
    var sheet = document.getElementById('phonePePaySheet');
    if (backdrop && sheet) {
        backdrop.classList.remove('show');
        sheet.classList.remove('show');
        document.body.classList.remove('mob-sheet-lock');
    }
}

function onPhonePeAmountChange(val) {
    var amt = parseFloat(val) || 0;
    var btnAmt = document.getElementById('phonepeBtnAmount');
    if (btnAmt) {
        btnAmt.innerText = '₹' + Math.round(amt).toLocaleString('en-IN');
    }

    // Highlight matching chip or unhighlight all
    var chips = document.querySelectorAll('.mob-chip');
    chips.forEach(function(c) {
        var text = c.innerText.replace(/[^0-9]/g, '');
        if (parseInt(text, 10) === Math.round(amt)) {
            c.classList.add('active');
        } else {
            c.classList.remove('active');
        }
    });
}

function setPhonePeAmount(val, btn) {
    var input = document.getElementById('phonepePayAmountInput');
    if (input) {
        input.value = val;
        onPhonePeAmountChange(val);
    }
    var chips = document.querySelectorAll('.mob-chip');
    chips.forEach(function(c) { c.classList.remove('active'); });
    if (btn) {
        btn.classList.add('active');
    } else {
        if (chips.length > 0) chips[0].classList.add('active');
    }
}

function submitPhonePePayment() {
    var input = document.getElementById('phonepePayAmountInput');
    var errBox = document.getElementById('phonepeModalError');
    var btn = document.getElementById('phonepeProceedBtn');
    var amount = parseFloat(input ? input.value : 0);

    if (errBox) errBox.style.display = 'none';

    if (!amount || amount <= 0) {
        showPhonePeError('Please enter a valid amount greater than ₹0.');
        return;
    }
    if (amount > maxDueFees) {
        showPhonePeError('Amount cannot exceed your total pending fee of ₹' + maxDueFees.toLocaleString('en-IN'));
        return;
    }

    // Set loading state
    btn.disabled = true;
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Connecting to PhonePe...';

    fetch('{{ route("student.payment.initiate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ amount: amount })
    })
    .then(function(res) {
        return res.json().then(function(data) {
            return { ok: res.ok, data: data };
        });
    })
    .then(function(response) {
        if (response.ok && response.data.status && response.data.redirect_url) {
            btn.innerHTML = '<i class="fa fa-check"></i> Redirecting to PhonePe...';
            window.location.href = response.data.redirect_url;
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            showPhonePeError(response.data.message || 'Unable to initiate PhonePe payment. Please try again.');
        }
    })
    .catch(function(error) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        showPhonePeError('Network error connecting to payment gateway: ' + error.message);
    });
}

function showPhonePeError(msg) {
    var errBox = document.getElementById('phonepeModalError');
    if (errBox) {
        errBox.innerText = msg;
        errBox.style.display = 'block';
    } else {
        alert(msg);
    }
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhonePeModal();
    }
});
</script>
