@php
    $start = $startIndex ?? 0;
@endphp

@forelse($rows as $index => $row)
    @php
        $status = (string) $row->status;
        $statusInfo = match($status) {
            '2' => ['label' => 'PENDING', 'badge_class' => 'status-badge-pending'],
            '1' => ['label' => 'APPROVED', 'badge_class' => 'status-badge-approved'],
            '0' => ['label' => 'REJECTED', 'badge_class' => 'status-badge-rejected'],
            '3' => ['label' => 'CANCELLED', 'badge_class' => 'status-badge-cancelled'],
            default => ['label' => 'PENDING', 'badge_class' => 'status-badge-pending']
        };

        $isStudent = ($row->resolved_user_type ?? strtolower((string)($row->user_type ?? ''))) === 'student';
        
        $fromTs = !empty($row->from_date) ? strtotime($row->from_date) : null;
        $toTs = !empty($row->to_date) ? strtotime($row->to_date) : null;
        $daysCount = ($fromTs && $toTs && $toTs >= $fromTs) ? (int)(round(($toTs - $fromTs) / 86400) + 1) : 1;
        $daysText = $daysCount > 1 ? "{$daysCount} Days" : "1 Day";

        $fromDateFormatted = $fromTs ? date('d/m/Y', $fromTs) : '-';
        $toDateFormatted = $toTs ? date('d/m/Y', $toTs) : '-';
        $sameDay = ($fromDateFormatted === $toDateFormatted);

        $name = trim($row->person_name ?? 'Unnamed');
        $initials = '';
        $words = preg_split('/\s+/', $name);
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= strtoupper(mb_substr($w, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        if (empty($initials)) $initials = $isStudent ? 'ST' : 'SF';
    @endphp

    <div class="student-mob-card" data-id="{{ $row->id }}">
        
        {{-- Card Header: Avatar, Name, Badges & Status --}}
        <div class="student-card-header">
            <div class="student-avatar-box {{ $isStudent ? 'avatar-student' : 'avatar-staff' }}">
                <span>{{ $initials }}</span>
            </div>

            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">{{ $name }}</span>
                    <span class="student-status-badge {{ $statusInfo['badge_class'] }}">
                        {{ $statusInfo['label'] }}
                    </span>
                </div>

                <div class="student-pills-wrap">
                    @if($isStudent)
                        <span class="student-class-badge">
                            <i class="fa fa-graduation-cap mr-1"></i> STUDENT
                        </span>
                    @else
                        <span class="student-role-staff-badge">
                            <i class="fa fa-briefcase mr-1"></i> STAFF
                        </span>
                    @endif

                    <span class="student-adm-badge">
                        <i class="fa fa-id-badge text-muted mr-1"></i> {{ $row->resolved_attendance_id ?? $row->attendance_unique_id ?? '-' }}
                    </span>

                    <span class="student-sno-badge">#{{ $start + $index + 1 }}</span>
                </div>
            </div>
        </div>

        {{-- Card Meta Grid: Exact structure like Admission View --}}
        <div class="student-meta-grid">
            <div class="student-meta-item">
                <i class="fa fa-calendar text-primary"></i>
                @if($sameDay)
                    <span>{{ $fromDateFormatted }}</span>
                @else
                    <span>{{ $fromDateFormatted }} &rarr; {{ $toDateFormatted }}</span>
                @endif
            </div>

            <div class="student-meta-item">
                <i class="fa fa-clock-o text-muted"></i>
                <span class="font-weight-bold text-dark">{{ $daysText }}</span>
            </div>

            <div class="student-meta-item full-width">
                <i class="fa fa-comment-o text-muted"></i>
                <span class="leave-reason-snippet" title="{{ $row->reason }}">
                    <b>Reason:</b> {{ !empty($row->reason) ? $row->reason : 'No reason mentioned' }}
                </span>
            </div>
        </div>

        {{-- Card Actions Row (Strict 30px height, balanced layout) --}}
        <div class="student-card-actions">
            @if($status === '2')
                {{-- Pending: Approve (Green) & Reject (Red) & Delete (Trash) --}}
                <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="leave-act-form flex-fill" onsubmit="return confirmAction(this, 'Approve Leave', 'Approve leave for {{ addslashes($name) }}?')">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $row->id }}">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="mob-btn-action btn-act-approve">
                        <i class="fa fa-check"></i> Approve
                    </button>
                </form>

                <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="leave-act-form flex-fill" onsubmit="return confirmAction(this, 'Reject Leave', 'Reject leave for {{ addslashes($name) }}?')">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $row->id }}">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="mob-btn-action btn-act-reject">
                        <i class="fa fa-times"></i> Reject
                    </button>
                </form>
            @elseif($status === '1')
                {{-- Approved: Cancel Button (Amber) --}}
                <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="leave-act-form flex-fill" onsubmit="return confirmAction(this, 'Cancel Leave', 'Cancel approved leave for {{ addslashes($name) }}?')">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $row->id }}">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="mob-btn-action btn-act-cancel">
                        <i class="fa fa-ban"></i> Cancel Leave
                    </button>
                </form>
            @endif

            {{-- Delete Action Button --}}
            <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="leave-act-form action-delete-form" onsubmit="return confirmAction(this, 'Delete Leave', 'Permanently delete this leave record for {{ addslashes($name) }}?')">
                @csrf
                <input type="hidden" name="leave_id" value="{{ $row->id }}">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="mob-btn-action btn-act-delete" title="Delete">
                    <i class="fa fa-trash-o"></i>
                </button>
            </form>
        </div>

    </div>
@empty
    <div class="mob-empty-state">
        <div class="mob-empty-icon">
            <i class="fa fa-calendar-check-o"></i>
        </div>
        <div class="mob-empty-title">No Leave Requests Found</div>
        <div class="mob-empty-desc">No records match your selected status or search criteria.</div>
        <button type="button" class="mob-btn-reset-filters" id="btnMobResetEmpty">
            <i class="fa fa-refresh mr-1"></i> Reset Filters
        </button>
    </div>
@endforelse