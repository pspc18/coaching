@forelse($rows as $row)
    @php
        $studentsCount = $admissionsCounts[$row->id] ?? 0;
        $feesCount = $feesCounts[$row->id] ?? 0;
        $subCount = $subjectCounts[$row->id] ?? 0;
        $isLocked = ($studentsCount > 0 || $feesCount > 0);
    @endphp
    <tr data-class-id="{{ $row->id }}">
        <td class="text-center" style="width:65px;">
            <span class="badge-class-id">#{{ $row->id }}</span>
        </td>
        <td style="min-width: 180px;">
            <div class="font-weight-bold text-dark" style="font-size:12px; line-height: 1.3;">{{ $row->name }}</div>
        </td>
        <td class="text-center" style="width: 120px;">
            <span class="badge-class-order">
                <i class="fa fa-sort-numeric-asc"></i> {{ $row->orderBy ?? 0 }}
            </span>
        </td>
        <td class="text-center" style="width: 140px;">
            <span class="badge-class-students {{ $studentsCount > 0 ? 'has-students' : '' }}">
                <i class="fa fa-users"></i> {{ $studentsCount }} {{ Str::plural('Student', $studentsCount) }}
            </span>
        </td>
        <td class="text-center" style="width: 130px;">
            <span class="badge-class-fees {{ $feesCount > 0 ? 'has-fees' : '' }}">
                <i class="fa fa-inr"></i> {{ $feesCount }} {{ Str::plural('Head', $feesCount) }}
            </span>
        </td>
        <td class="text-center" style="width: 140px;">
            <a href="{{ url('add_subject?class_type_id=' . $row->id) }}" class="badge-class-subjects {{ $subCount > 0 ? 'has-subjects' : '' }}" title="View & Manage Subjects">
                <i class="fa fa-book"></i> {{ $subCount }} Subjects
            </a>
        </td>
        <td class="text-center fixed_action_col" style="width: 90px; white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:4px;">
                <button type="button" class="btn-action-icon btn-action-edit js-edit-class"
                        data-toggle="modal" data-target="#editClassModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        title="Edit Class">
                    <i class="fa fa-edit"></i>
                </button>
                @if(!$isLocked)
                    <button type="button" class="btn-action-icon btn-action-delete js-delete-class-btn"
                            data-id="{{ $row->id }}"
                            data-name="{{ $row->name }}"
                            title="Delete Class">
                        <i class="fa fa-trash"></i>
                    </button>
                @else
                    <button type="button" class="btn-action-icon btn-action-locked" title="Locked: active students or fees attached" disabled>
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