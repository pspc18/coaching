@php
$getSetting = Helper::getSetting();
$getstudentbirthday = Helper::getstudentbirthday();
$getUsersBirthday = Helper::getUsersBirthday();
$getUser=Helper::getUser();
$getSession=Helper::getSession();
$getAllBranch = Helper::getAllBranch();
$roleName = DB::table('role')->whereNull('deleted_at')->find(Session::get('role_id'));
$data = Session::all();
$userNotificationUnreadCount = 0;
$headerUserNotifications = collect();
if ((int) Session::get('role_id') !== 3 && !empty(Session::get('id'))) {
    $userNotificationQuery = DB::table('notifications')
        ->where('user_id', (int) Session::get('id'))
        ->where('branch_id', (int) Session::get('branch_id'))
        ->where('session_id', (int) Session::get('session_id'))
        ->where('show_status', 1)
        ->whereNull('deleted_at');
    $userNotificationUnreadCount = (clone $userNotificationQuery)->where('message_seen', 0)->count();
    $headerUserNotifications = (clone $userNotificationQuery)->orderByDesc('id')->take(4)->get();
}

// User Avatar & Initial computation
$headerUserName = trim((string) (Session::get('first_name') ?: ($getUser['first_name'] ?? 'Admin')));
$headerUserInitial = !empty($headerUserName) ? mb_strtoupper(mb_substr($headerUserName, 0, 1)) : 'A';
$headerUserImageName = !empty($getUser['image']) ? $getUser['image'] : null;
$headerHasProfileImage = false;
$headerProfileImageSrc = '';

if (!empty($headerUserImageName)) {
    if (filter_var($headerUserImageName, FILTER_VALIDATE_URL)) {
        $headerHasProfileImage = true;
        $headerProfileImageSrc = $headerUserImageName;
    } else {
        $uploadPath = env('IMAGE_UPLOAD_PATH') ? rtrim(env('IMAGE_UPLOAD_PATH'), '/\\') . '/profile/' . $headerUserImageName : null;
        $publicPath = public_path('schoolimage/profile/' . $headerUserImageName);
        if (($uploadPath && file_exists($uploadPath)) || file_exists($publicPath)) {
            $headerHasProfileImage = true;
            $headerProfileImageSrc = rtrim(env('IMAGE_SHOW_PATH'), '/') . '/profile/' . $headerUserImageName;
        }
    }
}
@endphp
@php
    $apkData = DB::table('settings')->first();
