@php

    $uiPopup = array_merge(
        config('initialConfig', []),
        session('saas_cfg', []) 
    );
@endphp


<script>

$(document).ready(function(){
    

(async function () {

window.SAAS_CFG = @json($uiPopup);

const cfg = window.SAAS_CFG || {};

  /* 🔐 AMC CONTROL */
//   if (cfg.amc?.enabled && ['critical','grace','warning'].includes(cfg.amc.state)) {
//       showBanner(`<div><marquee> ⚠️ Software Is Expiring Soon @if(!empty(Session::get('amc_date'))) [{{ date('d-m-Y', strtotime(Session::get('amc_date'))) }}] @endif - Renew Your AMC<marquee> </div>`, "alert-info");
//       //$('#paymentReminderModal').modal('toggle');
//   }
    @if(!in_array(Session::get('role_id'), [2, 3]))
    if (cfg.amc?.enabled && ['critical','grace','warning'].includes(cfg.amc.state)) {
        showBanner(`
            <div class="mq-wrap amc-alert-wrap">
                <div class="mq amc-alert">
                    🚨🚨 <strong>URGENT ALERT!</strong> 🚨🚨
                    <span class="blink-text">
                        Your Software Support & Services Are Expiring 
                        @if(!empty(Session::get('amc_date')))
                            [ {{ date('d-m-Y', strtotime(Session::get('amc_date'))) }} ]
                        @endif
                        — Renew AMC Immediately To Avoid Service Interruption!
                    </span>
                </div>
            </div>
        `, "alert-danger");
    }
    @endif

  if (cfg.amc?.state === 'locked') {
    document.body.innerHTML =
      `<h1 style="text-align:center">🔒 Software Locked<br>Contact Support</h1>`;
  }

  /* ⭐ REVIEW CONTROL */
  if (cfg.review?.enabled && new Date().getDate() === cfg.review.day) {
    //$('#reviewModal').modal('toggle');
  }

  /* 📢 ANNOUNCEMENT */
  if (cfg.announcement?.enabled) {
    //showBanner(cfg.announcement.message);
  }
  
  if (cfg.instant?.enabled) {
      if(cfg.instant.type == 'header'){
          showBanner(cfg.instant.title + ' : ' + cfg.instant.message, "alert-warning");
      }else if(cfg.instant.type == 'small'){
          $('#smallModal').modal('toggle');
          $('#small_instant_title').html(cfg.instant.title);
          $('#small_instant_message').html(cfg.instant.message);
      }else if(cfg.instant.type == 'fullscreen'){
          $('#fullscreenModal').modal('toggle');
          $('#fullscreen_instant_title').html(cfg.instant.title);
          $('#fullscreen_instant_message').html(cfg.instant.message);
      }else{}

      if(cfg.instant.type == 'small' || cfg.instant.type == 'fullscreen'){
        document.addEventListener('contextmenu', e => e.preventDefault());

        document.addEventListener('keydown', function (e) {

            // F12
            if (e.keyCode === 123) {
                e.preventDefault();
                return false;
            }

            // Ctrl + Shift + I / J / C
            if (e.ctrlKey && e.shiftKey && ['I','J','C'].includes(e.key.toUpperCase())) {
                e.preventDefault();
                return false;
            }

            // Ctrl + U (View Source)
            if (e.ctrlKey && e.key.toUpperCase() === 'U') {
                e.preventDefault();
                return false;
            }
        });
      }
    
  }


})();

function showModal(id) {
  document.getElementById(id)?.classList.add('show');
}

// function showBanner(msg, cssclass) {
//     const b = document.createElement('div');
//     b.className = `alert ${cssclass} text-center mb-0`;
//     b.innerHTML = msg;
//     document.body.prepend(b);
// }

function showBanner(msg, cls){
  document.body.insertAdjacentHTML('afterbegin', `<div class="alert ${cls} text-center mb-0">${msg}</div>`);
}

});
</script>





    <div class="modal" id="smallModal" data-bs-backdrop="false">
      <div class="modal-dialog" >
        <div class="modal-content" >
    
          <div class="modal-header">
            <h4 class="modal-title">👋 Hello, Valuable User!</h4>
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
          </div>
    
          <div class="modal-body text-center">
                <h3 id="small_instant_title"></h3>
                <p id="small_instant_message"></p>
            
            <a href="{{ url('helpAndUpdate') }}" class="btn btn-primary">📲 Contact Support!</a>
          </div>
    
          <div class="modal-footer">
            <a href="{{ url('/') }}" class="btn btn-warning " >Refresh</a>
          </div>
    
        </div>
      </div>
    </div>

        <div class="modal" id="fullscreenModal" data-bs-backdrop="false">
          <div class="modal-dialog modal-fullscreen" >
            <div class="modal-content border-glow-animation" >
        
              <div class="modal-header">
                <h4 class="modal-title">👋 Hello, Valuable User!</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
              </div>
        
              <div class="modal-body text-center border-glow-animation">
        
                
                
                <div class="row mt-2">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        
                        <img class="connectionimg" src="{{ asset('public/images/default/software_expired.jpg') }}">
                        <br><br>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <h3 id="fullscreen_instant_title"></h3>
                <p id="fullscreen_instant_message"></p>
            
                <a href="{{ url('helpAndUpdate') }}" class="btn btn-primary">📲 Contact Support!</a>
            
              </div>
        
              <div class="modal-footer">
                 <a href="{{ url('/') }}" class="btn btn-warning " >Refresh</a>
              </div>
        
            </div>
          </div>
        </div>







