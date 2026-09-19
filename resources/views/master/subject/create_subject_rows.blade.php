@forelse($rows as $row)
    @php
        $assignedCount = $subjectCounts[$row->name] ?? 0;
        $isOther = (int)($row->other_subject ?? 0) === 1;
    @endphp
    <tr data-subject-id="{{ $row->id }}">
        <td class="text-center">
            <span class="badge-subject-id">#{{ $row->id }}</span>
        </td>
        <td>
            <div class="font-weight-bold text-dark" style="font-size:12px;">{{ $row->name }}</div>
        </td>
        <td class="text-center">
            @if($isOther)
                <span class="badge-subject-other">
                    <i class="fa fa-tag mr-1"></i>Other Subject
                </span>
            @else
                <span class="badge-subject-main">
                    <i class="fa fa-check-circle mr-1"></i>Main Subject
                </span>
            @endif
        </td>
        <td class="text-center">
            <span class="badge-assigned-classes {{ $assignedCount > 0 ? 'has-classes' : '' }}">
                <i class="fa fa-graduation-cap mr-1 {{ $assignedCount > 0 ? 'text-success' : 'text-muted' }}"></i>{{ $assignedCount }} {{ Str::plural('Class', $assignedCount) }}
            </span>
        </td>
        <td class="text-center fixed_action_col" style="white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:4px;">
                <button type="button" class="btn-action-icon btn-action-edit js-edit-subject"
                        data-toggle="modal" data-target="#editSubjectModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        data-other="{{ $row->other_subject }}"
                        title="Edit Subject">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn-action-icon btn-action-delete js-delete-subject"
                        data-toggle="modal" data-target="#deleteSubjectModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        title="Delete Subject">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="5">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-book"></i></div>
                <div class="empty-title">No Subjects Found</div>
                <div class="empty-desc">No subjects match your current search or filter criteria.</div>
            </div>
        </td>
    </tr>
@endforelse