@endphp
<style>
  @media screen and (min-width:600px) {
      .student-mobile{
          display:none;
      }
      .desktop-student{
          display:inline !important;
      }
  }

  /* ==========================================================================
     Birthday Pill Badge with Animated Celebration Icon (Sharp Edges)
     ========================================================================== */
  .header-birthday-pill {
      display: inline-flex !important;
      align-items: center !important;
      gap: 6px !important;
      height: 32px !important;
      padding: 0 10px 0 6px !important;
      background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%) !important;
      border: 1px solid #fecdd3 !important;
      border-radius: 2px !important;
      color: #e11d48 !important;
      font-weight: 700 !important;
      font-size: 11.5px !important;
      box-shadow: 0 1px 2px rgba(225, 29, 72, 0.12) !important;
      transition: all 0.2s ease !important;
      text-decoration: none !important;
      cursor: pointer;
      line-height: 1;
      margin: 0 !important;
  }
  .header-birthday-pill:hover {
      background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%) !important;
      box-shadow: 0 2px 5px rgba(225, 29, 72, 0.22) !important;
      color: #be123c !important;
      text-decoration: none !important;
  }
  .header-birthday-pill .bday-icon-bubble {
      width: 22px;
      height: 22px;
      border-radius: 2px;
      background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      box-shadow: 0 1px 2px rgba(225, 29, 72, 0.25);
      animation: bdayWiggle 2.8s ease-in-out infinite;
  }
  .header-birthday-pill .bday-text {
      font-weight: 700;
      letter-spacing: .02em;
  }
  .header-birthday-pill .bday-count-badge {
      background: #e11d48;
      color: #ffffff;
      font-size: 9.5px;
      font-weight: 800;
      padding: 1px 5px;
      border-radius: 2px;
      min-width: 16px;
      text-align: center;
      line-height: 1.2;
  }
  @keyframes bdayWiggle {
      0%, 100% { transform: rotate(0deg); }
      10% { transform: rotate(14deg) scale(1.1); }
      20% { transform: rotate(-12deg) scale(1.1); }
      30% { transform: rotate(10deg); }
      40% { transform: rotate(-6deg); }
      50% { transform: rotate(0deg); }
  }

  /* ==========================================================================
     Profile Initial Avatar (Fallback when profile image is missing/broken)
     ========================================================================== */
  .user-avatar-initial {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #002C54 0%, #0a4d8c 100%);
      color: #ffffff !important;
      font-weight: 700;
      font-size: 14px;
      line-height: 1;
      text-transform: uppercase;
      user-select: none;
      letter-spacing: 0.5px;
  }
  .dropdown-user-avatar {
      width: 54px;
      height: 54px;
      border-radius: 2px;
      margin: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #002C54 0%, #0a4d8c 100%);
      color: #ffffff !important;
      font-weight: 800;
      font-size: 22px;
      line-height: 1;
      text-transform: uppercase;
      user-select: none;
      border: 1px solid #001f3d;
      box-shadow: 0 2px 4px rgba(0, 44, 84, 0.2);
  }

  /* ==========================================================================
     Header Quick Student Live Search (Desktop View)
     ========================================================================== */
  .header-quick-search-wrapper {
      flex: 1 1 auto;
      max-width: 480px;
      min-width: 260px;
      margin: 0 16px;
      position: relative;
      z-index: 1040;
  }
  .header-quick-search-box {
      width: 100%;
      position: relative;
  }
  .quick-search-input-group {
      display: flex;
      align-items: center;
      position: relative;
      width: 100%;
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      border-radius: 20px;
      transition: all 0.2s ease;
      height: 34px;
      padding: 0 10px 0 12px;
  }
  .quick-search-input-group:focus-within {
      background: #ffffff;
      border-color: #002C54;
      box-shadow: 0 0 0 3px rgba(0, 44, 84, 0.12);
  }
  .quick-search-icon {
      color: #64748b;
      font-size: 13px;
      margin-right: 6px;
      flex-shrink: 0;
  }
  .quick-search-input {
      flex: 1;
      border: none;
      background: transparent;
      outline: none;
      font-size: 12.5px;
      color: #0f172a;
      padding: 4px 6px;
      min-width: 0;
  }
  .quick-search-input::placeholder {
      font-size: 11.5px;
      color: #94a3b8;
  }
  .quick-search-spinner {
      color: #002C54;
      font-size: 13px;
      margin-left: 6px;
      flex-shrink: 0;
  }
  .quick-search-clear {
      background: transparent;
      border: none;
      font-size: 18px;
      line-height: 1;
      color: #94a3b8;
      cursor: pointer;
      padding: 0 4px;
      margin-left: 4px;
      outline: none;
      transition: color 0.15s ease;
  }
  .quick-search-clear:hover {
      color: #dc2626;
  }
  .quick-search-shortcut {
      margin-left: 6px;
      flex-shrink: 0;
      user-select: none;
  }
  .quick-search-shortcut kbd {
      background: #e2e8f0;
      border: 1px solid #cbd5e1;
      border-radius: 3px;
      font-size: 10px;
      font-family: inherit;
      padding: 1px 5px;
      color: #475569;
  }
  .header-quick-search-dropdown {
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      width: 580px;
      max-width: 90vw;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      box-shadow: 0 18px 40px -6px rgba(0, 20, 40, 0.25), 0 4px 12px rgba(0, 0, 0, 0.08);
      z-index: 9999;
      overflow: hidden;
  }
  .quick-search-dropdown-header {
      padding: 8px 12px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      font-size: 11px;
      font-weight: 600;
      color: #475569;
  }
  .quick-search-dropdown-header kbd {
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 2px;
      padding: 0 4px;
      font-size: 9.5px;
      color: #64748b;
  }
  .quick-search-results-list {
      max-height: 380px;
      overflow-y: auto;
      overscroll-behavior: contain;
  }
  .quick-student-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 9px 12px;
      border-bottom: 1px solid #f1f5f9;
      transition: background 0.15s ease;
      text-decoration: none !important;
      color: inherit;
      position: relative;
  }
  .quick-student-item:last-child {
      border-bottom: none;
  }
  .quick-student-item:hover,
  .quick-student-item.selected {
      background: #f0f7ff;
  }
  .quick-student-item.selected::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 3px;
      background: #002C54;
  }
  .quick-student-avatar-col {
      flex-shrink: 0;
  }
  .quick-student-avatar-img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #cbd5e1;
  }
  .quick-student-avatar-initial {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
      color: #ffffff;
      font-weight: 700;
      font-size: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #001f3d;
      user-select: none;
  }
  .quick-student-info-col {
      flex: 1;
      min-width: 0;
  }
  .quick-student-top-line {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 6px;
      margin-bottom: 3px;
  }
  .quick-student-name {
      font-weight: 700;
      font-size: 13px;
      color: #0f172a;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 220px;
  }
  .quick-badge-class {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 1px 6px;
      font-size: 10px;
      font-weight: 600;
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
      border-radius: 3px;
      white-space: nowrap;
  }
  .quick-badge-adm {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      padding: 1px 6px;
      font-size: 10px;
      font-weight: 600;
      background: #f1f5f9;
      color: #475569;
      border: 1px solid #cbd5e1;
      border-radius: 3px;
      white-space: nowrap;
  }
  .quick-student-meta-line {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      font-size: 11px;
      color: #64748b;
  }
  .quick-meta-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
  }
  .quick-meta-pill b {
      color: #1e293b;
      font-weight: 600;
  }
  .quick-student-actions-col {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-shrink: 0;
  }
  .quick-action-btn {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
      font-size: 11px;
      font-weight: 600;
      border-radius: 3px;
      text-decoration: none !important;
      cursor: pointer;
      white-space: nowrap;
      transition: all 0.15s ease;
      line-height: 1.2;
  }
  .quick-action-btn.btn-collect-fees {
      background: #059669;
      color: #ffffff !important;
      border: 1px solid #059669;
  }
  .quick-action-btn.btn-collect-fees:hover {
      background: #047857;
      border-color: #047857;
      box-shadow: 0 1px 3px rgba(5, 150, 105, 0.35);
  }
  .quick-action-btn.btn-view-profile {
      background: #002C54;
      color: #ffffff !important;
      border: 1px solid #002C54;
  }
  .quick-action-btn.btn-view-profile:hover {
      background: #001f3d;
      border-color: #001f3d;
      box-shadow: 0 1px 3px rgba(0, 44, 84, 0.35);
  }
  .quick-search-dropdown-footer {
      padding: 8px 12px;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      text-align: center;
  }
  .btn-view-all-results {
      font-size: 11.5px;
      font-weight: 600;
      color: #0284c7;
      text-decoration: none !important;
      display: inline-flex;
      align-items: center;
      gap: 4px;
  }
  .btn-view-all-results:hover {
      color: #0369a1;
      text-decoration: underline !important;
  }
  .quick-search-empty-state {
      padding: 24px 16px;
      text-align: center;
      color: #64748b;
  }
  .quick-search-empty-state i {
      font-size: 26px;
      color: #94a3b8;
      margin-bottom: 8px;
      display: block;
  }
  .quick-search-empty-title {
      font-size: 13px;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 4px;
  }
  .quick-search-empty-desc {
      font-size: 11.5px;
      color: #64748b;
  }
  .quick-search-loading-state {
      padding: 20px 16px;
      text-align: center;
      color: #002C54;
      font-size: 12px;
      font-weight: 600;
  }
  .quick-search-loading-state i {
      margin-right: 6px;
  }