<style>
#paymentReminderModal{
    background-color: white;
}
.modal-fullscreen {
    width: 100vw;
    max-width: none;
    height: 100%;
    margin: 0;
}
.modal-fullscreen .modal-content {
    height: 100%;
    border: 0;
    border-radius: 0;
}
.border-glow-animation {
    border-color: #ff5722 !important;
    animation: borderWarningDangerGlow 2s infinite;
}
.refresh-button:hover {
    color: #ff5722;
    background-color: #6639b500;
    border-color: #ff5722;
    animation: borderGlow 2s infinite;
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
@keyframes borderWarningDangerGlow {
    0% {
        box-shadow: inset 0 0 5px #ff0000, inset 0 0 10px #ff0000; /* Danger */
    }
    25% {
        box-shadow: inset 0 0 5px #ffcc00, inset 0 0 10px #ffcc00; /* Warning */
    }
    50% {
        box-shadow: inset 0 0 5px #ff9900, inset 0 0 10px #ff9900; /* Warning */
    }
    75% {
        box-shadow: inset 0 0 5px #ffcc00, inset 0 0 10px #ffcc00; /* Warning */
    }
    100% {
        box-shadow: inset 0 0 5px #ff0000, inset 0 0 10px #ff0000; /* Danger */
    }
}



.connectionimg {
    max-width: 300px;
}

/*.mq-wrap{overflow:hidden}*/
/*.mq{white-space:nowrap;display:inline-block;padding-left:100%;animation:m 30s linear infinite}*/
/*@keyframes m{to{transform:translateX(-100%)}}*/


.amc-alert-wrap{
    background: linear-gradient(90deg,#8b0000,#ff0000,#8b0000);
    border: 3px solid #fff;
    box-shadow: 0 0 20px rgba(255,0,0,0.8);
    animation: shake 0.8s infinite;
}

.amc-alert{
    color:#fff;
    font-size:20px;
    font-weight:800;
    padding:12px;
    text-transform:uppercase;
    letter-spacing:1px;
}

.blink-text{
    animation: blink 1s infinite;
}

@keyframes blink{
    0%{opacity:1;}
    50%{opacity:0.2;}
    100%{opacity:1;}
}

@keyframes shake{
    0%{transform:translateX(0);}
    25%{transform:translateX(-2px);}
    50%{transform:translateX(2px);}
    75%{transform:translateX(-2px);}
    100%{transform:translateX(0);}
}

</style>