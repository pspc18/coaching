@php
$getUser=Helper::getUser();
 $notificationCount = DB::table('notifications')
    ->where('admission_id', Session::get('id'))
    ->where('show_status', 1)
    ->count();
@endphp
<!-- /.content-wrapper -->

<footer class="main-footer Display_none_mobile">
    
    <strong>{{ __('common.Copyright') }} &copy; 2014-{{ date('Y') }} <a target="blank" href="http://rukmanisoftware.com/">{{ __('common.Rukmani Software') }}</a>.</strong> {{ __('common.All rights reserved.') }}
    <div class="float-right d-none d-sm-inline-block"><b>{{ __('common.Version') }}</b> 6.1.0</div>
    
</footer>

<div class="Display_none_PC">
<footer class="main_mobile_footer">
    <div class="flex_footer_items">
        <ul>
            <a href="{{ url('/') }}">
                <div class="centerd_text_icon">
                <li class="{{ url('minidashboard') == URL::current() ? 'flex_footer_item_li_active' : "" }}">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </li>
                 </div>
                <p>Back</p>
            </a>
           <a href="#" id="refresh-button" onclick="refreshPage(event)">
    <div class="centerd_text_icon">
        <li>
            <i class="fa fa-refresh"></i>
        </li>
    </div>
    <p>Refresh</p>
</a>

<div id="refresh-animation" class="refresh-animation" style="display:none;">
    <div class="big-circle"></div>
</div>
            <!--<a href="{{url('clear-cache')}}">-->
            <!--    <div class="centerd_text_icon">-->
            <!--    <li>-->
            <!--        <i class="fa fa-recycle"></i>-->
            <!--    </li>-->
            <!--    </div>-->
            <!--        <p>Clear</p>-->
            <!--</a>-->
            <!--<a href="{{url('logout')}}">-->
            <!--    <div class="centerd_text_icon">-->
            <!--    <li class="bg-danger">-->
            <!--        <i class="fa fa-sign-out"></i>-->
            <!--    </li>-->
            <!--    </div>-->
            <!--        <p>Log Out</p>-->
            <!--</a>-->
            
            @php
            $url = "profile/edit/".Session::get('id');
            @endphp
            <a href="{{ url($url) }}">
                <div class="centerd_text_icon">
                <li class="{{ url($url)  == URL::current() ? 'flex_footer_item_li_active' : "" }}">
                    <i class="fa fa-user"></i>
                </li>
                </div>
               <p>Profile</p>
            </a>
            
              <a class="notificationModal" href="{{url('notification_fatch')}}" role="button">
                  <div class="centerd_text_icon">
                    <div class="ms-auto d-flex align-items-center text-white mr-2" style="font-size:20px; position: relative;">
                        <li>
                        <i class="fa fa-bell" aria-hidden="true"></i>
                        @if($notificationCount > 0)
                            <span style="position: absolute; top: -5px; right: -8px; background: red; color: white; border-radius: 50%; padding: 0px 4px; font-size: 12px;">
                                {{ $notificationCount }}
                            </span>
                        @endif
                        </i>
                        </li>
                    </div>
                    </div>
                     <p>Alerts</p>
                </a>
             
            <a href="#" onclick="confirmLogout(event)">
                <div class="centerd_text_icon">
                    <li class="bg-danger">
                        <i class="fa fa-sign-out"></i>
                    </li>
                </div>
                <p>Log Out</p>
            </a>
        </ul>
    </div>
</footer>
    </div>
<!-- Control Sidebar -->
<div id="logout-confirmation" class="confirmation-popup" style="display:none;">
    <div class="popup-content">
        <h3>Confirm Logout</h3>
        <p>Are you sure you want to log out?</p>
        <button onclick="logout()" class="btn">Yes</button>
        <button onclick="closePopup()" class="btn">No</button>
    </div>
</div>
<style>
      @media screen and (max-width:600px) {
      .main_mobile_footer ul li{
       box-shadow: 0 1px 3px rgba(0, 0, 0, .12), 0 1px 2px rgba(0, 0, 0, .24) !important;
       color: black;
       background-color: #f8f8ff;
      }
      .confirmation-popup {
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    }
    
    .popup-content {
    background-color: white;
    padding: 20px;
   
    max-width: 600px;
    text-align: center;
    }
    #logout-confirmation .btn{
        box-shadow:2px 2px 2px black;
        margin: 10px;
    }
    .refresh-animation {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999; /* Ensure it appears above other content */
    }
    
    .big-circle {
        width: 100px; 
        height: 100px; 
        border: 10px dashed black; 
        border-top: 10px solid transparent; /* Top transparent for spinning effect */
        border-radius: 50%;
        animation: rotate 0.6s linear infinite; /* Continuous rotation */
    }
    
    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    }
</style>
<script>
    function confirmLogout(event) {
    event.preventDefault();
    document.getElementById('logout-confirmation').style.display = 'flex';
}

function closePopup() {
    document.getElementById('logout-confirmation').style.display = 'none';
}

function logout() {
    window.location.href = "{{url('logout')}}"; 
}

</script>
<script>
    function refreshPage(event) {
    event.preventDefault(); 
    const animation = document.getElementById('refresh-animation');
    animation.style.display = 'flex';

   
    setTimeout(() => {
        animation.style.display = 'none';
        location.reload(); 
    }, 1000); 
}

</script>
<aside class="control-sidebar control-sidebar-dark"></aside>
