@forelse($rows as $row)
    @php
        $assignedCount = $subjectCounts[$row->name] ?? 0;
        $isOther = (int)($row->other_subject ?? 0) === 1;
    @endphp
    <tr data-subject-id="{{ $row->id }}">
        <td class="text-center font-weight-bold" style="color:#002C54; font-size:11px;">
            #{{ $row->id }}
        </td>
        <td>
            <div class="font-weight-bold text-dark" style="font-size:12px;">{{ $row->name }}</div>
        </td>
        <td class="text-center">
            @if($isOther)
                <span class="badge badge-warning" style="font-size:10.5px; padding:2px 7px; font-weight:600;">
                    Other Subject
                </span>
            @else
                <span class="badge badge-primary" style="font-size:10.5px; padding:2px 7px; font-weight:600;">
                    Main Subject
                </span>
            @endif
        </td>
        <td class="text-center">
            <span class="badge badge-light border font-weight-semibold" style="font-size:11px; padding:2px 7px;">
                <i class="fa fa-graduation-cap text-info mr-1"></i> {{ $assignedCount }} {{ Str::plural('Class', $assignedCount) }}
            </span>
        </td>
        <td class="text-center fixed_action_col" style="white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                <button type="button" class="btn-action-icon btn-action-edit js-edit-subject"
                        data-toggle="modal" data-target="#editSubjectModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        data-other="{{ $row->other_subject }}"
                        title="Edit Subject">
                    <i class="fa fa-edit"></i>
                </button>
                <form action="{{ url('delete_create_subject') }}" method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                    @csrf
                    <input type="hidden" name="delete_id" value="{{ $row->id }}">
                    <button type="submit" class="btn-action-icon btn-action-delete" title="Delete Subject">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
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