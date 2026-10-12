@php
    $modalStudent = $dashboardStudent ?? $getUser ?? Helper::getUser();
    $modalStudentName = trim(($modalStudent->first_name ?? '') . ' ' . ($modalStudent->last_name ?? ''));
    if (empty($modalStudentName)) $modalStudentName = 'Student';
    $modalClassName = optional($modalStudent->ClassTypes)->name ?? ($modalStudent->class_name ?? '---');
    $modalAdmNo = $modalStudent->admissionNo ?? '---';
    $modalPendingFees = (float)($pendingFees ?? 0);
    $modalInitial = strtoupper(mb_substr($modalStudentName, 0, 1, 'UTF-8')) ?: 'S';
@endphp

<!-- Arise ERP - PhonePe Payment Gateway Bottom Sheet Modal -->
<div id="phonePePayModal" class="phonepe-modal-overlay" style="display: none;" aria-hidden="true">
    <div class="phonepe-modal-backdrop" onclick="closePhonePeModal()"></div>
    
    <div class="phonepe-modal-container" role="dialog" aria-modal="true">
        <!-- Native Mobile Sheet Drag Handle -->
        <div class="phonepe-drag-handle" aria-hidden="true"></div>

        <!-- Modal Header -->
        <div class="phonepe-modal-header">
            <div class="phonepe-header-left">
                <div class="phonepe-logo-icon">
                    <i class="fa fa-shield"></i>
                </div>
                <div>
                    <h3 class="phonepe-modal-title">Pay School Fees</h3>
                    <span class="phonepe-modal-subtitle">PhonePe Secure PG &bull; Arise ERP</span>
                </div>
            </div>
            <button type="button" class="phonepe-close-btn" onclick="closePhonePeModal()" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <!-- Scrollable Modal Body -->
        <div class="phonepe-modal-body">
            
            <!-- Student Mini Identity Strip -->
            <div class="phonepe-student-strip">
                <div class="phonepe-student-avatar">
                    {{ $modalInitial }}
                </div>
                <div class="phonepe-student-meta">
                    <div class="student-name-text">{{ strtoupper($modalStudentName) }}</div>
                    <div class="student-sub-pills">
                        <span class="meta-pill"><i class="fa fa-id-card-o"></i> Adm: <strong>{{ $modalAdmNo }}</strong></span>
                        <span class="meta-pill"><i class="fa fa-graduation-cap"></i> Class: <strong>{{ $modalClassName }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Due Balance Summary Card -->
            <div class="phonepe-due-box">
                <div>
                    <div class="due-label">Current Outstanding Due</div>
                    <div class="due-amount" id="modalDueAmountDisplay">₹ {{ number_format($modalPendingFees, 2) }}</div>
                </div>
                <div class="due-box-badge">
                    <i class="fa fa-clock-o"></i> Unpaid Dues
                </div>
            </div>

            <!-- Amount Input Field -->
            <div class="phonepe-input-group">
                <div class="phonepe-label-row">
                    <label for="phonepePayAmountInput" class="phonepe-input-label">
                        Enter Payable Amount (₹)
                    </label>
                    <button type="button" class="quick-full-link" onclick="setPhonePeAmount({{ (int)$modalPendingFees }}, null)">
                        <i class="fa fa-refresh"></i> Pay Full Due
                    </button>
                </div>
                
                <div class="phonepe-input-wrapper">
                    <span class="currency-prefix">₹</span>
                    <input type="number" 
                           id="phonepePayAmountInput" 
                           class="phonepe-amount-input" 
                           value="{{ (int)$modalPendingFees }}" 
                           min="1" 
                           max="{{ (int)$modalPendingFees }}" 
                           step="1"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           oninput="onPhonePeAmountChange(this.value)"
                           placeholder="0">
                </div>
                <div class="phonepe-input-hint">
                    <i class="fa fa-info-circle text-primary"></i> Partial payment allowed. Official receipt generates immediately.
                </div>
            </div>

            <!-- Quick Amount Selector Chips -->
            @php
                $pFees = (int)$modalPendingFees;
            @endphp
            <div class="phonepe-chips-section">
                <div class="chips-title">Quick Select:</div>
                <div class="phonepe-chips-row">
                    <button type="button" class="phonepe-chip active" onclick="setPhonePeAmount({{ $pFees }}, this)">
                        <i class="fa fa-check-circle chip-check"></i> Full Due (₹{{ number_format($pFees, 0) }})
                    </button>
                    @if($pFees > 1000)
                        <button type="button" class="phonepe-chip" onclick="setPhonePeAmount(1000, this)">
                            <i class="fa fa-check-circle chip-check"></i> ₹1,000
                        </button>
                    @endif
                    @if($pFees > 2500)
                        <button type="button" class="phonepe-chip" onclick="setPhonePeAmount(2500, this)">
                            <i class="fa fa-check-circle chip-check"></i> ₹2,500
                        </button>
                    @endif
                    @if($pFees > 5000)
                        <button type="button" class="phonepe-chip" onclick="setPhonePeAmount(5000, this)">
                            <i class="fa fa-check-circle chip-check"></i> ₹5,000
                        </button>
                    @endif
                    @if($pFees > 10000)
                        <button type="button" class="phonepe-chip" onclick="setPhonePeAmount(10000, this)">
                            <i class="fa fa-check-circle chip-check"></i> ₹10,000
                        </button>
                    @endif
                </div>
            </div>

            <!-- Error Banner -->
            <div id="phonepeModalError" class="phonepe-alert-error" style="display: none;"></div>

            <!-- Payment Methods Strip -->
            <div class="phonepe-trust-strip">
                <div class="trust-icons">
                    <span class="pay-method-badge"><i class="fa fa-qrcode text-primary"></i> PhonePe / UPI</span>
                    <span class="pay-method-badge"><i class="fa fa-credit-card"></i> Cards</span>
                    <span class="pay-method-badge"><i class="fa fa-bank"></i> NetBanking</span>
                </div>
                <div class="security-text">
                    <i class="fa fa-lock text-success"></i> Instant Fee Receipt &bull; 100% Encrypted &amp; Secure
                </div>
            </div>

        </div>

        <!-- Fixed Modal Footer -->
        <div class="phonepe-modal-footer">
            <button type="button" class="phonepe-btn-cancel" onclick="closePhonePeModal()">
                Cancel
            </button>
            <button type="button" class="phonepe-btn-pay" id="phonepeProceedBtn" onclick="submitPhonePePayment()">
                <i class="fa fa-shield"></i> Proceed to Pay <span id="phonepeBtnAmount">₹{{ number_format($pFees, 0) }}</span>
            </button>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT PAYMENT BOTTOM SHEET MODAL
   ========================================================================== */
.phonepe-modal-overlay {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100dvh;
    z-index: 999999;
    display: flex;
    align-items: flex-end; /* Bottom sheet on mobile */
    justify-content: center;
    padding: 0;
    visibility: hidden;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.22s ease, visibility 0.22s ease;
}
.phonepe-modal-overlay.is-open {
    visibility: visible;
    opacity: 1;
    pointer-events: auto;
}
.phonepe-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 20, 40, 0.68);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.phonepe-modal-container {
    position: relative;
    z-index: 10;
    background: #ffffff;
    width: 100%;
    max-width: 460px;
    border-radius: 20px 20px 0 0;
    box-shadow: 0 -8px 35px rgba(0, 20, 40, 0.3);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: 92dvh;
    transform: translateY(100%);
    transition: transform 0.26s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #cbd5e1;
    border-bottom: none;
}
.phonepe-modal-overlay.is-open .phonepe-modal-container {
    transform: translateY(0);
}

