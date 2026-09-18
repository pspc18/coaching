@php
    $start = $startIndex ?? 0;
@endphp

@forelse($rows as $index => $row)
    @php
        $status = (string) $row->status;
        $rowClass = match($status) {
            '2' => 'row-pending',
            '1' => 'row-approved',
            '0' => 'row-rejected',
            '3' => 'row-cancelled',
            default => ''
        };
    @endphp
    <tr class="leave-row {{ $rowClass }}">
        {{-- S.No. --}}
        <td class="text-center font-weight-bold text-muted">{{ $start + $index + 1 }}</td>

        {{-- Type --}}
        <td class="text-center">
            @if(($row->resolved_user_type ?? strtolower((string)$row->user_type)) === 'student')
                <span class="badge badge-type badge-type-student">
                    <i class="fa fa-graduation-cap mr-1"></i> STUDENT
                </span>
            @else
                <span class="badge badge-type badge-type-staff">
                    <i class="fa fa-briefcase mr-1"></i> STAFF
                </span>
            @endif
        </td>

        {{-- Name --}}
        <td>
            <span class="font-weight-bold text-dark">{{ $row->person_name ?? '-' }}</span>
        </td>

        {{-- Attendance Unique ID --}}
        <td class="text-center font-monospace font-weight-bold text-primary">
            {{ $row->resolved_attendance_id ?? $row->attendance_unique_id ?? '-' }}
        </td>

        {{-- From Date --}}
        <td class="text-center">
            {{ !empty($row->from_date) ? date('d/m/Y', strtotime($row->from_date)) : '-' }}
        </td>

        {{-- To Date --}}
        <td class="text-center">
            {{ !empty($row->to_date) ? date('d/m/Y', strtotime($row->to_date)) : '-' }}
        </td>

        {{-- Reason --}}
        <td>
            <span class="text-truncate d-inline-block" style="max-width: 260px;" title="{{ $row->reason }}">
                {{ $row->reason ?: '-' }}
            </span>
        </td>

        {{-- Status --}}
        <td class="text-center">
            <span class="badge {{ $row->status_class }}">{{ $row->status_label }}</span>
        </td>

        {{-- Actions --}}
        <td class="text-center fixed_action_col">
            <div class="d-inline-flex flex-wrap align-items-center justify-content-center" style="gap: 4px;">
                @if($status === '2')
                    <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="d-inline m-0">
                        {{ csrf_field() }}
                        <input type="hidden" name="leave_id" value="{{ $row->id }}">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-sm btn-success" title="Approve Request" onclick="return confirm('Approve this leave request?')">
                            <i class="fa fa-check"></i> Approve
                        </button>
                    </form>
                    <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="d-inline m-0">
                        {{ csrf_field() }}
                        <input type="hidden" name="leave_id" value="{{ $row->id }}">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn btn-sm btn-danger" title="Reject Request" onclick="return confirm('Reject this leave request?')">
                            <i class="fa fa-times"></i> Reject
                        </button>
                    </form>
                @endif

                @if($status === '1')
                    <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="d-inline m-0">
                        {{ csrf_field() }}
                        <input type="hidden" name="leave_id" value="{{ $row->id }}">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-sm btn-warning text-dark font-weight-bold" title="Cancel Approved Leave" onclick="return confirm('Cancel this approved leave?')">
                            <i class="fa fa-ban"></i> Cancel
                        </button>
                    </form>
                @endif

                <form method="post" action="{{ url('attendance/leave/approvals/action') }}" class="d-inline m-0">
                    {{ csrf_field() }}
                    <input type="hidden" name="leave_id" value="{{ $row->id }}">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Leave Request" onclick="return confirm('Delete this leave request permanently?')">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="9" class="p-0 text-center">
            <div class="dash-empty-state">
                <div class="empty-icon" style="font-size: 38px; color: #94a3b8; margin-bottom: 8px;"><i class="fa fa-folder-open-o"></i></div>
                <div class="empty-title">No Leave Requests Found</div>
                <div class="empty-desc">No records match your selected status or filter criteria. Try resetting filters.</div>
            </div>
        </td>
    </tr>
@endforelse