</style>

<!--<div class="marquee">-->
<!--    <p>{{$getSetting->name}}</p>-->
<!--</div>-->
<!-- Navbar -->

@if(Session::get('role_id') == 3)
<nav class="navbar navbar-expand-lg navbar-dark student-mobile" style="background:#008CA4;box-shadow: 5px 28px 55px 30px #008CA4;">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-2 sidebar-toggle-btn" data-widget="pushmenu"><i class="fa fa-bars"></i></button>
            <span class="navbar-brand " style="margin-left: -30px;line-height: 20px;"> <span style="color:orange;"> Hello </span> {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }} <span style="font-size:12px;">( {{$getUser['ClassTypes']['name'] ?? ''}} )</span>
            <span style="font-size:12px;display:block;">Welcome Back</span>
            </span>
          
        </div>
        <div id="custom-top-loader"></div>
    </nav>
@endif


@if(Session::get('role_id') == 3)
<div class="desktop-student d-none">
    
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link sidebar-toggle-btn" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
    </li>
  </ul>
  
  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto flex_centerd_profile">
    @php
        $queMessage = DB::table('message_queues')->whereNull('deleted_at')->where('message_status', 0)->orderBy('id','DESC')->take(3)->get();
        $queMessageCount = DB::table('message_queues')->whereNull('deleted_at')->where('message_status', 0)->count();
    @endphp
     
    @if($queMessageCount > 0)
    
    <li class="nav-item dropdown d-none">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="true">
          <i class="fa fa-whatsapp text-success"></i>
          <span class="badge badge-danger navbar-badge">{{ $queMessageCount ?? '' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right " style="left: inherit; right: 0px;">
          
        @foreach($queMessage as $quemsg)
            <a href="#" class="dropdown-item">
                <div class="media">
                <div class="media-body">
                    <h3 class="dropdown-item-title">
                    {{ $quemsg->receiver_number ?? '' }}
                    <!-- <span class="float-right text-sm text-danger"><i class="fa fa-star"></i></span> -->
                    </h3>
                    <p class="text-sm">{{ Str::limit($quemsg->content ?? '', 15, '...') }}</p>
                    <p class="text-sm text-muted"><i class="fa fa-clock mr-1"></i> 
                </p>
                </div>
                </div>
            </a>
            <div class="dropdown-divider"></div>
        @endforeach
          
          <a href="{{ url('WhatsAppMessageHistory') }}" class="dropdown-item dropdown-footer">See All Pending WhatsApp Messages</a>
        </div>
    </li>
    
    @endif

    <li class="nav-item dropdown">
      <div class="Display_none_mobile">
        <a href="{{ URL::current() }}" id="refresh" class="refresh_btn" onclick="">Refresh!</a>
      </div>
    </li>
<div id="refresh-animation" class="refresh-animation" style="display:none;">
    <div class="big-circle"></div>
</div>
    @if(!empty(Session::get('id')))
    <li class="nav-item dropdown ">
      <a class="user-panel" data-toggle="dropdown" href="#" title="{{ $headerUserName }}">
        @if($headerHasProfileImage)
          <img src="{{ $headerProfileImageSrc }}" class="img-circle elevation-2" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
          <span class="user-avatar-initial" style="display:none;">{{ $headerUserInitial }}</span>
        @else
          <span class="user-avatar-initial">{{ $headerUserInitial }}</span>
        @endif
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <div class="row border-bottom mr-0">
          <div class="col-md-4 col-4">
            @if($headerHasProfileImage)
              <img class="profile_user_img" src="{{ $headerProfileImageSrc }}" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
              <span class="dropdown-user-avatar" style="display:none;">{{ $headerUserInitial }}</span>
            @else
              <span class="dropdown-user-avatar">{{ $headerUserInitial }}</span>
            @endif
          </div>
          <div class="col-md-8 col-8 align_centerd">
            <div>
              <h4>{{ Session::get('first_name') ?? '' }}</h4>
              <p>{{ $roleName->name ?? '' }}</p>
            </div>
          </div>
        </div>

        <a href="{{ url('profile/edit') }}/{{Session::get('id') ?? '' }}" class="{{ url('profile/edit/'.Session::get('id'))  == URL::current() ? 'dropdown-item border-bottom back_active_header' : "dropdown-item border-bottom" }}">
          <i class="fa fa-user-circle mr-2"></i>Profile Setting
        </a>

        <a href="{{ url('change_password') }}" class="{{ url('change_password')  == URL::current() ? 'dropdown-item border-bottom back_active_header' : "dropdown-item border-bottom" }}">
          <i class="fa fa-key mr-2"></i>Change Password
        </a>
        

        <a href="#" class="dropdown-item border-bottom text-danger" onclick="confirmLogout(event)">
          <i class="fa fa-sign-out mr-2"></i> Log Out
        </a>

       
      </div>
    </li>
    @endif
@if(Session::get('sibling_active') == 'yes')
            <li title='Switch Accounts' class="nav-item dropdown mr-2" style='background: #7c3aed; padding: 4px 8px; border-radius: 2px;'>
                <div>
                    <a href="{{ url('siblingList') }}" class="text-white" style='display: flex; align-items: center;'>
                        <i class="fa fa-users mr-1"></i><strong style='font-size:12px'>Switch</strong>
                    </a>
                </div>
            </li> 
         @endif
  </ul>
</nav>
</div>
@endif



@if(Session::get('role_id') !== 3)
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link sidebar-toggle-btn" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
    </li>
  </ul>
  
<ul class="navbar-nav header-center-nav" id="navbar_nav">
    <li class="nav-item dropdown">
      <div class="Display_none_desktop" >
       <h4 class="first-name">{{ Session::get('first_name') ?? '' }} &nbsp &nbsp</h4>
       <div style="display: flex;align-items: first baseline;justify-content: space-evenly;">
           <h4>{{$getUser['ClassTypes']['name'] ?? ''}}</h4>
        <!--<p class="role-name">{{ $roleName->name ?? '' }}</p>-->
       </div>
      </div>
    </li>
</ul>

  <!-- Desktop Quick Live Student Search Bar -->
  <div class="header-quick-search-wrapper d-none d-md-flex">
    <div class="header-quick-search-box">
      <div class="quick-search-input-group">
        <span class="quick-search-icon"><i class="fa fa-search"></i></span>
        <input type="text" id="headerStudentSearchInput" class="quick-search-input" placeholder="Search student by Name, Father, Mobile, Adm No, Aadhar..." autocomplete="off" spellcheck="false">
        <span class="quick-search-spinner" id="headerSearchSpinner" style="display:none;"><i class="fa fa-circle-o-notch fa-spin"></i></span>
        <button type="button" class="quick-search-clear" id="headerSearchClear" style="display:none;" title="Clear search">&times;</button>
        <span class="quick-search-shortcut" title="Press Ctrl + K to search"><kbd>Ctrl+K</kbd></span>
      </div>

      <!-- Auto-suggestion dropdown popup -->
      <div class="header-quick-search-dropdown shadow-lg" id="headerSearchDropdown" style="display:none;">
        <div class="quick-search-dropdown-header d-flex align-items-center justify-content-between">
          <span class="dropdown-header-title"><i class="fa fa-users text-primary mr-1"></i> <span id="headerSearchCount">0 Students Found</span></span>
          <span class="dropdown-header-hint"><kbd>↑</kbd> <kbd>↓</kbd> navigate &nbsp; <kbd>Esc</kbd> close</span>
        </div>
        <div class="quick-search-results-list" id="headerSearchResultsList">
          <!-- Dynamically populated via AJAX -->
        </div>
        <div class="quick-search-dropdown-footer" id="headerSearchFooter" style="display:none;">
          <a href="{{ url('admissionView') }}" id="headerSearchAllLink" class="btn-view-all-results">
            <span>View all matching records in Student Admission List</span> <i class="fa fa-arrow-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto flex_centerd_profile">
  
    @php
        $totalBirthdaysCount = count($getstudentbirthday ?? []) + count($getUsersBirthday ?? []);
    @endphp
    @if(Session::get('role_id') == 1)
    @if($totalBirthdaysCount > 0)
    <li class="nav-item">
      <a class="nav-link header-birthday-pill mr-2" href="{{url('happy_birthday')}}" title="Today's Birthdays ({{ $totalBirthdaysCount }}) - Click to send wishes">
        <span class="bday-icon-bubble">
          <i class="fa fa-birthday-cake"></i>
        </span>
        <span class="bday-text d-none d-md-inline">Birthdays</span>
        <span class="bday-count-badge">{{ $totalBirthdaysCount }}</span>
      </a>
    </li>
    @endif
    @endif




    <li class="nav-item dropdown">
      <div class="Display_none_mobile">
        <form action="{{url('changeBranch')}}" method="POST">
          @csrf
          <select class="selectDesign " id="branch_id" name="branch_id" onchange="this.form.submit()">
          @if(Session::get('role_id') != 3)
           
            <!--<option value=""> All Branch</option>-->
            @foreach($getAllBranch as $branch)

            <option value="{{ $branch->id ?? ''  }} " {{ ( $branch->id == Session::get('branch_id')) ? 'selected' : '' }}>{{ $branch->branch_name ?? ''  }} </option>
            @endforeach
            @endif
          </select>
        </form>
      </div>
    </li>

    @if(Session::get('role_id') != 1 && Session::get('role_id') != 6)
    <li class="nav-item dropdown">
      <div class="Display_none_mobile">
        <select class="selectDesign " id="sessionData" name="sessionData" disabled>
          @if(!empty($getSession))
          @foreach($getSession as $type)
          <option value="{{ $type->id ?? ''  }} " {{ ( $type->id == Session::get('session_id')) ? 'selected' : '' }}>{{ $type->from_year ?? ''  }} - {{ $type->to_year ?? ''  }}</option>
          @endforeach
          @endif
        </select>
      </div>
    </li>
    @else
    <li class="nav-item dropdown">
      <div class="Display_none_mobile">
        <form action="{{url('sectionDataId')}}" method="POST">
          @csrf
          <select class="selectDesign " id="sessionData" name="sessionData" onchange="this.form.submit()">
            @if(!empty($getSession))
            @foreach($getSession as $type)
            <option value="{{ $type->id ?? ''  }} " {{ ( $type->id == Session::get('session_id')) ? 'selected' : '' }}>{{ $type->from_year ?? ''  }} - {{ $type->to_year ?? ''  }}</option>
            @endforeach
            @endif
          </select>
        </form>
      </div>
    </li>
    @endif

    <li class="nav-item dropdown header-gap-item">
      <div class="Display_none_mobile">
        <a href="{{ URL::current() }}" id="refresh" class="refresh_btn" onclick="">Refresh!</a>
      </div>
    </li>
    <li class="nav-item dropdown header-notification-bell">
      <a class="nav-link" data-toggle="dropdown" href="#" aria-label="Notifications">
        <i class="fa fa-bell-o"></i>
        @if($userNotificationUnreadCount > 0)
          <span class="badge badge-danger navbar-badge">{{ $userNotificationUnreadCount > 99 ? '99+' : $userNotificationUnreadCount }}</span>
        @endif
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right notification-dropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
          <strong>Notifications</strong>
          @if($userNotificationUnreadCount > 0)<span>{{ $userNotificationUnreadCount }} unread</span>@endif
        </div>
        <div class="dropdown-divider"></div>
        @forelse($headerUserNotifications as $headerNotification)
          <a href="{{ url('user-notifications') }}" class="dropdown-item header-notification-item {{ (int) $headerNotification->message_seen === 0 ? 'is-unread' : '' }}">
            <i class="fa {{ $headerNotification->type === 'notice' ? 'fa-bullhorn' : 'fa-bell' }} mr-2"></i>
            <span>
              <strong>{{ Str::limit($headerNotification->title ?: 'Notification', 30) }}</strong>
              <small>{{ Str::limit(trim((string) $headerNotification->content), 48) }}</small>
            </span>
          </a>
          <div class="dropdown-divider"></div>
        @empty
          <div class="text-center text-muted py-3 small">No notifications found.</div>
          <div class="dropdown-divider"></div>
        @endforelse
        <a href="{{ url('user-notifications') }}" class="dropdown-item dropdown-footer">View all notifications</a>
      </div>
    </li>
<div id="refresh-animation" class="refresh-animation" style="display:none;">
    <div class="big-circle"></div>
</div>
    @if(!empty(Session::get('id')))
    <li class="nav-item dropdown mobile_padding">
      <a class="user-panel" data-toggle="dropdown" href="#" title="{{ $headerUserName }}">
        @if($headerHasProfileImage)
          <img src="{{ $headerProfileImageSrc }}" class="img-circle elevation-2" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
          <span class="user-avatar-initial" style="display:none;">{{ $headerUserInitial }}</span>
        @else
          <span class="user-avatar-initial">{{ $headerUserInitial }}</span>
        @endif
        {{-- <span class="badge badge-warning navbar-badge">15</span> --}}
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        {{-- <span class="dropdown-item dropdown-header">15 Notifications</span> --}}
        {{-- <div class="dropdown-divider"></div> --}}
        <div class="row border-bottom mr-0">
          <div class="col-md-4 col-4">
            @if($headerHasProfileImage)
              <img class="profile_user_img" src="{{ $headerProfileImageSrc }}" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
              <span class="dropdown-user-avatar" style="display:none;">{{ $headerUserInitial }}</span>
            @else
              <span class="dropdown-user-avatar">{{ $headerUserInitial }}</span>
            @endif
          </div>
          <div class="col-md-8 col-8 align_centerd">
            <div>
              <h4>{{ Session::get('first_name') ?? '' }}</h4>
              <p>{{ $roleName->name ?? '' }}</p>
            </div>
          </div>
        </div>

        <a href="{{ url('profile/edit') }}/{{Session::get('id') ?? '' }}" class="{{ url('profile/edit/'.Session::get('id'))  == URL::current() ? 'dropdown-item border-bottom back_active_header' : "dropdown-item border-bottom" }}">
          <i class="fa fa-user-circle mr-2"></i>Profile Setting
          {{-- <span class="float-right text-muted text-sm">3 mins</span> --}}
        </a>

        <a href="{{ url('change_password') }}" class="{{ url('change_password')  == URL::current() ? 'dropdown-item border-bottom back_active_header' : "dropdown-item border-bottom" }}">
          <i class="fa fa-key mr-2"></i>Change Password
          {{-- <span class="float-right text-muted text-sm">3 mins</span> --}}
        </a>

   @if(Session::get('role_id') == 1 || Session::get('role_id') == 2)
        <div class="dropdown-item border-bottom Display_none_PC">
         
          <div class="flex_row">
            <i class="fa fa-calendar-check-o mr-2"></i>
            <form action="{{url('sectionDataId')}}" method="POST">
              @csrf
              <select class="form-control select" id="sessionData" name="sessionData" onchange="this.form.submit()">
                @if(!empty($getSession))
                @foreach($getSession as $type)
                <option value="{{ $type->id ?? ''  }} " {{ ( $type->id == Session::get('session_id')) ? 'selected' : '' }}>{{ $type->from_year ?? ''  }} - {{ $type->to_year ?? ''  }}</option>
                @endforeach
                @endif
              </select>
            </form>
          </div>
         
        </div>
 @endif
 
        <a href="#" class="dropdown-item border-bottom text-danger" onclick="confirmLogout(event)">
          <i class="fa fa-sign-out mr-2"></i> Log Out
          {{-- <span class="float-right text-muted text-sm">3 mins</span> --}}
        </a>

        {{-- <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> --}}
      </div>
    </li>
    @endif 
        
  </ul>
</nav>

@endif


<div class="modal fade" id="subModules">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <input type="text" id="find_value" name="find_value" class="form-control" placeholder="Search Modules">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body" id="sub_modules">
        No Data Found
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<style>
.preloader1{
    position: absolute;
  top: 63px;
  left: 141px;
  font-size: 75px;
}
</style>
<script>
  $(document).ready(function() {
    var BASEURL = "{{ url('/') }}";
    $(document).on('keyup', '#find_value', function() {
      var values = $(this).val();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: BASEURL + '/get_modules',
        data: {
          name: values
        },
        success: function(data) {
          if (data.length != 0) {
            // alert(JSON.stringify(data));
          } else {

          }
        }
      });
    });
  });