/* Drag handle on mobile */
.phonepe-drag-handle {
    width: 38px;
    height: 4px;
    border-radius: 99px;
    background: #cbd5e1;
    margin: 8px auto 4px auto;
}

/* Desktop centering */
@media (min-width: 641px) {
    .phonepe-modal-overlay {
        align-items: center;
        padding: 20px;
    }
    .phonepe-modal-container {
        border-radius: 18px;
        border-bottom: 1px solid #cbd5e1;
        box-shadow: 0 20px 50px rgba(0, 25, 55, 0.35);
        transform: translateY(16px) scale(0.96);
        max-height: 88vh;
    }
    .phonepe-modal-overlay.is-open .phonepe-modal-container {
        transform: translateY(0) scale(1);
    }
    .phonepe-drag-handle {
        display: none;
    }
}

/* Header */
.phonepe-modal-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 60%, #0a4275 100%);
    color: #ffffff;
    padding: 13px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(56, 189, 248, 0.25);
    position: relative;
    flex-shrink: 0;
}
.phonepe-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.phonepe-logo-icon {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(56, 189, 248, 0.35);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #38bdf8;
    font-size: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.18);
}
.phonepe-modal-title {
    font-size: 14.5px;
    font-weight: 800;
    margin: 0;
    color: #ffffff;
    letter-spacing: -0.2px;
    line-height: 1.25;
}
.phonepe-modal-subtitle {
    font-size: 10px;
    color: #93c5fd;
    font-weight: 600;
    display: block;
    margin-top: 2px;
}
.phonepe-close-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #ffffff;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}
.phonepe-close-btn:hover {
    background: rgba(255, 255, 255, 0.22);
}
.phonepe-close-btn:active {
    transform: scale(0.92);
}

/* Modal Body */
.phonepe-modal-body {
    padding: 14px 16px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    flex: 1;
}

