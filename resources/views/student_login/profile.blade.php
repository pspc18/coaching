@php
    $getUser = Helper::getUser();
    $getSetting = Helper::getSetting();
    $fullName = trim(($data->first_name ?? '').' '.($data->last_name ?? ''));
    $className = optional($data->ClassTypes)->name ?? '-';
    $imageUrl = !empty($data->image)
        ? env('IMAGE_SHOW_PATH').'/profile/'.$data->image
        : asset('public/assets/student_login/img/user_icon.png');
    $profileFields = [$data->image, $data->mobile, $data->email, $data->address, $data->father_name, $data->mother_name, $data->dob];
    $completedFields = collect($profileFields)->filter(function ($value) { return !empty($value); })->count();
    $profileCompletion = (int) round(($completedFields / count($profileFields)) * 100);
    $canEditProfile = (int) Session::get('role_id') !== 3;
@endphp
@extends('student_login.layout.app')
@section('title', 'My Profile')
@section('page_title', 'MY PROFILE')
@section('page_sub', $fullName.' · '.$className)

@section('content')
<section class="student-profile-app">
    <div class="profile-cover">
        <div class="cover-grid"></div>
        <div class="cover-orb orb-one"></div>
        <div class="cover-orb orb-two"></div>
        <div class="profile-identity">
            <div class="student-profile-avatar-frame">
                <div id="avatarPlaceholder" class="avatar-placeholder {{ !empty($data->image) ? 'd-none' : '' }}"><i class="bi bi-person-fill"></i></div>
                <div id="profileImage" class="profile-avatar-photo {{ empty($data->image) ? 'd-none' : '' }}" @if(!empty($data->image)) style="background-image:url('{{ $imageUrl }}')" @endif role="img" aria-label="{{ $fullName }}"></div>
                @if($canEditProfile)
                    <button type="button" id="changePhotoBtn" class="avatar-action" aria-label="Change profile photo"><i class="bi bi-camera-fill"></i></button>
                    <div id="photoLoader" class="avatar-loader"><span class="spinner-border spinner-border-sm"></span></div>
                @endif
            </div>
            <h1>{{ $fullName ?: 'Student' }}</h1>
            <p>{{ $className }} <span></span> {{ $getSetting->name ?? 'School' }}</p>
            <div class="student-status"><i class="bi bi-patch-check-fill"></i> Active student</div>
        </div>
    </div>

    <div class="profile-body">
        <div class="identity-strip">
            <div><span>Admission No.</span><strong>{{ $data->admissionNo ?: '-' }}</strong></div>
            <div><span>Username</span><strong>{{ $data->userName ?: '-' }}</strong></div>
        </div>

        <div class="completion-card">
            <div class="completion-ring" style="--completion: {{ $profileCompletion * 3.6 }}deg"><div>{{ $profileCompletion }}<small>%</small></div></div>
            <div class="completion-copy"><span>Profile strength</span><strong>{{ !$canEditProfile ? 'Profile information' : ($profileCompletion >= 85 ? 'Looking great!' : 'Complete your profile') }}</strong><small>{{ !$canEditProfile ? 'Profile updates are managed by the school.' : ($profileCompletion >= 85 ? 'Your important details are up to date.' : 'Add missing contact details and a profile photo.') }}</small></div>
            @if($canEditProfile)
                <button type="button" data-bs-toggle="modal" data-bs-target="#editContactModal" aria-label="Complete profile"><i class="bi bi-arrow-up-right"></i></button>
            @endif
        </div>

        <div class="section-heading">
            <div><span>Personal information</span><small>Your registered student details</small></div>
        </div>

        <div class="app-card detail-card">
            <div class="detail-row">
                <div class="detail-icon blue"><i class="bi bi-person"></i></div>
                <div><span>Full Name</span><strong>{{ $fullName ?: '-' }}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-icon purple"><i class="bi bi-calendar3"></i></div>
                <div><span>Date of Birth</span><strong>{{ !empty($data->dob) ? date('d M Y', strtotime($data->dob)) : '-' }}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-icon amber"><i class="bi bi-people"></i></div>
                <div><span>Father's Name</span><strong>{{ $data->father_name ?: '-' }}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-icon pink"><i class="bi bi-person-heart"></i></div>
                <div><span>Mother's Name</span><strong>{{ $data->mother_name ?: '-' }}</strong></div>
            </div>
        </div>

        <div class="section-heading action-heading">
            <div><span>Contact details</span><small>{{ $canEditProfile ? 'Keep your contact information updated' : 'Contact the school to request changes' }}</small></div>
            @if($canEditProfile)
                <button type="button" data-bs-toggle="modal" data-bs-target="#editContactModal"><i class="bi bi-pencil"></i> Edit</button>
            @endif
        </div>

        <div class="app-card detail-card">
            <div class="detail-row">
                <div class="detail-icon green"><i class="bi bi-telephone"></i></div>
                <div><span>Mobile Number</span><strong id="profileMobile">{{ $data->mobile ?: '-' }}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-icon cyan"><i class="bi bi-envelope"></i></div>
                <div><span>Email Address</span><strong id="profileEmail">{{ $data->email ?: '-' }}</strong></div>
            </div>
            <div class="detail-row align-start">
                <div class="detail-icon red"><i class="bi bi-geo-alt"></i></div>
                <div><span>Home Address</span><strong id="profileAddress">{{ $data->address ?: '-' }}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-icon slate"><i class="bi bi-phone"></i></div>
                <div><span>Parent Contact</span><strong>{{ $data->father_mobile ?: ($data->mother_mob ?: '-') }}</strong></div>
            </div>
        </div>

        <div class="section-heading">
            <div><span>Account & security</span><small>Manage your login security</small></div>
        </div>

        <button type="button" class="app-card security-action" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <span class="security-icon"><i class="bi bi-shield-lock"></i></span>
            <span class="security-copy"><strong>Change Password</strong><small>Update your account password securely</small></span>
            <i class="bi bi-chevron-right"></i>
        </button>

        @if($canEditProfile)
            <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" hidden>
            @if(!empty($data->image))
                <button type="button" id="removePhotoBtn" class="remove-photo"><i class="bi bi-trash3"></i> Remove profile photo</button>
            @endif
        @endif
    </div>