</script>


<script>
  function SearchValue() {

    var BASEURL = "{{ url('/') }}";
    var SearchItem = $('#SearchItem').val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      url: BASEURL + '/all_students_search',
      data: {
        name: SearchItem
      },
      success: function(data) {

        $('.students_search').html('');
        $('.students_search').html(data);

      }
    });

  }
</script>





<!-- Logout Confirmation Modal (Standard Arise ERP Modal Theme) -->
<div class="modal fade" id="logout-confirmation" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px; margin: 1.75rem auto;">
        <div class="modal-content" style="border-radius: 5px; overflow: hidden; border: 1px solid rgba(0, 44, 84, 0.18); box-shadow: 0 20px 45px -8px rgba(0, 44, 84, 0.35);">
            <div class="modal-header bg-primary text-white" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important; padding: 10px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: space-between;">
                <h5 class="modal-title font-size-14 font-weight-bold" id="logoutModalLabel" style="color: #ffffff !important; margin: 0; font-size: 13.5px !important; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa fa-sign-out"></i> Confirm Logout
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePopup()" aria-label="Close" style="background: transparent; border: none; font-size: 20px; line-height: 1; color: #ffffff; opacity: 0.85; cursor: pointer; padding: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center" style="background: #ffffff; padding: 24px 20px !important;">
                <div style="width: 52px; height: 52px; margin: 0 auto 14px auto; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-power-off text-danger" style="font-size: 24px; color: #dc2626;"></i>
                </div>
                <h6 style="font-weight: 700; color: #002C54; font-size: 15px; margin-bottom: 6px;">Ready to leave?</h6>
                <p style="color: #64748b; font-size: 12.5px; margin-bottom: 0; line-height: 1.5;">Are you sure you want to end your current session and log out?</p>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 10px 16px; display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePopup()" style="min-width: 80px; font-weight: 600; font-size: 12px; border-radius: 3px;">Cancel</button>
                <button type="button" onclick="logout()" class="btn btn-danger btn-sm" style="min-width: 96px; font-weight: 600; font-size: 12px; border-radius: 3px; background: #dc2626; border-color: #dc2626;">
                    <i class="fa fa-sign-out mr-1"></i> Yes, Log Out
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmLogout(event) {
    if (event && event.preventDefault) event.preventDefault();
    if (typeof jQuery !== 'undefined' && $('#logout-confirmation').modal) {
        $('#logout-confirmation').modal('show');
    } else {
        var modal = document.getElementById('logout-confirmation');
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'block';
            modal.setAttribute('aria-hidden', 'false');
        } else {
            if (confirm('Are you sure you want to log out?')) {
                logout();
            }
        }
    }
}

