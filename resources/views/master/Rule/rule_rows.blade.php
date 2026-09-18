@forelse($rows as $row)
    <tr data-rule-id="{{ $row->id }}">
        <td class="text-center font-weight-bold" style="color:#002C54; font-size:11px; width:65px;">
            #{{ $row->id }}
        </td>
        <td style="min-width: 200px;">
            <div class="font-weight-bold text-dark" style="font-size:12px;">{{ $row->name }}</div>
        </td>
        <td class="text-center" style="width: 170px;">
            <span class="badge badge-primary" style="font-size:10.5px; padding:3px 8px; font-weight:600; background:#002C54;">
                <i class="fa fa-user-circle mr-1"></i> {{ $row->role_name ?? 'General / All Roles' }}
            </span>
        </td>
        <td>
            <div class="text-dark" style="font-size:11.5px; line-height:1.45; max-height:48px; overflow:hidden; text-overflow:ellipsis;">
                {!! Str::limit(strip_tags($row->description ?? ''), 130) ?: '<span class="text-muted font-italic">No additional description provided.</span>' !!}
            </div>
        </td>
        <td class="text-center fixed_action_col" style="width: 90px; white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                <button type="button" class="btn-action-icon btn-action-edit js-edit-rule"
                        data-toggle="modal" data-target="#editRuleModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        data-role-id="{{ $row->role_id }}"
                        data-description="{{ htmlspecialchars($row->description ?? '', ENT_QUOTES, 'UTF-8') }}"
                        title="Edit Rule">
                    <i class="fa fa-edit"></i>
                </button>
                <form action="{{ url('rules_delete') }}" method="post" class="js-delete-rule-form" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                    @csrf
                    <input type="hidden" name="delete_id" value="{{ $row->id }}">
                    <button type="submit" class="btn-action-icon btn-action-delete" title="Delete Rule">
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
                <div class="empty-icon"><i class="fa fa-gavel"></i></div>
                <div class="empty-title">No Rules Found</div>
                <div class="empty-desc">No institutional rules or policies match your current search and filter criteria.</div>
            </div>
        </td>
    </tr>
@endforelse