<!-- PhonePe Payment Gateway Modal Component -->
<div id="phonePePayModal" class="phonepe-modal-overlay" style="display: none;">
    <div class="phonepe-modal-container">
        <!-- Modal Header -->
        <div class="phonepe-modal-header">
            <div class="phonepe-header-left">
                <div class="phonepe-logo-icon">
                    <i class="fa fa-shield"></i>
                </div>
                <div>
                    <h4 class="phonepe-modal-title">Pay School Fees</h4>
                    <span class="phonepe-modal-subtitle">Powered by PhonePe Payment Gateway</span>
                </div>
            </div>
            <button type="button" class="phonepe-close-btn" onclick="closePhonePeModal()" aria-label="Close">
                &times;
            </button>
        </div>

        <!-- Modal Body -->
        <div class="phonepe-modal-body">
            <!-- Student Mini Banner -->
            <div class="phonepe-student-strip">
                <div>
                    <div class="strip-label">STUDENT</div>
                    <div class="strip-val">{{ strtoupper(trim(($dashboardStudent->first_name ?? '') . ' ' . ($dashboardStudent->last_name ?? ''))) }}</div>
                </div>
                <div style="text-align: right;">
                    <div class="strip-label">CLASS / ADM NO</div>
                    <div class="strip-val">{{ $dashboardStudent->class_name ?? '---' }} &bull; {{ $dashboardStudent->admissionNo ?? '---' }}</div>
                </div>
            </div>

            <!-- Due Balance Summary -->
            <div class="phonepe-due-box">
                <div class="due-box-left">
                    <span class="due-label">Current Outstanding Due</span>
                    <span class="due-amount" id="modalDueAmountDisplay">₹ {{ number_format($pendingFees ?? 0, 2) }}</span>
                </div>
                <div class="due-box-badge">
                    <i class="fa fa-clock-o"></i> Pending
                </div>
            </div>

            <!-- Amount Input Field -->
            <div class="phonepe-input-group">
                <label for="phonepePayAmountInput" class="phonepe-input-label">
                    Enter Amount to Pay Now (₹)
                </label>
                <div class="phonepe-input-wrapper">
                    <span class="currency-prefix">₹</span>
                    <input type="number" 
                           id="phonepePayAmountInput" 
                           class="phonepe-amount-input" 
                           value="{{ (int)($pendingFees ?? 0) }}" 
                           min="1" 
                           max="{{ (int)($pendingFees ?? 0) }}" 
                           step="1"
                           oninput="onPhonePeAmountChange(this.value)"
                           placeholder="Enter amount">
                </div>
                <span class="phonepe-input-hint">You can pay the full amount or a partial installment.</span>
            </div>

            <!-- Quick Amount Chips -->
            @php
                $pFees = (int)($pendingFees ?? 0);
            @endphp
            <div class="phonepe-chips-row">
                <button type="button" class="phonepe-chip active" onclick="setPhonePeAmount({{ $pFees }}, this)">
                    Full Due (₹{{ number_format($pFees, 0) }})
                </button>
                @if($pFees > 1000)
                    <button type="button" class="phonepe-chip" onclick="setPhonePeAmount(1000, this)">
                        ₹1,000
                    </button>
                @endif
                @if($pFees > 2500)
                    <button type="button" class="phonepe-chip" onclick="setPhonePeAmount(2500, this)">
                        ₹2,500
                    </button>
                @endif
                @if($pFees > 5000)
                    <button type="button" class="phonepe-chip" onclick="setPhonePeAmount(5000, this)">
                        ₹5,000
                    </button>
                @endif
            </div>

            <!-- Error Banner -->
            <div id="phonepeModalError" class="phonepe-alert-error" style="display: none;"></div>

            <!-- Payment Methods Info -->
            <div class="phonepe-trust-strip">
                <div class="trust-icons">
                    <span class="pay-method-badge"><i class="fa fa-qrcode"></i> UPI (PhonePe, GPay, Paytm)</span>
                    <span class="pay-method-badge"><i class="fa fa-credit-card"></i> Cards</span>
                    <span class="pay-method-badge"><i class="fa fa-bank"></i> NetBanking</span>
                </div>
                <div class="security-text">
                    <i class="fa fa-lock text-success"></i> Instant Fee Receipt &bull; 100% Encrypted &amp; Secure
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="phonepe-modal-footer">
            <button type="button" class="phonepe-btn-cancel" onclick="closePhonePeModal()">
                Cancel
            </button>
            <button type="button" class="phonepe-btn-pay" id="phonepeProceedBtn" onclick="submitPhonePePayment()">
                <i class="fa fa-lock"></i> Proceed to Pay <span id="phonepeBtnAmount">₹{{ number_format($pFees, 0) }}</span>
            </button>
        </div>
    </div>
</div>