function closePopup() {
    if (typeof jQuery !== 'undefined' && $('#logout-confirmation').modal) {
        $('#logout-confirmation').modal('hide');
    } else {
        var modal = document.getElementById('logout-confirmation');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
    }
}

function logout() {
    window.location.href = "{{url('logout')}}"; 
}
</script>
<script>
    function refreshPage(event) {
    event.preventDefault(); 
    const animation = document.getElementById('refresh');
    animation.style.display = 'flex';

   
    setTimeout(() => {
        animation.style.display = 'none';
        location.reload(); 
    }, 1000); 
}



/* function fetchBalance() {
        $.ajax({
            url: '/check-balance',
            type: 'GET',
            success: function(response) {
                $('#balanceResult').html("Balance: " + JSON.stringify(response));
            },
            error: function(error) {
                console.log(error);
                $('#balanceResult').html("Error fetching balance.");
            }
        });
    }*/

 
    
 
</script>
<script>
  window.addEventListener('load', function() {


const id = "{{Session::get('id')}}";
const modal = "{{Session::get('modal_name')}}" || 'User';
    const user = {
      id:id ,
      'modal':modal
    };

    // Save user info as JSON string in localStorage
    localStorage.setItem('user', JSON.stringify(user));
  });
</script>



<style>
.Display_none_desktop{
    display:none;
}
  @media screen and (max-width:600px) {
      .Display_none_desktop{
        display:block;
        color:black;
        font-size:20px;
      display: flex;
        
    }
    .first-name{
        display:inline;
    }
    #navbar_nav{
        margin-left:0px !important;
        margin-top:0px !important;
    }
    .header-center-nav{
        margin-left:0 !important;
        margin-top:0 !important;
        padding-left:8px;
    }
    .role-name{
        font-size:15px;
        display:inline;
    }
    .user-panel {
      padding: 0px 0px !important
    }

    .flex_centerd_profile{
        gap: 4px;
    }

    .header-gap-item{
        margin-right: 4px;
    }
   
  }

  .solid {
    border: solid thin;
    margin: 4px;
    width: 110px;
    height: 91px;
  }

  .center {
    margin-left: 33%;
  }

  .preloader {
    /*background-color:#f7f7f7e8;
*/
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 999999;
    -webkit-transition: .6s;
    -o-transition: .6s;
    transition: .6s;
    margin: 0 auto;
  }

  .preloader .preloader-circle {
    width: 169px;
    height: 169px;
    position: relative;
    border-style: solid;
    border-width: 1px;
    border-top-color: #ff2020;
    border-bottom-color: transparent;
    border-left-color: transparent;
    border-right-color: transparent;
    z-index: 10;
    border-radius: 50% ! important;
    -webkit-box-shadow: 0 1px 5px 0 rgba(35, 181, 185, 0.15);
    box-shadow: 0 1px 5px 0 rgba(35, 181, 185, 0.15);
    background-color: #ffffff;
    -webkit-animation: zoom 2000ms infinite ease;
    animation: zoom 2000ms infinite ease;
    -webkit-transition: .6s;
    -o-transition: .6s;
    transition: .6s;
  }

  .preloader .preloader-circle2 {
    border-top-color: #0078ff;
  }

  .preloader .preloader-img {
    position: absolute;
    top: 50%;
    z-index: 200;
    left: 0;
    right: 0;
    margin: 0 auto;
    text-align: center;
    display: inline-block;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    padding-top: 6px;
    -webkit-transition: .6s;
    -o-transition: .6s;
    transition: .6s;
  }

  .preloader .preloader-img img {
    max-width: 163px
  }

  . .preloader .pere-text strong {
    font-weight: 800;
    color: #dca73a;
    text-transform: uppercase;
  }

  @-webkit-keyframes zoom {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
      -webkit-transition: .6s;
      -o-transition: .6s;
      transition: .6s
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
      -webkit-transition: .6s;
      -o-transition: .6s;
      transition: .6s
    }
  }

  @keyframes zoom {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
      -webkit-transition: .6s;
      -o-transition: .6s;
      transition: .6s
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
      -webkit-transition: .6s;
      -o-transition: .6s;
      transition: .6s;
    }
  }

  .section-padding2 {
    padding-top: 200px;
    padding-bottom: 200px;
  }

  @media only screen and (min-width: 1200px) and (max-width: 1600px) {
    .section-padding2 {
      padding-top: 200px;
      padding-bottom: 200px;
    }
  }

  @media only screen and (min-width: 992px) and (max-width: 1199px) {
    .section-padding2 {
      padding-top: 200px;
      padding-bottom: 200px;
    }
  }

  @media only screen and (min-width: 768px) and (max-width: 991px) {
    .section-padding2 {
      padding-top: 100px;
      padding-bottom: 100px;
    }
  }

  @media only screen and (min-width: 576px) and (max-width: 767px) {
    .section-padding2 {
      padding-top: 50px;
      padding-bottom: 50px;
    }
  }

  @media (max-width: 575px) {
    .section-padding2 {
      padding-top: 50px;
      padding-bottom: 50px
    }
  }

  .padding-bottom {
    padding-bottom: 250px;
  }

  @media only screen and (min-width: 1200px) and (max-width: 1600px) {
    .padding-bottom {
      padding-bottom: 250px;
    }
  }

  @media only screen and (min-width: 992px) and (max-width: 1199px) {
    .padding-bottom {
      padding-bottom: 150px;
    }
  }

  @media only screen and (min-width: 768px) and (max-width: 991px) {
    .padding-bottom {
      padding-bottom: 40px;
    }
  }

  @media only screen and (min-width: 576px) and (max-width: 767px) {
    .padding-bottom {
      padding-bottom: 10px;
    }
  }

  @media (max-width: 575px) {
    .padding-bottom {
      padding-bottom: 10px;
    }
  }

  .lf-padding {
    padding-left: 60px;
    padding-right: 60px;
  }

  @media only screen and (min-width: 992px) and (max-width: 1199px) {
    .lf-padding {
      padding-left: 60px;
      padding-right: 60px;
    }
  }

  @media only screen and (min-width: 768px) and (max-width: 991px) {
    .lf-padding {
      padding-left: 30px;
      padding-right: 30px
    }
  }

  @media only screen and (min-width: 576px) and (max-width: 767px) {
    .lf-padding {
      padding-left: 15px;
      padding-right: 15px;
    }
  }

  .align-items-center {
    -ms-flex-align: center !important;
    align-items: center !important;
  }

  .justify-content-center {
    -ms-flex-pack: center !important;
    justify-content: center !important;
  }

  .d-flex {
    display: -ms-flexbox !important;
    display: flex !important;
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
    
    #logout-confirmation .modal-content {
        box-shadow: 0 20px 45px -8px rgba(0, 44, 84, 0.35) !important;
    }