/* Student Identity Strip */
.phonepe-student-strip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 9px 12px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.phonepe-student-avatar {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
}
.phonepe-student-meta {
    flex: 1;
    min-width: 0;
}
.student-name-text {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.student-sub-pills {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 3px;
    font-size: 10.5px;
    color: #64748b;
}
.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 10px;
}
.meta-pill strong {
    color: #0f172a;
}

/* Due Balance Summary */
.phonepe-due-box {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1px solid #fde68a;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.due-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #92400e;
    letter-spacing: 0.5px;
}
.due-amount {
    font-size: 21px;
    font-weight: 800;
    color: #b45309;
    letter-spacing: -0.4px;
    margin-top: 2px;
}
.due-box-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #ffffff;
    border: 1px solid #fcd34d;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    color: #b45309;
}

/* Amount Input */
.phonepe-input-group {
    margin-bottom: 12px;
}
.phonepe-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.phonepe-input-label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin: 0;
}
.quick-full-link {
    background: none;
    border: none;
    color: #0284c7;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.quick-full-link:hover {
    text-decoration: underline;
}
.phonepe-input-wrapper {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 6px 14px;
    transition: all 0.15s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.phonepe-input-wrapper:focus-within {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18);
}
.currency-prefix {
    font-size: 20px;
    font-weight: 800;
    color: #002C54;
    margin-right: 8px;
}
.phonepe-amount-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 21px;
    font-weight: 800;
    color: #002C54;
    width: 100%;
    padding: 2px 0;
    font-family: inherit;
    -moz-appearance: textfield;
}
.phonepe-amount-input::-webkit-outer-spin-button,
.phonepe-amount-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.phonepe-input-hint {
    font-size: 10.5px;
    color: #64748b;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Quick Chips */
.phonepe-chips-section {
    margin-bottom: 14px;
}
.chips-title {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.4px;
    margin-bottom: 6px;
}
.phonepe-chips-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.phonepe-chip {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 6px 11px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    user-select: none;
}
.chip-check {
    font-size: 10px;
    display: none;
}
.phonepe-chip:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}
.phonepe-chip.active {
    background: #e0f2fe;
    border-color: #0284c7;
    color: #0284c7;
    font-weight: 800;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
}
.phonepe-chip.active .chip-check {
    display: inline-block;
}

/* Error Banner */
.phonepe-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-size: 11.5px;
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 12px;
    line-height: 1.4;
}

/* Trust Strip */
.phonepe-trust-strip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 12px;
}
.trust-icons {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-bottom: 6px;
}
.pay-method-badge {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.security-text {
    text-align: center;
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* Modal Footer */
.phonepe-modal-footer {
    padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.phonepe-btn-cancel {
    flex: 1;
    min-height: 42px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}
.phonepe-btn-cancel:hover {
    background: #e2e8f0;
}
.phonepe-btn-cancel:active {
    transform: scale(0.98);
}
.phonepe-btn-pay {
    flex: 2;
    min-height: 42px;
    background: linear-gradient(135deg, #002C54 0%, #001f3f 50%, #5f259f 100%);
    border: 1px solid #001f3f;
    color: #ffffff;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    transition: all 0.18s ease;
}
.phonepe-btn-pay:hover {
    opacity: 0.95;
    box-shadow: 0 6px 18px rgba(0, 44, 84, 0.35);
}
.phonepe-btn-pay:active {
    transform: scale(0.98);
}
.phonepe-btn-pay:disabled {
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
    var modal = document.getElementById('phonePePayModal');
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
    var chips = document.querySelectorAll('.phonepe-chip');
    chips.forEach(function(c, idx) {
        if (idx === 0) c.classList.add('active');
        else c.classList.remove('active');
    });

    if (modal) {
        modal.style.display = 'flex';
        // Request animation frame for smooth transition
        requestAnimationFrame(function() {
            modal.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';
    }
}

function closePhonePeModal() {
    var modal = document.getElementById('phonePePayModal');
    if (modal) {
        modal.classList.remove('is-open');
        setTimeout(function() {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 220);
    }
}

function onPhonePeAmountChange(val) {
    var amt = parseFloat(val) || 0;
    var btnAmt = document.getElementById('phonepeBtnAmount');
    if (btnAmt) {
        btnAmt.innerText = '₹' + Math.round(amt).toLocaleString('en-IN');
    }

    // Highlight matching chip or unhighlight all
    var chips = document.querySelectorAll('.phonepe-chip');
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
    var chips = document.querySelectorAll('.phonepe-chip');
    chips.forEach(function(c) { c.classList.remove('active'); });
    if (btn) {
        btn.classList.add('active');
    } else {
        // If "Pay Full Due" link was clicked
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

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhonePeModal();
    }
});
</script>