<style>
/* PhonePe Modal Styles */
.phonepe-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: phonepeFadeIn 0.2s ease-out;
}
@keyframes phonepeFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.phonepe-modal-container {
    background: #ffffff;
    width: 100%;
    max-width: 440px;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 44, 84, 0.25);
    overflow: hidden;
    animation: phonepeScaleUp 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #e2e8f0;
}
@keyframes phonepeScaleUp {
    from { transform: scale(0.92); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.phonepe-modal-header {
    background: linear-gradient(135deg, #002C54 0%, #001f3d 100%);
    color: #ffffff;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.phonepe-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.phonepe-logo-icon {
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, #5f259f 0%, #3d126d 100%);
    color: #fde047;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 10px rgba(95, 37, 159, 0.35);
}
.phonepe-modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    line-height: 1.2;
}
.phonepe-modal-subtitle {
    font-size: 11px;
    color: #94a3b8;
}
.phonepe-close-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: #ffffff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}
.phonepe-close-btn:hover {
    background: rgba(255, 255, 255, 0.25);
}
.phonepe-modal-body {
    padding: 20px;
}
.phonepe-student-strip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    display: flex;
    justify-content: space-between;
    margin-bottom: 14px;
}
.strip-label {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.5px;
}
.strip-val {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
}
.phonepe-due-box {
    background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
    border: 1px solid #fecdd3;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
.due-box-left {
    display: flex;
    flex-direction: column;
}
.due-label {
    font-size: 11px;
    font-weight: 600;
    color: #e11d48;
}
.due-amount {
    font-size: 22px;
    font-weight: 800;
    color: #be123c;
    line-height: 1.2;
    margin-top: 2px;
}
.due-box-badge {
    background: #ffffff;
    color: #e11d48;
    border: 1px solid #fda4af;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
}
.phonepe-input-group {
    margin-bottom: 12px;
}
.phonepe-input-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
}
.phonepe-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.currency-prefix {
    position: absolute;
    left: 14px;
    font-size: 18px;
    font-weight: 700;
    color: #64748b;
}
.phonepe-amount-input {
    width: 100%;
    height: 48px;
    padding: 0 14px 0 34px;
    border: 2px solid #cbd5e1;
    border-radius: 10px;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    outline: none;
    transition: all 0.15s ease;
}
.phonepe-amount-input:focus {
    border-color: #5f259f;
    box-shadow: 0 0 0 3px rgba(95, 37, 159, 0.15);
}
.phonepe-input-hint {
    display: block;
    font-size: 10.5px;
    color: #64748b;
    margin-top: 4px;
}
.phonepe-chips-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 16px;
}
.phonepe-chip {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.12s ease;
}
.phonepe-chip:hover {
    background: #e2e8f0;
}
.phonepe-chip.active {
    background: #5f259f;
    border-color: #5f259f;
    color: #ffffff;
}
.phonepe-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-size: 12px;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 14px;
    font-weight: 600;
}
.phonepe-trust-strip {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 10px 12px;
    text-align: center;
}
.trust-icons {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    justify-content: center;
    margin-bottom: 6px;
}
.pay-method-badge {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    color: #475569;
    padding: 2px 6px;
}
.security-text {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
}
.phonepe-modal-footer {
    padding: 14px 20px 18px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 10px;
}
.phonepe-btn-cancel {
    flex: 1;
    height: 44px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}
.phonepe-btn-cancel:hover {
    background: #f1f5f9;
}
.phonepe-btn-pay {
    flex: 2;
    height: 44px;
    background: linear-gradient(135deg, #5f259f 0%, #4a1c7d 100%);
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(95, 37, 159, 0.28);
    transition: all 0.15s ease;
}
.phonepe-btn-pay:hover {
    background: linear-gradient(135deg, #531f8c 0%, #3e166a 100%);
    transform: translateY(-1px);
}
.phonepe-btn-pay:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}
</style>

<script>
var maxDueFees = {{ (float)($pendingFees ?? 0) }};

function openPhonePeModal(due) {
    if (due !== undefined) {
        maxDueFees = parseFloat(due);
    }
    var modal = document.getElementById('phonePePayModal');
    var input = document.getElementById('phonepePayAmountInput');
    var err = document.getElementById('phonepeModalError');
    if (err) err.style.display = 'none';

    if (input) {
        input.value = Math.round(maxDueFees);
        input.max = Math.round(maxDueFees);
        onPhonePeAmountChange(input.value);
    }
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closePhonePeModal() {
    var modal = document.getElementById('phonePePayModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function onPhonePeAmountChange(val) {
    var amt = parseFloat(val) || 0;
    var btnAmt = document.getElementById('phonepeBtnAmount');
    if (btnAmt) {
        btnAmt.innerText = '₹' + Math.round(amt).toLocaleString('en-IN');
    }
    // Update chip active classes
    var chips = document.querySelectorAll('.phonepe-chip');
    chips.forEach(function(c) { c.classList.remove('active'); });
}

function setPhonePeAmount(val, btn) {
    var input = document.getElementById('phonepePayAmountInput');
    if (input) {
        input.value = val;
        onPhonePeAmountChange(val);
    }
    var chips = document.querySelectorAll('.phonepe-chip');
    chips.forEach(function(c) { c.classList.remove('active'); });
    if (btn) btn.classList.add('active');
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

// Close modal when clicking on overlay background
document.addEventListener('click', function(e) {
    var modal = document.getElementById('phonePePayModal');
    if (e.target === modal) {
        closePhonePeModal();
    }
});
</script>