</style>

<script>
(function() {
    function initQuickSearch() {
        var searchInput = document.getElementById('headerStudentSearchInput');
        var dropdown = document.getElementById('headerSearchDropdown');
        var resultsList = document.getElementById('headerSearchResultsList');
        var countEl = document.getElementById('headerSearchCount');
        var spinner = document.getElementById('headerSearchSpinner');
        var clearBtn = document.getElementById('headerSearchClear');
        var footerEl = document.getElementById('headerSearchFooter');
        var allLink = document.getElementById('headerSearchAllLink');

        if (!searchInput || !dropdown || !resultsList) return;

        var debounceTimer = null;
        var currentRequest = null;
        var selectedIndex = -1;

        function debounce(callback, wait) {
            return function() {
                var context = this;
                var args = arguments;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    callback.apply(context, args);
                }, wait);
            };
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function closeDropdown() {
            dropdown.style.display = 'none';
            selectedIndex = -1;
        }

        function showDropdown() {
            dropdown.style.display = 'block';
        }

        function performSearch(query) {
            query = query.trim();
            if (query.length < 2) {
                closeDropdown();
                if (spinner) spinner.style.display = 'none';
                return;
            }

            if (spinner) spinner.style.display = 'inline-block';
            if (clearBtn) clearBtn.style.display = 'inline-block';

            if (currentRequest && currentRequest.abort) {
                currentRequest.abort();
            }

            var searchUrl = "{{ url('quickStudentSearch') }}?keyword=" + encodeURIComponent(query);
            if (allLink) {
                allLink.href = "{{ url('admissionView') }}?name=" + encodeURIComponent(query);
            }

            currentRequest = $.ajax({
                url: searchUrl,
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (spinner) spinner.style.display = 'none';
                    if (!res || res.status !== 'success' || !res.data) {
                        renderEmpty(query);
                        return;
                    }

                    var students = res.data;
                    if (students.length === 0) {
                        renderEmpty(query);
                    } else {
                        renderResults(students, query);
                    }
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    if (spinner) spinner.style.display = 'none';
                    renderError();
                }
            });
        }

        function renderResults(students, query) {
            if (countEl) {
                countEl.textContent = students.length + (students.length === 1 ? ' Student Found' : ' Students Found');
            }

            var html = '';
            students.forEach(function(s, idx) {
                var avatarHtml = '';
                if (s.image_url) {
                    avatarHtml = '<img src="' + escapeHtml(s.image_url) + '" class="quick-student-avatar-img" onerror="this.style.display=\'none\'; if(this.nextElementSibling){this.nextElementSibling.style.display=\'flex\';}">' +
                                 '<div class="quick-student-avatar-initial" style="display:none;">' + escapeHtml(s.first_letter) + '</div>';
                } else {
                    avatarHtml = '<div class="quick-student-avatar-initial">' + escapeHtml(s.first_letter) + '</div>';
                }

                var aadhaarHtml = '';
                if (s.aadhaar && s.aadhaar !== '-' && s.aadhaar !== '0') {
                    aadhaarHtml = '<span class="quick-meta-pill" title="Aadhaar Number"><i class="fa fa-id-card text-muted"></i> <span>Aadhar:</span> <b>' + escapeHtml(s.aadhaar) + '</b></span>';
                }

                html += '<div class="quick-student-item" data-index="' + idx + '" data-profile="' + escapeHtml(s.profile_url) + '" data-fees="' + escapeHtml(s.collect_fees_url) + '">' +
                            '<div class="quick-student-avatar-col">' + avatarHtml + '</div>' +
                            '<div class="quick-student-info-col">' +
                                '<div class="quick-student-top-line">' +
                                    '<span class="quick-student-name" title="' + escapeHtml(s.name) + '">' + escapeHtml(s.name) + '</span>' +
                                    '<span class="quick-badge-class"><i class="fa fa-book"></i> ' + escapeHtml(s.class_name) + '</span>' +
                                    '<span class="quick-badge-adm"><i class="fa fa-id-badge"></i> Adm: #' + escapeHtml(s.admission_no) + '</span>' +
                                '</div>' +
                                '<div class="quick-student-meta-line">' +
                                    '<span class="quick-meta-pill" title="Father Name"><i class="fa fa-user-circle text-muted"></i> <span>S/O:</span> <b>' + escapeHtml(s.father_name) + '</b></span>' +
                                    '<span class="quick-meta-pill" title="Mobile"><i class="fa fa-phone text-muted"></i> <b>' + escapeHtml(s.mobile) + '</b></span>' +
                                    aadhaarHtml +
                                '</div>' +
                            '</div>' +
                            '<div class="quick-student-actions-col">' +
                                '<a href="' + escapeHtml(s.collect_fees_url) + '" class="quick-action-btn btn-collect-fees" title="Directly Collect Fees for ' + escapeHtml(s.name) + '">' +
                                    '<i class="fa fa-money"></i> <span>Collect Fees</span>' +
                                '</a>' +
                                '<a href="' + escapeHtml(s.profile_url) + '" class="quick-action-btn btn-view-profile" title="View Full Student Profile">' +
                                    '<i class="fa fa-user"></i> <span>Profile</span>' +
                                '</a>' +
                            '</div>' +
                        '</div>';
            });

            resultsList.innerHTML = html;
            if (footerEl) footerEl.style.display = 'block';
            showDropdown();
            selectedIndex = -1;
        }

        function renderEmpty(query) {
            if (countEl) countEl.textContent = '0 Students Found';
            resultsList.innerHTML = '<div class="quick-search-empty-state">' +
                                        '<i class="fa fa-user-times"></i>' +
                                        '<div class="quick-search-empty-title">No students found matching "' + escapeHtml(query) + '"</div>' +
                                        '<div class="quick-search-empty-desc">Try searching by mobile number, admission number, father name, or aadhar.</div>' +
                                    '</div>';
            if (footerEl) footerEl.style.display = 'none';
            showDropdown();
            selectedIndex = -1;
        }

        function renderError() {
            if (countEl) countEl.textContent = 'Error';
            resultsList.innerHTML = '<div class="quick-search-empty-state">' +
                                        '<i class="fa fa-exclamation-triangle text-danger"></i>' +
                                        '<div class="quick-search-empty-title">Search could not be completed</div>' +
                                        '<div class="quick-search-empty-desc">Please try again in a moment.</div>' +
                                    '</div>';
            if (footerEl) footerEl.style.display = 'none';
            showDropdown();
            selectedIndex = -1;
        }

        var debouncedSearch = debounce(function() {
            performSearch(searchInput.value);
        }, 250);

        searchInput.addEventListener('input', function() {
            var val = this.value;
            if (val.trim().length > 0) {
                if (clearBtn) clearBtn.style.display = 'inline-block';
            } else {
                if (clearBtn) clearBtn.style.display = 'none';
            }
            debouncedSearch();
        });

        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && resultsList.children.length > 0) {
                showDropdown();
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchInput.value = '';
                clearBtn.style.display = 'none';
                if (spinner) spinner.style.display = 'none';
                closeDropdown();
                searchInput.focus();
            });
        }

        searchInput.addEventListener('keydown', function(e) {
            if (dropdown.style.display === 'none') return;

            var items = resultsList.querySelectorAll('.quick-student-item');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % items.length;
                highlightItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                highlightItem(items);
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && selectedIndex < items.length) {
                    e.preventDefault();
                    var profileUrl = items[selectedIndex].getAttribute('data-profile');
                    if (profileUrl) window.location.href = profileUrl;
                }
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });

        function highlightItem(items) {
            items.forEach(function(item, idx) {
                if (idx === selectedIndex) {
                    item.classList.add('selected');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('selected');
                }
            });
        }

        $(document).on('click', '.quick-student-item', function(e) {
            if ($(e.target).closest('.quick-action-btn').length > 0) {
                return;
            }
            var profileUrl = $(this).data('profile');
            if (profileUrl) {
                window.location.href = profileUrl;
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header-quick-search-box')) {
                closeDropdown();
            }
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            } else if (e.altKey && (e.key === 's' || e.key === 'S')) {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuickSearch);
    } else {
        initQuickSearch();
    }
})();
</script>
