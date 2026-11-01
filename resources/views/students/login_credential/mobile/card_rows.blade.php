@php
    $i = $startIndex ?? 0;
    $schoolName = $setting->name ?? 'Our School';
    $loginUrl = url('/');
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $item)
        @php
            $fullName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
            $firstName = trim($item->first_name ?? '');
            $hasCred = !empty($item->userName) && !empty($item->confirm_password);
            $mobileClean = preg_replace('/\D/', '', (string)($item->mobile ?? ''));
            if (strlen($mobileClean) > 10) {
                $mobileClean = substr($mobileClean, -10);
            }
            $dobFormatted = !empty($item->dob) ? date('dmy', strtotime($item->dob)) : '';
            $initial = strtoupper(mb_substr($fullName ?: 'S', 0, 1, 'UTF-8'));
            
            // Pre-built WhatsApp message with credentials
            $waMsg = "Dear Parent,\nLogin credentials for {$fullName} (Class: " . ($item->class_name ?? '-') . "):\nUsername: " . ($item->userName ?: 'N/A') . "\nPassword: " . ($item->confirm_password ?: 'N/A') . "\nLogin Portal: {$loginUrl}\nRegards,\n{$schoolName}";
            $waUrl = !empty($mobileClean) ? "https://api.whatsapp.com/send?phone=91{$mobileClean}&text=" . urlencode($waMsg) : '#';
        @endphp
        <div class="mob-cred-card {{ $hasCred ? 'card-cred-active' : 'card-cred-pending' }}"
             id="credCard{{ $item->id }}"
             data-id="{{ $item->id }}"
             data-name="{{ strtolower($fullName) }}"
             data-firstname="{{ strtolower($firstName) }}"
             data-class="{{ strtolower($item->class_name ?? '') }}"
             data-guardian="{{ strtolower($item->father_name ?? '') }}"
             data-mobile="{{ $mobileClean }}"
             data-adm="{{ $item->admissionNo ?? '' }}"
             data-dob="{{ $dobFormatted }}"
             data-username="{{ strtolower($item->userName ?? '') }}"
             data-status="{{ $hasCred ? 'generated' : 'pending' }}">
            
            {{-- Top Header Row --}}
            <div class="mob-card-head">
                <div class="mob-check-box">
                    <input type="checkbox" class="student-check" value="{{ $item->id }}"
                           data-id="{{ $item->id }}"
                           data-name="{{ $fullName }}"
                           data-firstname="{{ $firstName }}"
                           data-class="{{ $item->class_name ?? '' }}"
                           data-mobile="{{ $mobileClean }}"
                           data-adm="{{ $item->admissionNo ?? '' }}"
                           data-dob="{{ $dobFormatted }}"
                           data-username="{{ $item->userName ?? '' }}"
                           data-password="{{ $item->confirm_password ?? '' }}"
                           title="Select {{ $fullName }}">
                </div>

                <div class="mob-avatar-box">
                    <span>{{ $initial }}</span>
                </div>

                <div class="mob-head-info">
                    <div class="mob-name-row">
                        <span class="mob-student-name">{{ $fullName ?: '-' }}</span>
                        @if($hasCred)
                            <span class="mob-status-pill pill-active status-badge-{{ $item->id }}"><i class="fa fa-check-circle"></i> Generated</span>
                        @else
                            <span class="mob-status-pill pill-pending status-badge-{{ $item->id }}"><i class="fa fa-clock-o"></i> Pending</span>
                        @endif
                    </div>
                    <div class="mob-pills-row">
                        <span class="mob-class-badge">{{ $item->class_name ?: '-' }}</span>
                        @if(!empty($item->admissionNo))
                            <span class="mob-adm-badge">Adm: {{ $item->admissionNo }}</span>
                        @endif
                        @if(!empty($item->attendance_unique_id))
                            <span class="mob-uid-badge">ID: {{ $item->attendance_unique_id }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Meta Grid --}}
            <div class="mob-meta-grid">
                <div class="mob-meta-item">
                    <i class="fa fa-user-circle-o text-muted"></i>
                    <span class="mob-meta-label">Father:</span>
                    <span class="mob-meta-val">{{ $item->father_name ?: '-' }}</span>
                </div>
                <div class="mob-meta-item">
                    <i class="fa fa-phone text-muted"></i>
                    <span class="mob-meta-label">Mobile:</span>
                    @if(!empty($mobileClean))
                        <a href="tel:{{ $mobileClean }}" class="mob-phone-link">{{ $item->mobile }}</a>
                        <a href="{{ $waUrl }}" target="_blank" class="mob-wa-btn" title="Send Credentials via WhatsApp">
                            <i class="fa fa-whatsapp"></i>
                        </a>
                    @else
                        <span class="mob-meta-val">-</span>
                    @endif
                </div>
            </div>

            {{-- Credentials Section --}}
            <div class="mob-cred-box">
                <div class="mob-cred-item">
                    <span class="mob-cred-title"><i class="fa fa-user text-info mr-1"></i> User:</span>
                    <div class="mob-cred-val-wrap user-val-wrap-{{ $item->id }}">
                        @if(!empty($item->userName))
                            <span class="mob-cred-pill mob-cred-user user-pill-{{ $item->id }}">{{ $item->userName }}</span>
                            <button type="button" class="mob-btn-copy copy-btn" data-copy="{{ $item->userName }}" title="Copy Username">
                                <i class="fa fa-clone"></i>
                            </button>
                        @else
                            <span class="mob-cred-pill mob-cred-empty user-pill-{{ $item->id }}">Not Set</span>
                        @endif
                    </div>
                </div>

                <div class="mob-cred-item">
                    <span class="mob-cred-title"><i class="fa fa-lock text-warning mr-1"></i> Pass:</span>
                    <div class="mob-cred-val-wrap pass-val-wrap-{{ $item->id }}">
                        @if(!empty($item->confirm_password))
                            <span class="mob-cred-pill mob-cred-pass pass-masked pass-pill-{{ $item->id }}"
                                  data-plain="{{ $item->confirm_password }}"
                                  data-masked="••••••••">{{ '••••••••' }}</span>
                            <button type="button" class="mob-btn-eye toggle-pass-btn" title="Toggle Show/Hide Password">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="mob-btn-copy copy-btn" data-copy="{{ $item->confirm_password }}" title="Copy Password">
                                <i class="fa fa-clone"></i>
                            </button>
                        @else
                            <span class="mob-cred-pill mob-cred-empty pass-pill-{{ $item->id }}">Not Set</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Footer Action Row --}}
            <div class="mob-card-footer">
                @if($hasCred)
                    <button type="button" class="mob-card-btn-act btn-edit-pass act-btn-{{ $item->id }}"
                            onclick="openSingleGenerate(this)"
                            data-id="{{ $item->id }}"
                            data-name="{{ $fullName }}"
                            data-firstname="{{ $firstName }}"
                            data-class="{{ $item->class_name ?? '' }}"
                            data-mobile="{{ $mobileClean }}"
                            data-adm="{{ $item->admissionNo ?? '' }}"
                            data-dob="{{ $dobFormatted }}"
                            data-username="{{ $item->userName ?? '' }}"
                            data-password="{{ $item->confirm_password ?? '' }}"
                            title="Reset / Edit Password">
                        <i class="fa fa-key mr-1"></i> Reset / Edit Password
                    </button>
                @else
                    <button type="button" class="mob-card-btn-act btn-gen-now act-btn-{{ $item->id }}"
                            onclick="openSingleGenerate(this)"
                            data-id="{{ $item->id }}"
                            data-name="{{ $fullName }}"
                            data-firstname="{{ $firstName }}"
                            data-class="{{ $item->class_name ?? '' }}"
                            data-mobile="{{ $mobileClean }}"
                            data-adm="{{ $item->admissionNo ?? '' }}"
                            data-dob="{{ $dobFormatted }}"
                            data-username=""
                            data-password=""
                            title="Generate Credentials Now">
                        <i class="fa fa-magic mr-1"></i> Generate Credential
                    </button>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="mob-empty-feed">
        <i class="fa fa-search-minus empty-icon"></i>
        <h6>No Student Records Found</h6>
        <p>Try adjusting your search query or class filter to view student credentials.</p>
    </div>
@endif
