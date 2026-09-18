@forelse($rows as $row)
    @php
        $className = $classTypes[$row->class_type_id]->name ?? 'Unknown Class';
        $isOther = (int)($row->other_subject ?? 0) === 1;
    @endphp
    <tr data-assigned-id="{{ $row->id }}">
        <td class="text-center font-weight-bold" style="color:#002C54; font-size:11px; width:65px;">
            #{{ $row->id }}
            <input type="hidden" name="subject_id[]" value="{{ $row->id }}">
        </td>
        <td style="min-width: 180px;">
            <div class="font-weight-bold text-dark" style="font-size:12px;">{{ $row->name }}</div>
        </td>
        <td class="text-center" style="width: 160px;">
            <span class="badge badge-info" style="font-size:11px; padding:3px 8px; font-weight:600;">
                <i class="fa fa-graduation-cap mr-1"></i> {{ $className }}
            </span>
        </td>
        <td class="text-center" style="width: 130px;">
            @if($isOther)
                <span class="badge badge-warning" style="font-size:10.5px; padding:2px 7px; font-weight:600;">
                    Other Subject
                </span>
            @else
                <span class="badge badge-primary" style="font-size:10.5px; padding:2px 7px; font-weight:600; background:#002C54;">
                    Main Subject
                </span>
            @endif
        </td>
        <td class="text-center" style="width: 100px;">
            <input type="number" name="sort_by[]" class="form-control form-control-sm text-center mx-auto js-sort-input"
                   style="width:65px; height:24px; font-size:11px; padding:2px 4px; border-radius:2px;"
                   value="{{ $row->sort_by ?? 1 }}" min="1" data-id="{{ $row->id }}">
        </td>
        <td class="text-center fixed_action_col" style="width: 70px; white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                <form action="{{ url('delete_subject') }}" method="post" class="js-delete-subject-form" style="display:inline-block;" onsubmit="return confirm('Remove this assigned subject from class?');">
                    @csrf
                    <input type="hidden" name="delete_id" value="{{ $row->id }}">
                    <button type="submit" class="btn-action-icon btn-action-delete" title="Remove Subject from Class">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="6">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-book"></i></div>
                <div class="empty-title">No Subjects Assigned</div>
                <div class="empty-desc">No curriculum subjects are assigned to this class criteria. Click "+ Assign Subjects to Class" above to map subjects.</div>
            </div>
        </td>
    </tr>
@endforelse