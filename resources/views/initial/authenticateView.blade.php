<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>

@if(!empty($data))

    <section id="softwareExpiredSection" class="border-glow-animation">
@php
    $response = $data['response']['data'] ?? null;
    $amcDate  = $response['amc_date'] ?? ($data['amc_date'] ?? null);
    $status   = $response['status'] ?? null;
    $expired  = $amcDate && $amcDate < date('Y-m-d');
 
@endphp
       
        <div>
            <img class="connectionimg" src="{{ asset('public/images/default/software_expired.jpg') }}">
        
                @if($status === 0)
                    <h2>🔐 Account Inactive!</h2>
                    <p>Your account is currently inactive. To resume uninterrupted access, a quick review or renewal may be required. Please contact your provider or administration for immediate assistance and reactivation.</p>
                @endif
                @if($expired === true)
                    <h2>⏳ Validity Expired!</h2>
                    <p>Your service validity has ended. Renewal takes less than a minute and restores your access instantly. To restore full access immediately, please renew your plan or contact your provider for assistance.</p>
                @endif
          
            
            <br>
            <a href="{{ URL::current() }}" class="refresh-button">Refresh</a>&nbsp; &nbsp;
            <!--<a class="refresh-button autoPayment" onclick="autoPayment()">Payment</a>&nbsp; &nbsp;-->
            <a class="refresh-button" href="{{ url('helpAndUpdate') }}" target="blank">Support</a>
        </div>
     
        @if(isset($data['Initial Error']))
            <div class="error-title">
                <h2> {{ $data['Initial Error'] }}</h2>
            </div>
        @endif
        <div>
        @if($status === 0 || $expired === true)
        @else
        <h1 class="text-left"><pre style="background:#111; color:#0f0; padding:15px; font-size:14px;">{{ json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></h1>
        @endif
        </div>
    </section>
    
    
@endif

<style>
.border-glow-animation {
    background-color: #6639b500 !important;
    border-color: #ff5722 !important;
    animation: borderGlow 2s infinite;
}

.refresh-button {
    display: inline-block;
    padding: 12px 24px;
    background-color: #5d00ff;
    color: #fff;
    border: 1px solid transparent;
    border-color: #5d00ff;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-decoration: none;
}

.refresh-button:hover {
    color: #ff5722;
    background-color: #6639b500;
    border-color: #ff5722;
    animation: borderGlow 2s infinite;
}

.connectionimg {
    max-width: 400px;
    margin-top: 20px;
}

section {
    width: 100%;
    height: 100%;
    align-items: center;
    text-align: center;
    justify-content: center;
    overflow: auto;
}

.text-left{
    text-align:left;
}

@keyframes borderGlow {
    0% {
        box-shadow: 0 0 5px #ff0000, 0 0 10px #ff0000;
    }
    25% {
        box-shadow: 0 0 5px #ff9900, 0 0 10px #ff9900;
    }
    50% {
        box-shadow: 0 0 5px #33cc33, 0 0 10px #33cc33;
    }
    75% {
        box-shadow: 0 0 5px #3399ff, 0 0 10px #3399ff;
    }
    100% {
        box-shadow: 0 0 5px #ff33cc, 0 0 10px #ff33cc;
    }
}

@keyframes borderInsideGlow {
    0% {
        box-shadow: inset 0 0 5px #ff0000, inset 0 0 10px #ff0000;
    }
    25% {
        box-shadow: inset 0 0 5px #ff9900, inset 0 0 10px #ff9900;
    }
    50% {
        box-shadow: inset 0 0 5px #33cc33, inset 0 0 10px #33cc33;
    }
    75% {
        box-shadow: inset 0 0 5px #3399ff, inset 0 0 10px #3399ff;
    }
    100% {
        box-shadow: inset 0 0 5px #ff33cc, inset 0 0 10px #ff33cc;
    }
}
</style>

<script>
function autoPayment(){
    $('.autoPayment').html('Auto Payment is Coming Soon!');
    setTimeout(function(){
        $('.autoPayment').html('Payment');
    }, 4000);
} 
</script>