</section>

@if($canEditProfile)
<div class="modal fade profile-modal" id="editContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="contactForm" novalidate>
                <div class="modal-header">
                    <div><h5 class="modal-title">Edit Contact Details</h5><small>Update your reachable information</small></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="contactErrors" class="form-alert d-none"></div>
                    <label class="field-label" for="mobile">Mobile Number</label>
                    <div class="input-shell"><i class="bi bi-telephone"></i><input id="mobile" name="mobile" type="tel" class="form-control" value="{{ $data->mobile }}" maxlength="15" inputmode="numeric"></div>
                    <label class="field-label" for="email">Email Address</label>
                    <div class="input-shell"><i class="bi bi-envelope"></i><input id="email" name="email" type="email" class="form-control" value="{{ $data->email }}"></div>
                    <label class="field-label" for="address">Home Address</label>
                    <div class="input-shell textarea-shell"><i class="bi bi-geo-alt"></i><textarea id="address" name="address" class="form-control" rows="3">{{ $data->address }}</textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary submit-btn"><span>Save Changes</span></button></div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade profile-modal" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="passwordForm" novalidate>
                <div class="modal-header security-modal-head">
                    <div class="modal-shield"><i class="bi bi-shield-check"></i></div>
                    <div><h5 class="modal-title">Change Password</h5><small>Choose a strong password you don't reuse</small></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="passwordErrors" class="form-alert d-none"></div>
                    <label class="field-label" for="current_password">Current Password</label>
                    <div class="input-shell password-shell"><i class="bi bi-lock"></i><input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password"><button type="button" class="password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button></div>
                    <label class="field-label" for="password">New Password</label>
                    <div class="input-shell password-shell"><i class="bi bi-key"></i><input id="password" name="password" type="password" class="form-control" autocomplete="new-password"><button type="button" class="password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button></div>
                    <div class="strength-track"><span id="strengthBar"></span></div>
                    <small id="strengthText" class="strength-text">Use at least 8 characters</small>
                    <label class="field-label" for="password_confirmation">Confirm New Password</label>
                    <div class="input-shell password-shell"><i class="bi bi-check2-circle"></i><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password"><button type="button" class="password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button></div>
                    <div class="password-hint"><i class="bi bi-info-circle"></i><span>Your password must be at least 8 characters and different from the current password.</span></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary submit-btn"><span>Update Password</span></button></div>
            </form>
        </div>
    </div>
</div>

