@php
    $i = $startIndex ?? 0;
    $schoolName = $setting->name ?? 'Our School';
    $loginUrl = url('/');
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $item)
        @php
            $fullName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
            $hasCred = !empty($item->userName) && !empty($item->confirm_password);
            $mobileClean = preg_replace('/\D/', '', (string)($item->mobile ?? ''));
            if (strlen($mobileClean) > 10) {
                $mobileClean = substr($mobileClean, -10);
            }
            
            // Pre-built WhatsApp message for parents
            $waMsg = "Dear Parent,\nLogin credentials for {$fullName} (Class: " . ($item->class_name ?? '-') . "):\nUsername: " . ($item->userName ?: 'N/A') . "\nPassword: " . ($item->confirm_password ?: 'N/A') . "\nLogin Portal: {$loginUrl}\nRegards,\n{$schoolName}";
            $waUrl = !empty($mobileClean) ? "https://api.whatsapp.com/send?phone=91{$mobileClean}&text=" . urlencode($waMsg) : '#';
        @endphp
        <tr class="credential-row {{ $hasCred ? 'row-cred-active' : 'row-cred-pending' }}" 
            data-id="{{ $item->id }}"
            data-name="{{ strtolower($fullName) }}" 
            data-class="{{ strtolower($item->class_name ?? '') }}" 
            data-guardian="{{ strtolower($item->father_name ?? '') }}" 
            data-mobile="{{ $mobileClean }}"
            data-username="{{ strtolower($item->userName ?? '') }}"
            data-status="{{ $hasCred ? 'generated' : 'pending' }}">
            
            {{-- 1. Checkbox --}}
            <td class="text-center check-cell">
                <input type="checkbox" class="student-check" value="{{ $item->id }}" 
                       data-name="{{ $fullName }}" 
                       data-class="{{ $item->class_name ?? '' }}"
                       data-mobile="{{ $mobileClean }}"
                       data-username="{{ $item->userName ?? '' }}"
                       data-password="{{ $item->confirm_password ?? '' }}"
                       title="Select {{ $fullName }}">
            </td>

            {{-- 2. Serial Number --}}
            <td class="text-center serial-cell font-weight-bold text-muted">{{ ++$i }}</td>

            {{-- 3. Student Details --}}
            <td>
                <div class="student-info-box">
                    <span class="student-name-text">{{ $fullName ?: '-' }}</span>
                    <div class="student-sub-info">
                        @if(!empty($item->admissionNo))
                            <span class="badge-adm-no" title="Admission Number">Adm: {{ $item->admissionNo }}</span>
                        @endif
                        @if(!empty($item->attendance_unique_id))
                            <span class="badge-unique-id" title="Attendance Unique ID">ID: {{ $item->attendance_unique_id }}</span>
                        @endif
                    </div>
                </div>
            </td>

            {{-- 4. Class --}}
            <td class="text-center">
                <span class="badge-class">{{ $item->class_name ?: '-' }}</span>
            </td>

            {{-- 5. Father / Guardian --}}
            <td>
                <span class="guardian-name">{{ $item->father_name ?: '-' }}</span>
            </td>

            {{-- 6. Mobile & WhatsApp Share --}}
            <td>
                <div class="mobile-cell-wrap">
                    <span class="mobile-text">{{ $item->mobile ?: '-' }}</span>
                    @if(!empty($mobileClean))
                        <a href="{{ $waUrl }}" target="_blank" class="btn-row-wa" title="Send Credentials via WhatsApp">
                            <i class="fa fa-whatsapp"></i>
                        </a>
                    @endif
                </div>
            </td>

            {{-- 7. Username with 1-Click Copy --}}
            <td>
                <div class="cred-cell-wrap">
                    @if(!empty($item->userName))
                        <span class="cred-pill cred-pill-user" title="Username">{{ $item->userName }}</span>
                        <button type="button" class="btn-copy-icon copy-btn" data-copy="{{ $item->userName }}" title="Copy Username">
                            <i class="fa fa-clone"></i>
                        </button>
                    @else
                        <span class="cred-pill cred-pill-empty">Not Set</span>
                    @endif
                </div>
            </td>

            {{-- 8. Password with Eye Reveal Toggle & 1-Click Copy --}}
            <td>
                <div class="cred-cell-wrap">
                    @if(!empty($item->confirm_password))
                        <span class="cred-pill cred-pill-pass pass-masked" 
                              data-plain="{{ $item->confirm_password }}" 
                              data-masked="••••••••" 
                              title="Click eye to reveal">••••••••</span>
                        <button type="button" class="btn-toggle-eye" title="Reveal / Hide Password">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn-copy-icon copy-btn" data-copy="{{ $item->confirm_password }}" title="Copy Password">
                            <i class="fa fa-clone"></i>
                        </button>
                    @else
                        <span class="cred-pill cred-pill-empty">Not Set</span>
                    @endif
                </div>
            </td>

            {{-- 9. Status Pill --}}
            <td class="text-center">
                @if($hasCred)
                    <span class="badge-status-pill badge-status-active" title="Login Credentials Active">
                        <i class="fa fa-check-circle mr-1"></i> Generated
                    </span>
                @else
                    <span class="badge-status-pill badge-status-pending" title="Credentials Not Yet Generated">
                        <i class="fa fa-clock-o mr-1"></i> Pending
                    </span>
                @endif
            </td>

            {{-- 10. Fixed Action Column --}}
            <td class="text-center fixed_action_col">
                <div class="table-actions">
                    @if($hasCred)
                        <button type="button" class="table-btn btn-action-copy-both copy-both-btn" 
                                data-name="{{ $fullName }}" 
                                data-class="{{ $item->class_name ?? '' }}" 
                                data-user="{{ $item->userName }}" 
                                data-pass="{{ $item->confirm_password }}" 
                                title="Copy Username & Password">
                            <i class="fa fa-clipboard"></i>
                        </button>
                    @endif

                    @if(!empty($mobileClean) && $hasCred)
                        <a href="{{ $waUrl }}" target="_blank" class="table-btn btn-action-wa" title="Share Credentials on WhatsApp">
                            <i class="fa fa-whatsapp"></i>
                        </a>
                    @endif

                    <a href="{{ url('studentDetail/'.$item->id) }}" class="table-btn btn-action-view" title="View Student Details">
                        <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
@else
    {{-- Centered Empty State matching Arise ERP design --}}
    <tr id="empty-state-row">
        <td colspan="10" class="p-0">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-key"></i></div>
                <div class="empty-title">No Login Credentials Found</div>
                <div class="empty-desc">There are no student credential records matching your current filter criteria or active session.</div>
            </div>
        </td>
    </tr>
@endif