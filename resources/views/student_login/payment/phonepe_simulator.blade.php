<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhonePe UAT Sandbox Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f4f5f8;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .simulator-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(95, 37, 159, 0.12);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header-bar {
            background: linear-gradient(135deg, #5f259f 0%, #3d126d 100%);
            color: #ffffff;
            padding: 20px 24px;
            text-align: center;
            position: relative;
        }
        .sandbox-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #fde047;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .phonepe-brand {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .phonepe-brand span { color: #fde047; }
        .merchant-sub {
            font-size: 13px;
            opacity: 0.9;
            margin-top: 4px;
        }
        .card-body {
            padding: 24px;
        }
        .amount-display {
            text-align: center;
            padding: 16px 0 20px 0;
            border-bottom: 1px dashed #cbd5e1;
        }
        .amount-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .amount-value {
            font-size: 34px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }
        .details-list {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }
        .detail-row .label { color: #64748b; font-weight: 500; }
        .detail-row .val { color: #0f172a; font-weight: 700; text-align: right; }
        .methods-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 20px;
        }
        .methods-title {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .payment-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            border: 1.5px solid #5f259f;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }
        .pill-radio {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #5f259f;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pill-radio::after {
            content: '';
            width: 6px;
            height: 6px;
            background: #fff;
            border-radius: 50%;
        }
        .btn-pay-success {
            width: 100%;
            background: #5f259f;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(95, 37, 159, 0.25);
        }
        .btn-pay-success:hover {
            background: #4a1c7d;
            transform: translateY(-1px);
        }
        .btn-pay-fail {
            width: 100%;
            background: transparent;
            color: #ef4444;
            font-size: 13px;
            font-weight: 600;
            padding: 10px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.2s ease;
        }
        .btn-pay-fail:hover {
            background: #fee2e2;
        }
        .trust-badge {
            text-align: center;
            margin-top: 16px;
            font-size: 11px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
    </style>
</head>
<body>

<div class="simulator-card">
    <div class="header-bar">
        <div class="sandbox-badge"><i class="fa fa-flask"></i> UAT Sandbox Simulator</div>
        <div class="phonepe-brand">
            <i class="fa fa-shield"></i> Phone<span>Pe</span> PG
        </div>
        <div class="merchant-sub">Arise Education Point &bull; School Fees</div>
    </div>

    <div class="card-body">
        <div class="amount-display">
            <div class="amount-label">Amount Payable</div>
            <div class="amount-value">₹ {{ number_format($order->amount, 2) }}</div>
        </div>

        <div class="details-list">
            <div class="detail-row">
                <span class="label">Order ID:</span>
                <span class="val">{{ $order->merchant_order_id }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Student Name:</span>
                <span class="val">{{ strtoupper(trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))) }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Admission No:</span>
                <span class="val">{{ $student->admissionNo ?? '---' }}</span>
            </div>
        </div>

        <div class="methods-box">
            <div class="methods-title"><i class="fa fa-check-circle text-success"></i> Selected Payment Instrument</div>
            <div class="payment-pill">
                <div class="pill-radio"></div>
                <span>PhonePe UPI / QR / NetBanking</span>
            </div>
        </div>

        <form action="{{ route('student.payment.phonepe.callback') }}" method="POST">
            @csrf
            <input type="hidden" name="merchant_order_id" value="{{ $order->merchant_order_id }}">
            <input type="hidden" name="simulation_action" value="success">
            <button type="submit" class="btn-pay-success">
                <i class="fa fa-lock"></i> Pay ₹ {{ number_format($order->amount, 2) }} (Simulate Success)
            </button>
        </form>

        <form action="{{ route('student.payment.phonepe.callback') }}" method="POST">
            @csrf
            <input type="hidden" name="merchant_order_id" value="{{ $order->merchant_order_id }}">
            <input type="hidden" name="simulation_action" value="fail">
            <button type="submit" class="btn-pay-fail">
                <i class="fa fa-times-circle"></i> Cancel / Simulate Payment Failure
            </button>
        </form>

        <div class="trust-badge">
            <i class="fa fa-lock"></i> 256-Bit SSL Encrypted Sandbox Testing Environment
        </div>
    </div>
</div>

</body>
</html>