<style>
.student-profile-app{min-height:calc(100vh - 126px);background:#f3f6fb;padding-bottom:28px;color:#1d2939}
.profile-cover{height:272px;position:relative;overflow:hidden;background:linear-gradient(145deg,#4169e1 0%,#294bb4 48%,#172d73 100%);border-radius:0 0 34px 34px;box-shadow:0 18px 38px rgba(27,52,130,.2)}
.cover-grid{position:absolute;inset:0;opacity:.13;background-image:linear-gradient(rgba(255,255,255,.25) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.25) 1px,transparent 1px);background-size:28px 28px;mask-image:linear-gradient(to bottom,black,transparent 88%)}
.cover-orb{position:absolute;border-radius:50%;background:rgba(255,255,255,.08)}.orb-one{width:220px;height:220px;right:-90px;top:-70px}.orb-two{width:130px;height:130px;left:-50px;bottom:-20px}
.profile-identity{position:relative;z-index:1;text-align:center;padding-top:24px;color:#fff}.student-profile-avatar-frame{position:relative!important;display:block!important;width:108px!important;height:108px!important;min-width:108px!important;min-height:108px!important;max-width:108px!important;max-height:108px!important;margin:0 auto!important;border:4px solid rgba(255,255,255,.95);border-radius:50%!important;overflow:visible;box-shadow:0 12px 34px rgba(5,20,70,.38);isolation:isolate}.student-profile-avatar-frame:before{content:"";position:absolute;inset:-9px;border-radius:50%;border:1px solid rgba(255,255,255,.3);pointer-events:none}.profile-avatar-photo{position:absolute!important;inset:0!important;width:100px!important;height:100px!important;min-width:100px!important;min-height:100px!important;max-width:100px!important;max-height:100px!important;margin:0!important;padding:0!important;border-radius:50%!important;background-size:cover!important;background-position:center!important;background-repeat:no-repeat!important}.profile-avatar-photo.d-none{display:none!important}.avatar-placeholder{position:absolute;inset:0;width:100px!important;height:100px!important;min-width:100px!important;min-height:100px!important;max-width:100px!important;max-height:100px!important;border-radius:50%;display:grid;place-items:center;background:linear-gradient(145deg,#eff3ff,#dce6ff);color:#3156d3;font-size:52px}.avatar-placeholder.d-none{display:none!important}
.avatar-action{position:absolute;right:-2px;bottom:4px;width:34px;height:34px;border-radius:50%;border:3px solid #fff;background:#ffb020;color:#fff;display:grid;place-items:center;box-shadow:0 4px 12px rgba(0,0,0,.2)}.avatar-loader{position:absolute;inset:4px;border-radius:50%;background:rgba(20,37,83,.68);display:none;place-items:center;color:#fff}
.profile-identity h1{font-size:22px;font-weight:750;margin:11px 16px 3px;letter-spacing:-.02em}.profile-identity p{margin:0;font-size:12px;color:rgba(255,255,255,.78)}.profile-identity p span{display:inline-block;width:4px;height:4px;border-radius:50%;background:#ffcf5a;margin:0 7px 2px}.student-status{display:inline-flex;align-items:center;gap:5px;margin-top:9px;padding:4px 9px;border:1px solid rgba(255,255,255,.2);border-radius:99px;background:rgba(255,255,255,.1);backdrop-filter:blur(8px);font-size:9px;font-weight:650;color:#f2f6ff}.student-status i{color:#79e6ae}
.profile-body{position:relative;margin-top:-24px;padding:0 14px;z-index:2}.identity-strip{background:rgba(255,255,255,.97);border-radius:18px;display:grid;grid-template-columns:1fr 1fr;padding:16px 4px;box-shadow:0 12px 34px rgba(31,52,112,.14);backdrop-filter:blur(12px)}.identity-strip>div{text-align:center;padding:0 8px}.identity-strip>div+div{border-left:1px solid #e9edf5}.identity-strip span,.detail-row span{display:block;color:#8a94a6;font-size:9px;text-transform:uppercase;letter-spacing:.06em}.identity-strip strong{display:block;color:#253858;font-size:13px;margin-top:4px;word-break:break-word}
.completion-card{display:flex;align-items:center;gap:12px;margin-top:12px;padding:13px 14px;border-radius:18px;background:linear-gradient(125deg,#fff 0%,#f7f9ff 100%);border:1px solid #e7ecf8;box-shadow:0 7px 22px rgba(42,55,92,.06)}.completion-ring{--completion:0deg;width:48px;height:48px;flex:0 0 48px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(#3156d3 var(--completion),#e7ebf5 0);position:relative}.completion-ring:before{content:"";position:absolute;inset:5px;background:#fff;border-radius:50%}.completion-ring div{position:relative;z-index:1;font-size:12px;font-weight:800;color:#263e94}.completion-ring small{font-size:7px}.completion-copy{min-width:0;flex:1}.completion-copy>span{display:block;font-size:9px;color:#8b95a7;text-transform:uppercase;letter-spacing:.06em}.completion-copy strong{display:block;font-size:12px;color:#253858;margin:1px 0}.completion-copy small{display:block;font-size:9px;color:#98a2b3;line-height:1.35}.completion-card button{width:31px;height:31px;flex:0 0 31px;border:0;border-radius:10px;background:#e8eeff;color:#3156d3;display:grid;place-items:center}
.section-heading{display:flex;align-items:center;justify-content:space-between;margin:22px 2px 9px}.section-heading span{display:block;font-size:14px;font-weight:750;color:#26354e}.section-heading small{display:block;color:#98a2b3;font-size:10px;margin-top:1px}.action-heading button{border:0;background:#e8eeff;color:#3156d3;border-radius:9px;padding:6px 10px;font-size:11px;font-weight:700}
.app-card{background:#fff;border:1px solid #edf0f6;border-radius:18px;box-shadow:0 7px 24px rgba(42,55,92,.06)}.detail-card{padding:3px 14px;position:relative;overflow:hidden}.detail-card:before{content:"";position:absolute;left:0;top:18px;bottom:18px;width:3px;border-radius:0 3px 3px 0;background:linear-gradient(#4169e1,#7c9aff)}.detail-row{display:flex;align-items:center;gap:12px;padding:14px 1px}.detail-row+.detail-row{border-top:1px solid #eef1f6}.detail-row>div:last-child{min-width:0;flex:1}.detail-row strong{display:block;font-size:12px;color:#253858;font-weight:650;margin-top:3px;line-height:1.45;word-break:break-word}.align-start{align-items:flex-start}
.detail-icon{width:36px;height:36px;flex:0 0 36px;border-radius:11px;display:grid;place-items:center;font-size:16px}.detail-icon.blue{background:#e9efff;color:#3156d3}.detail-icon.purple{background:#f2eaff;color:#7a45c7}.detail-icon.amber{background:#fff4dc;color:#d78a00}.detail-icon.pink{background:#ffeaf3;color:#d33d78}.detail-icon.green{background:#e4f8ef;color:#139466}.detail-icon.cyan{background:#e4f7fa;color:#1089a0}.detail-icon.red{background:#ffeceb;color:#dd5149}.detail-icon.slate{background:#edf1f5;color:#5a6878}
.security-action{width:100%;border:1px solid #dfe6f7;display:flex;align-items:center;text-align:left;padding:15px;color:#344054;background:linear-gradient(125deg,#fff 55%,#f1f5ff);transition:transform .18s ease,box-shadow .18s ease}.security-action:active{transform:scale(.985)}.security-icon{width:45px;height:45px;display:grid;place-items:center;border-radius:14px;background:linear-gradient(145deg,#3156d3,#6d8aeb);color:#fff;font-size:20px;box-shadow:0 7px 16px rgba(49,86,211,.25)}.security-copy{flex:1;margin-left:12px}.security-copy strong{display:block;font-size:13px}.security-copy small{display:block;color:#98a2b3;font-size:10px;margin-top:2px}.security-action>.bi-chevron-right{color:#6981c8}.remove-photo{display:block;margin:14px auto 0;border:0;background:transparent;color:#d14d4d;font-size:11px;padding:7px}
.profile-modal .modal-dialog{width:calc(100% - 24px);margin:12px auto}.profile-modal .modal-content{border:0;border-radius:20px;box-shadow:0 20px 60px rgba(13,25,60,.22);overflow:hidden}.profile-modal .modal-header{padding:18px;border-bottom:1px solid #edf0f5;align-items:flex-start}.profile-modal .modal-title{font-size:17px;font-weight:750;color:#253858}.profile-modal .modal-header small{font-size:10px;color:#8a94a6}.profile-modal .modal-body{padding:17px 18px}.profile-modal .modal-footer{padding:12px 18px;border-top:1px solid #edf0f5;gap:8px}.profile-modal .modal-footer .btn{flex:1;border-radius:11px;font-size:12px;font-weight:650;padding:10px}.security-modal-head{display:flex;gap:10px}.security-modal-head>div:nth-child(2){flex:1}.modal-shield{width:38px;height:38px;border-radius:11px;background:#e8eeff;color:#3156d3;display:grid;place-items:center;font-size:19px}
.field-label{display:block;margin:0 0 6px;font-size:11px;font-weight:650;color:#475467}.field-label:not(:first-of-type){margin-top:14px}.input-shell{position:relative}.input-shell>i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#98a2b3;z-index:2}.input-shell .form-control{height:44px;border:1px solid #dfe4ec;border-radius:11px;padding-left:38px;font-size:12px;box-shadow:none}.input-shell .form-control:focus{border-color:#6682db;box-shadow:0 0 0 3px rgba(49,86,211,.1)}.textarea-shell>i{top:14px;transform:none}.textarea-shell .form-control{height:auto;padding-top:11px;resize:none}.password-shell .form-control{padding-right:42px}.password-toggle{position:absolute;right:5px;top:5px;width:34px;height:34px;border:0;background:transparent;color:#7b8798}.strength-track{height:4px;background:#edf0f5;border-radius:99px;margin:8px 2px 4px;overflow:hidden}.strength-track span{display:block;width:0;height:100%;transition:.2s;border-radius:99px}.strength-text{font-size:9px;color:#98a2b3}.password-hint{display:flex;gap:7px;background:#f5f7fc;color:#667085;border-radius:10px;padding:9px 10px;margin-top:14px;font-size:9px;line-height:1.45}.form-alert{background:#fff0f0;color:#b42318;border:1px solid #ffd4d2;border-radius:10px;padding:9px 11px;margin-bottom:13px;font-size:10px}.btn-primary{background:#3156d3;border-color:#3156d3}
[data-theme="dark"] .student-profile-app{background:#111827;color:#e5e7eb}[data-theme="dark"] .identity-strip,[data-theme="dark"] .app-card,[data-theme="dark"] .profile-modal .modal-content,[data-theme="dark"] .completion-card{background:#1b2433;border-color:#2c3748}[data-theme="dark"] .completion-ring:before{background:#1b2433}[data-theme="dark"] .completion-copy strong,[data-theme="dark"] .identity-strip strong,[data-theme="dark"] .detail-row strong,[data-theme="dark"] .section-heading span,[data-theme="dark"] .security-action,[data-theme="dark"] .profile-modal .modal-title{color:#e7ecf4}[data-theme="dark"] .detail-row+.detail-row,[data-theme="dark"] .identity-strip>div+div{border-color:#303b4c}[data-theme="dark"] .profile-modal .modal-header,[data-theme="dark"] .profile-modal .modal-footer{border-color:#303b4c}[data-theme="dark"] .input-shell .form-control{background:#111827;border-color:#3b4657;color:#e5e7eb}[data-theme="dark"] .profile-modal .btn-close{filter:invert(1)}
@media(min-width:451px){.profile-cover{border-radius:0 0 30px 30px}.profile-modal .modal-dialog{max-width:420px}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const profileUrl = @json(url('profileStudent'));
    const passwordUrl = @json(route('student.profile.change-password'));

    function showErrors(box, payload, fallback) {
        const errors = payload && payload.errors ? Object.values(payload.errors).flat() : [payload.message || fallback];
        box.textContent = errors.join(' ');
        box.classList.remove('d-none');
    }

    function setBusy(form, busy) {
        const button = form.querySelector('.submit-btn');
        button.disabled = busy;
        button.querySelector('span').textContent = busy ? 'Please wait...' : button.dataset.label;
    }

    document.querySelectorAll('.submit-btn').forEach(button => button.dataset.label = button.querySelector('span').textContent);

    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
    const photoImage = document.getElementById('profileImage');
    const avatarPlaceholder = document.getElementById('avatarPlaceholder');
    const photoLoader = document.getElementById('photoLoader');
    document.getElementById('changePhotoBtn').addEventListener('click', () => photoInput.click());
    photoInput.addEventListener('change', async function () {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 3 * 1024 * 1024) return StudentModal.error('Photo too large', 'Please select an image smaller than 3 MB.');
        const formData = new FormData(); formData.append('photo', file);
        photoLoader.style.display = 'grid';
        try {
            const response = await fetch(profileUrl, {method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body:formData});
            const result = await response.json();
            if (!response.ok) throw result;
            photoImage.style.backgroundImage = 'url("' + result.image_url.replace(/"/g, '%22') + '")';
            photoImage.classList.remove('d-none');
            avatarPlaceholder.classList.add('d-none');
            StudentModal.open({type:'success', title:'Photo updated', message:result.message, timer:1800, showConfirm:false});
        } catch (error) { StudentModal.error('Upload failed', error.message || 'Please choose a valid JPG, PNG or WebP image.'); }
        finally { photoLoader.style.display = 'none'; photoInput.value = ''; }
    });

    const removePhoto = document.getElementById('removePhotoBtn');
    if (removePhoto) removePhoto.addEventListener('click', async function () {
        const confirmation = await StudentModal.confirm({type:'warning', title:'Remove profile photo?', message:'The default avatar will be shown.', confirmText:'Remove', danger:true});
        if (!confirmation.isConfirmed) return;
        const response = await fetch(profileUrl, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body:JSON.stringify({delete_photo:true})});
        const result = await response.json();
        if (response.ok) { photoImage.style.backgroundImage = ''; photoImage.classList.add('d-none'); avatarPlaceholder.classList.remove('d-none'); removePhoto.remove(); StudentModal.open({type:'success', title:'Photo removed', timer:1500, showConfirm:false}); }
    });

    const contactForm = document.getElementById('contactForm');
    contactForm.addEventListener('submit', async function (event) {
        event.preventDefault(); const errorBox = document.getElementById('contactErrors'); errorBox.classList.add('d-none'); setBusy(this, true);
        const payload = Object.fromEntries(new FormData(this).entries());
        try {
            const response = await fetch(profileUrl, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body:JSON.stringify(payload)});
            const result = await response.json(); if (!response.ok) { showErrors(errorBox, result, 'Unable to update details.'); return; }
            document.getElementById('profileMobile').textContent = result.data.mobile || '-';
            document.getElementById('profileEmail').textContent = result.data.email || '-';
            document.getElementById('profileAddress').textContent = result.data.address || '-';
            bootstrap.Modal.getInstance(document.getElementById('editContactModal')).hide();
            StudentModal.open({type:'success', title:'Profile updated', message:result.message, timer:1800, showConfirm:false});
        } catch (error) { showErrors(errorBox, error, 'Network error. Please try again.'); }
        finally { setBusy(this, false); }
    });
    }

    document.querySelectorAll('.password-toggle').forEach(toggle => toggle.addEventListener('click', function () {
        const input = this.parentElement.querySelector('input'); const visible = input.type === 'text'; input.type = visible ? 'password' : 'text';
        this.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    }));

    const passwordInput = document.getElementById('password');
    passwordInput.addEventListener('input', function () {
        let score = 0; if(this.value.length >= 8) score++; if(/[A-Z]/.test(this.value)) score++; if(/[0-9]/.test(this.value)) score++; if(/[^A-Za-z0-9]/.test(this.value)) score++;
        const colors=['#e34850','#e59a15','#4aa86b','#16865a'], labels=['Weak','Fair','Good','Strong'];
        const bar=document.getElementById('strengthBar'), text=document.getElementById('strengthText');
        bar.style.width=(score * 25)+'%'; bar.style.background=colors[Math.max(0,score-1)]; text.textContent=this.value ? labels[Math.max(0,score-1)]+' password' : 'Use at least 8 characters';
    });

    const passwordForm = document.getElementById('passwordForm');
    passwordForm.addEventListener('submit', async function (event) {
        event.preventDefault(); const errorBox=document.getElementById('passwordErrors'); errorBox.classList.add('d-none'); setBusy(this,true);
        const payload=Object.fromEntries(new FormData(this).entries());
        try {
            const response=await fetch(passwordUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:JSON.stringify(payload)});
            const result=await response.json(); if(!response.ok){showErrors(errorBox,result,'Unable to change password.');return;}
            this.reset(); document.getElementById('strengthBar').style.width='0'; document.getElementById('strengthText').textContent='Use at least 8 characters';
            bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
            StudentModal.success('Password changed', 'Use your new password the next time you sign in.');
        } catch(error){showErrors(errorBox,error,'Network error. Please try again.');}
        finally{setBusy(this,false);}
    });
});
</script>
@endsection
