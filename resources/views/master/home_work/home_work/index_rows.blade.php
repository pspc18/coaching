@php
    $i = $startIndex ?? 0;
    $today = date('Y-m-d');
    $permission = \App\Helpers\Helper::permissioncheck(10);
    $currentRoleId = Session::get('role_id');
    $currentTeacherId = Session::get('teacher_id');
    $isStudent = ($currentRoleId == 3);
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $item)
        @php
            // Extract DPP / Worksheet category if prefixed in title
            $type = 'Homework';
            $rawTitle = $item->title ?? '';
            $displayTitle = $rawTitle;
            if (preg_match('/^\[(.*?)\]\s*(.*)$/', $rawTitle, $matches)) {
                $type = trim($matches[1]);
                $displayTitle = trim($matches[2]);
            }

            // Category badge class
            $badgeClass = 'badge-type-general';
            $typeLower = strtolower($type);
            if (strpos($typeLower, 'dpp') !== false) {
                $badgeClass = 'badge-type-dpp';
            } elseif (strpos($typeLower, 'worksheet') !== false) {
                $badgeClass = 'badge-type-worksheet';
            } elseif (strpos($typeLower, 'pyq') !== false) {
                $badgeClass = 'badge-type-pyq';
            } elseif (strpos($typeLower, 'subjective') !== false) {
                $badgeClass = 'badge-type-subjective';
            } elseif (strpos($typeLower, 'revision') !== false) {
                $badgeClass = 'badge-type-revision';
            }

            // Assigned by name
            $assignedByName = 'Admin';
            if (!empty($item->Teacher)) {
                $assignedByName = trim(($item->Teacher->first_name ?? '') . ' ' . ($item->Teacher->last_name ?? ''));
            } elseif (!empty($item->User)) {
                $assignedByName = trim(($item->User->first_name ?? '') . ' ' . ($item->User->last_name ?? ''));
            }
            if (empty($assignedByName)) {
                $assignedByName = 'Admin';
            }

            // Status calculation
            $dueDate = !empty($item->submission_date) ? date('Y-m-d', strtotime($item->submission_date)) : '';
            $isOverdue = (!empty($dueDate) && $dueDate < $today);
            $isDueToday = (!empty($dueDate) && $dueDate == $today);

            // Row permissions
            $itemTeacherId = $item->teacher_id ?? null;
            $canEdit = !$isStudent && (($itemTeacherId == $currentTeacherId) || ($currentRoleId == 1)) && ($permission->edit ?? true);
            $canDelete = !$isStudent && (($itemTeacherId == $currentTeacherId) || ($currentRoleId == 1)) && ($permission->delete ?? true);

            // Safe Base64 description for modal preview
            $encodedDesc = base64_encode($item->description ?? '');
        @endphp
        <tr class="hw-row" data-id="{{ $item->id }}">
            {{-- Sr. No. --}}
            <td class="text-center" style="vertical-align: middle;">
                <span class="row-num font-weight-bold text-muted">{{ ++$i }}</span>
            </td>

            {{-- Category / DPP Type --}}
            <td>
                <span class="badge-hw-type {{ $badgeClass }}">{{ $type }}</span>
            </td>

            {{-- Title / Topic --}}
            <td>
                @php
                    $isLongTitle = mb_strlen($displayTitle) > 30;
                @endphp
                <div class="hw-title-cell" title="{{ $rawTitle }}">
                    @if($isLongTitle)
                        <div class="hw-title-short">
                            <span class="hw-title-text">{{ mb_substr($displayTitle, 0, 30) }}...</span>
                        </div>
                        <div class="hw-title-full" style="display: none;">
                            <span class="hw-title-text-full">{{ $displayTitle }}</span>
                        </div>
                        <button type="button" class="btn-toggle-title" data-expanded="false" title="Click to view full content">
                            <span class="toggle-text">Show More</span> <i class="fa fa-angle-down toggle-icon"></i>
                        </button>
                    @else
                        <div class="hw-title-short">
                            <span class="hw-title-text">{{ $displayTitle }}</span>
                        </div>
                    @endif

                    @if(!empty($item->target_duration))
                        <span class="hw-duration-hint"><i class="fa fa-clock-o mr-1"></i>{{ $item->target_duration }}</span>
                    @endif
                </div>
            </td>

            {{-- Class / Batch --}}
            <td>
                <span class="badge-class">{{ $item->ClassType->name ?? 'N/A' }}</span>
                @if(!empty($item->Section->name))
                    <span class="badge-section ml-1">{{ $item->Section->name }}</span>
                @endif
            </td>

            {{-- Subject --}}
            <td>
                <span class="hw-subject-text">
                    <i class="fa fa-book text-muted mr-1"></i>{{ $item->Subject->name ?? 'N/A' }}
                </span>
            </td>

            {{-- Assigned By --}}
            <td>
                <span class="hw-assigned-by" title="{{ $assignedByName }}">
                    <i class="fa fa-user-circle-o text-muted mr-1"></i>{{ $assignedByName }}
                </span>
            </td>

            {{-- Issue Date --}}
            <td class="text-nowrap">
                <span class="hw-date-label">
                    {{ !empty($item->homework_issue_date) ? date('d M Y', strtotime($item->homework_issue_date)) : '-' }}
                </span>
            </td>

            {{-- Due Date & Status Badge --}}
            <td class="text-nowrap">
                <div class="d-flex flex-column gap-1">
                    <span class="hw-date-label font-weight-600">
                        {{ !empty($item->submission_date) ? date('d M Y', strtotime($item->submission_date)) : '-' }}
                    </span>
                    @if($isOverdue)
                        <span class="badge-hw-status status-overdue"><i class="fa fa-clock-o mr-1"></i>Overdue</span>
                    @elseif($isDueToday)
                        <span class="badge-hw-status status-today"><i class="fa fa-exclamation-circle mr-1"></i>Due Today</span>
                    @else
                        <span class="badge-hw-status status-active"><i class="fa fa-check-circle-o mr-1"></i>Active</span>
                    @endif
                </div>
            </td>

            {{-- Attachment --}}
            <td class="text-center">
                @if(!empty($item->content_file))
                    @php
                        $ext = strtolower(pathinfo($item->content_file, PATHINFO_EXTENSION));
                        $icon = 'fa-file-text-o text-primary';
                        if(in_array($ext, ['pdf'])) $icon = 'fa-file-pdf-o text-danger';
                        elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $icon = 'fa-file-image-o text-success';
                        elseif(in_array($ext, ['doc', 'docx'])) $icon = 'fa-file-word-o text-info';
                    @endphp
                    <a href="{{ asset('schoolimage/homework/' . $item->content_file) }}" 
                       target="_blank" 
                       download 
                       class="hw-file-chip" 
                       title="Download Attachment: {{ $item->content_file }}">
                        <i class="fa {{ $icon }}"></i> <span class="file-ext">{{ strtoupper($ext ?: 'FILE') }}</span>
                    </a>
                @else
                    <span class="text-muted" style="font-size: 11px;">-</span>
                @endif
            </td>

            {{-- Submissions Count (Distinct Students & Visual Progress Bar) --}}
            <td class="text-center">
                @php
                    $submittedCount = $item->submitted_students_count ?? ($item->upload_homework_count ?? 0);
                    $totalClassStudents = $totalStudentsPerClass[$item->class_type_id] ?? 0;
                    $pendingCount = max(0, $totalClassStudents - $submittedCount);
                    $pct = ($totalClassStudents > 0) ? min(100, round(($submittedCount / $totalClassStudents) * 100)) : 0;
                    $barColor = ($pct >= 75) ? 'bg-success' : (($pct >= 40) ? 'bg-primary' : 'bg-warning');
                @endphp
                @if(!$isStudent)
                    <div class="hw-progress-cell mx-auto" title="{{ $submittedCount }} submitted out of {{ $totalClassStudents }} active students in class ({{ $pct }}%)">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <a href="{{ url('homework/details/' . $item->id) }}" class="hw-submission-count" title="Click to view submitted students">
                                <i class="fa fa-users mr-1"></i>{{ $submittedCount }}@if($totalClassStudents > 0)<span class="text-muted font-weight-normal">/{{ $totalClassStudents }}</span>@endif
                            </a>
                            @if($totalClassStudents > 0)
                                <span class="hw-progress-pct">{{ $pct }}%</span>
                            @endif
                        </div>
                        @if($totalClassStudents > 0)
                            <div class="hw-mini-progress">
                                <div class="hw-mini-progress-bar {{ $barColor }}" style="width: {{ $pct }}%;"></div>
                            </div>
                        @endif
                    </div>
                @else
                    <span class="hw-submission-count hw-submission-readonly">
                        <i class="fa fa-paper-plane mr-1"></i>{{ $submittedCount }}
                    </span>
                @endif
            </td>

            {{-- Sticky Action Column --}}
            <td class="text-center fixed_action_col">
                <div class="table-actions">
                    <button type="button" 
                            class="table-btn btn-action-view viewHomeworkBtn" 
                            data-id="{{ $item->id }}" 
                            data-title="{{ htmlspecialchars($rawTitle, ENT_QUOTES) }}" 
                            data-display-title="{{ htmlspecialchars($displayTitle, ENT_QUOTES) }}" 
                            data-type="{{ htmlspecialchars($type, ENT_QUOTES) }}" 
                            data-badge-class="{{ $badgeClass }}" 
                            data-description="{{ $encodedDesc }}" 
                            data-class="{{ $item->ClassType->name ?? 'N/A' }}" 
                            data-section="{{ $item->Section->name ?? '' }}" 
                            data-subject="{{ $item->Subject->name ?? 'N/A' }}" 
                            data-issue-date="{{ !empty($item->homework_issue_date) ? date('d M Y', strtotime($item->homework_issue_date)) : '-' }}" 
                            data-submission-date="{{ !empty($item->submission_date) ? date('d M Y', strtotime($item->submission_date)) : '-' }}" 
                            data-is-overdue="{{ $isOverdue ? '1' : '0' }}" 
                            data-is-today="{{ $isDueToday ? '1' : '0' }}" 
                            data-created-by="{{ htmlspecialchars($assignedByName, ENT_QUOTES) }}" 
                            data-content-file="{{ !empty($item->content_file) ? asset('schoolimage/homework/' . $item->content_file) : '' }}" 
                            data-file-name="{{ $item->content_file ?? '' }}" 
                            data-target-duration="{{ htmlspecialchars($item->target_duration ?? '', ENT_QUOTES) }}" 
                            data-submission-count="{{ $submittedCount }}" 
                            data-submissions-url="{{ url('homework/details/' . $item->id) }}" 
                            title="View Homework Details">
                        <i class="fa fa-eye"></i>
                    </button>

                    @if(!$isStudent && $pendingCount > 0)
                        <button type="button" 
                                class="table-btn btn-action-remind btnRemindDefaulters" 
                                data-id="{{ $item->id }}" 
                                data-title="{{ htmlspecialchars($displayTitle, ENT_QUOTES) }}"
                                data-class="{{ $item->ClassType->name ?? 'N/A' }}"
                                data-pending="{{ $pendingCount }}"
                                title="Send WhatsApp reminder to {{ $pendingCount }} pending students">
                            <i class="fa fa-bell-o"></i>
                        </button>
                    @endif
                    
                    @if($canEdit)
                        <a href="{{ url('homework/edit/' . $item->id) }}" class="table-btn btn-action-edit" title="Edit Homework"><i class="fa fa-edit"></i></a>
                    @endif
                    
                    @if($canDelete)
                        <button type="button" 
                                class="table-btn btn-action-delete deleteHwBtn" 
                                data-id="{{ $item->id }}" 
                                data-title="{{ htmlspecialchars($displayTitle, ENT_QUOTES) }}"
                                data-type="{{ htmlspecialchars($type, ENT_QUOTES) }}"
                                data-badge-class="{{ $badgeClass }}"
                                data-class-sub="{{ htmlspecialchars(($item->ClassType->name ?? 'N/A') . (!empty($item->Section->name) ? ' (' . $item->Section->name . ')' : '') . ' • ' . ($item->Subject->name ?? 'N/A'), ENT_QUOTES) }}"
                                data-date="{{ $item->homework_issue_date ? date('d-m-Y', strtotime($item->homework_issue_date)) : '-' }}"
                                title="Delete Homework">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="11" class="p-0">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-flask"></i></div>
                <div class="empty-title">No Homework Found</div>
                <div class="empty-desc">There are no homework, DPP, or worksheet assignments matching your filter criteria.</div>
            </div>
        </td>
    </tr>
@endif
