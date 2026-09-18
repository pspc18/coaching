@php
    $i = $startIndex ?? 0;
    $imageShowPath = env('IMAGE_SHOW_PATH');
    $userPermission = Helper::permissioncheck(6);
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $item)
        @php
            $fullName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
            $roleName = $item['roleName']['name'] ?? 'Staff';
            $mobileClean = preg_replace('/\D/', '', (string)($item['mobile'] ?? ''));
            if (strlen($mobileClean) > 10) {
                $mobileClean = substr($mobileClean, -10);
            }
            $branchIds = array_filter(explode(',', (string)($item['access_branch_id'] ?? '')));
            $userStatus = (int) ($item->status ?? 1);
            $isTeacher = (int) ($item->role_id ?? 0) === 2;
            $rawImage = trim((string)($item['image'] ?? ''));
            $hasImage = !empty($rawImage) && preg_match('/\.(jpg|jpeg|png|webp|gif|svg)$/i', $rawImage);
            $firstLetter = !empty($item['first_name']) 
                ? strtoupper(mb_substr(trim($item['first_name']), 0, 1)) 
                : (!empty($fullName) ? strtoupper(mb_substr($fullName, 0, 1)) : 'U');
            
            $avatarColors = [
                '#002C54', '#0284c7', '#059669', '#d97706', '#7c3aed',
                '#db2777', '#dc2626', '#0891b2', '#4f46e5', '#ca8a04'
            ];
            $bgColor = $avatarColors[abs(crc32($firstLetter)) % count($avatarColors)];
        @endphp
        <tr class="user-row {{ $userStatus === 1 ? 'row-user-active' : ($userStatus === 2 ? 'row-user-dropped' : 'row-user-inactive') }}"
            data-id="{{ $item->id }}"
            data-name="{{ strtolower($fullName) }}"
            data-role="{{ strtolower($roleName) }}"
            data-mobile="{{ $mobileClean }}"
            data-email="{{ strtolower($item['email'] ?? '') }}"
            data-username="{{ strtolower($item['userName'] ?? '') }}"
            data-status="{{ $userStatus }}">

            {{-- 1. Serial Number --}}
            <td class="text-center serial-cell font-weight-bold text-muted">{{ ++$i }}</td>

            {{-- 2. Profile Photo or First Letter Avatar (No default image network request) --}}
            <td class="text-center p-1">
                @if($hasImage)
                    <img class="profileImg user-avatar-thumb pointer" 
                         src="{{ $imageShowPath . 'profile/' . $rawImage }}" 
                         data-img="{{ $imageShowPath . 'profile/' . $rawImage }}"
                         alt="{{ $fullName }}"
                         title="Click to view image"
                         onerror="this.style.display='none'; var s=this.nextElementSibling; if(s){ s.style.display='inline-flex'; }">
                    <span class="user-avatar-initial pointer" style="display:none; background-color: {{ $bgColor }};" title="{{ $fullName }}">
                        {{ $firstLetter }}
                    </span>
                @else
                    <span class="user-avatar-initial" style="background-color: {{ $bgColor }};" title="{{ $fullName }}">
                        {{ $firstLetter }}
                    </span>
                @endif
            </td>

            {{-- 3. Staff Details (Name, Unique ID, Branch & Mobile/WhatsApp) --}}
            <td>
                <div class="user-name-box">
                    <span class="user-name-text">{{ $fullName ?: '-' }}</span>
                    <div class="user-sub-info">
                        @if(!empty($item['attendance_unique_id']))
                            <span class="badge-biomax" title="BioMax / Attendance ID">ID: {{ $item['attendance_unique_id'] }}</span>
                        @endif
                        @if(!empty($item['mobile']))
                            <div class="mobile-cell-wrap" title="Mobile & WhatsApp">
                                <span class="mobile-text">{{ $item['mobile'] }}</span>
                                @if(!empty($mobileClean))
                                    <a href="https://api.whatsapp.com/send?phone=91{{ $mobileClean }}" target="_blank" class="btn-row-wa" title="Send WhatsApp to {{ $fullName }}">
                                        <i class="fa fa-whatsapp"></i>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </td>

            {{-- 4. Role & Access Branches --}}
            <td>
                <span class="badge-role">{{ $roleName }}</span>
                @if(!empty($branchIds))
                    <div class="access-branches-wrap">
                        @foreach($branchIds as $bId)
                            @if(isset($branchLookup[$bId]))
                                <span class="badge-access-branch" title="Branch Access">{{ $branchLookup[$bId] }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </td>

            {{-- 6. Email --}}
            <td>
                <span class="email-text" title="{{ $item['email'] ?? '-' }}">{{ $item['email'] ?: '-' }}</span>
            </td>

            {{-- 7. Username with Copy --}}
            <td>
                <div class="cred-cell-wrap">
                    @if(!empty($item['userName']))
                        <span class="cred-pill cred-pill-user" title="Username">{{ $item['userName'] }}</span>
                        <button type="button" class="btn-copy-icon copy-btn" data-copy="{{ $item['userName'] }}" title="Copy Username">
                            <i class="fa fa-clone"></i>
                        </button>
                    @else
                        <span class="text-muted font-italic">-</span>
                    @endif
                </div>
            </td>

            {{-- 8. Password with Eye Reveal Toggle & Copy --}}
            <td>
                <div class="cred-cell-wrap">
                    @if(!empty($item['confirm_password']))
                        <span class="cred-pill cred-pill-pass pass-masked" 
                              data-plain="{{ $item['confirm_password'] }}" 
                              data-masked="••••••••" 
                              title="Click eye to reveal">••••••••</span>
                        <button type="button" class="btn-toggle-eye" title="Reveal / Hide Password">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn-copy-icon copy-btn" data-copy="{{ $item['confirm_password'] }}" title="Copy Password">
                            <i class="fa fa-clone"></i>
                        </button>
                    @else
                        <span class="text-muted font-italic">-</span>
                    @endif
                </div>
            </td>

            {{-- 9. Status --}}
            <td class="text-center">
                @if(!$isTeacher)
                    @if($userStatus === 1)
                        <button type="button" class="badge-status-pill badge-status-active userStatus border-0 pointer" 
                                data-bs-toggle="modal" data-bs-target="#statusModal" data-toggle="modal" data-target="#statusModal"
                                data-id="{{ $item['id'] }}" data-status="0" title="Click to Deactivate">
                            <i class="fa fa-check-circle mr-1"></i> Active
                        </button>
                    @else
                        <button type="button" class="badge-status-pill badge-status-inactive userStatus border-0 pointer" 
                                data-bs-toggle="modal" data-bs-target="#statusModal" data-toggle="modal" data-target="#statusModal"
                                data-id="{{ $item['id'] }}" data-status="1" title="Click to Activate">
                            <i class="fa fa-times-circle mr-1"></i> Inactive
                        </button>
                    @endif
                @else
                    <select name="status" data-id="{{ $item['id'] }}" 
                            class="statusDrop form-select-status {{ $userStatus === 1 ? 'status-opt-active' : ($userStatus === 2 ? 'status-opt-dropped' : 'status-opt-inactive') }}"
                            title="Teacher Status">
                        <option value="1" {{ $userStatus === 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $userStatus === 0 ? 'selected' : '' }}>Inactive</option>
                        <option value="2" {{ $userStatus === 2 ? 'selected' : '' }}>Dropped</option>
                    </select>
                @endif
            </td>

            {{-- 10. Time Table --}}
            <td class="text-center">
                @if($isTeacher)
                    <button type="button" class="dash-btn dash-btn-sm dash-btn-primary timeTable"
                            data-user_id="{{ $item->id }}"
                            data-userName="{{ $fullName }}"
                            title="Manage Teacher Timetable">
                        <i class="fa fa-clock-o mr-1"></i> Timetable
                    </button>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            {{-- 11. Fixed Actions Column --}}
            <td class="text-center fixed_action_col">
                <div class="table-actions">
                    {{-- Copy Both Credentials --}}
                    @if(!empty($item['userName']) && !empty($item['confirm_password']))
                        <button type="button" class="table-btn btn-action-copy-both copy-both-btn" 
                                data-name="{{ $fullName }}" 
                                data-role="{{ $roleName }}" 
                                data-user="{{ $item['userName'] }}" 
                                data-pass="{{ $item['confirm_password'] }}" 
                                title="Copy Full Login Credentials">
                            <i class="fa fa-clipboard"></i>
                        </button>
                    @endif

                    {{-- View User Permissions --}}
                    @if($userPermission->add ?? true)
                        <button type="button" class="table-btn btn-action-perm view-user-permissions" 
                                data-role="{{ $item['role_id'] }}" 
                                data-user="{{ $item['id'] }}" 
                                title="Manage User Permissions">
                            <i class="fa fa-shield"></i>
                        </button>
                    @endif

                    {{-- More Actions Dropdown --}}
                    @if(Session::get('role_id') != 3)
                        <div class="dropdown d-inline-block">
                            <button type="button" class="table-btn btn-action-menu" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false" title="More Options">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item py-1 px-2 font-size-11" href="{{ url('relieving_letter_print_user/'.$item->id) }}" target="_blank">
                                        <i class="fa fa-print text-secondary mr-2"></i> Relieving Letter
                                    </a>
                                </li>
                                @if($userPermission->print ?? true)
                                    <li>
                                        <a class="dropdown-item py-1 px-2 font-size-11" href="{{ url('joining_letter_print_user/'.$item->id) }}" target="_blank">
                                            <i class="fa fa-file-text-o text-info mr-2"></i> Joining Letter
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1 px-2 font-size-11" href="{{ url('users_idCard/'.$item->id) }}" target="_blank">
                                            <i class="fa fa-id-card-o text-success mr-2"></i> ID Card Print
                                        </a>
                                    </li>
                                @endif
                                @if($userPermission->edit ?? true)
                                    <li>
                                        <a class="dropdown-item py-1 px-2 font-size-11" href="{{ url('editUser', $item['id']) }}">
                                            <i class="fa fa-pencil text-primary mr-2"></i> Edit Staff
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-1 px-2 font-size-11 text-danger deleteData" href="javascript:;" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#Modal_id" data-toggle="modal" data-target="#Modal_id">
                                        <i class="fa fa-trash-o mr-2"></i> Delete Staff
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    {{-- Centered Empty State matching Arise ERP design --}}
    <tr id="empty-state-row">
        <td colspan="10" class="p-0">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-users"></i></div>
                <div class="empty-title">No Staff / Users Found</div>
                <div class="empty-desc">There are no user records matching your current filter criteria or active session.</div>
            </div>
        </td>
    </tr>
@endif