@forelse($rows as $row)
    @php
        $studentsCount = $admissionsCounts[$row->id] ?? 0;
        $feesCount = $feesCounts[$row->id] ?? 0;
        $subCount = $subjectCounts[$row->id] ?? 0;
        $isLocked = ($studentsCount > 0 || $feesCount > 0);
    @endphp
    <tr data-class-id="{{ $row->id }}">
        <td class="text-center font-weight-bold" style="color:#002C54; font-size:11px; width:65px;">
            #{{ $row->id }}
        </td>
        <td style="min-width: 180px;">
            <div class="font-weight-bold text-dark" style="font-size:12px;">{{ $row->name }}</div>
        </td>
        <td class="text-center" style="width: 120px;">
            <span class="badge badge-light border text-muted" style="font-size:11px; padding:2px 7px;">
                <i class="fa fa-sort-numeric-asc text-secondary mr-1"></i> {{ $row->orderBy ?? 0 }}
            </span>
        </td>
        <td class="text-center" style="width: 140px;">
            <span class="badge {{ $studentsCount > 0 ? 'badge-primary' : 'badge-light border text-muted' }}" style="font-size:11px; padding:2px 7px; {{ $studentsCount > 0 ? 'background:#002C54;' : '' }}">
                <i class="fa fa-users mr-1"></i> {{ $studentsCount }} {{ Str::plural('Student', $studentsCount) }}
            </span>
        </td>
        <td class="text-center" style="width: 130px;">
            <span class="badge {{ $feesCount > 0 ? 'badge-success' : 'badge-light border text-muted' }}" style="font-size:11px; padding:2px 7px;">
                <i class="fa fa-inr mr-1"></i> {{ $feesCount }} {{ Str::plural('Head', $feesCount) }}
            </span>
        </td>
        <td class="text-center" style="width: 140px;">
            <a href="{{ url('add_subject?class_type_id=' . $row->id) }}" class="badge badge-info" style="font-size:11px; padding:3px 8px; text-decoration:none;" title="View & Manage Subjects">
                <i class="fa fa-book mr-1"></i> {{ $subCount }} Subjects
            </a>
        </td>
        <td class="text-center fixed_action_col" style="width: 90px; white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                <button type="button" class="btn-action-icon btn-action-edit js-edit-class"
                        data-toggle="modal" data-target="#editClassModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        title="Edit Class">
                    <i class="fa fa-edit"></i>
                </button>
                @if(!$isLocked)
                    <form action="{{ url('class_delete') }}" method="post" class="js-delete-class-form" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this class?');">
                        @csrf
                        <input type="hidden" name="delete_id" value="{{ $row->id }}">
                        <button type="submit" class="btn-action-icon btn-action-delete" title="Delete Class">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                @else
                    <button type="button" class="btn-action-icon" style="background:#f1f5f9; color:#94a3b8; border-color:#e2e8f0; cursor:not-allowed;" title="Locked: active students or fees attached" disabled>
                        <i class="fa fa-lock"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="7">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-graduation-cap"></i></div>
                <div class="empty-title">No Classes Found</div>
                <div class="empty-desc">No academic classes found matching criteria. Click "+ Add New Class" or "⚡ Quick Bulk Import" to create classes.</div>
            </div>
        </td>
    </tr>
@endforelse