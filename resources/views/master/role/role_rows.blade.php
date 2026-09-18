@forelse($rows as $row)
    @php
        $isSystem = (int)$row->id <= 5;
        $assignedUsers = $userCounts[$row->id] ?? 0;
    @endphp
    <tr data-role-id="{{ $row->id }}">
        <td class="text-center font-weight-bold" style="color:#002C54; font-size:11px;">
            #{{ $row->id }}
        </td>
        <td>
            <div class="font-weight-bold text-dark" style="font-size:12px;">{{ $row->name }}</div>
        </td>
        <td class="text-center">
            @if($isSystem)
                <span class="badge badge-secondary" style="font-size:10.5px; padding:2px 7px; font-weight:600;">
                    <i class="fa fa-shield mr-1"></i> Core System
                </span>
            @else
                <span class="badge badge-info" style="font-size:10.5px; padding:2px 7px; font-weight:600;">
                    Custom Role
                </span>
            @endif
        </td>
        <td class="text-center">
            <span class="badge badge-light border font-weight-semibold" style="font-size:11px; padding:2px 7px;">
                <i class="fa fa-users text-primary mr-1"></i> {{ $assignedUsers }} {{ Str::plural('Staff', $assignedUsers) }}
            </span>
        </td>
        <td class="text-center">
            <a href="{{ url('role_permission/' . $row->id) }}" class="btn-action-view" style="display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:2px; font-size:11px; font-weight:600; text-decoration:none;" title="Configure module access permissions">
                <i class="fa fa-key"></i> Permissions
            </a>
        </td>
        <td class="text-center fixed_action_col" style="white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                <button type="button" class="btn-action-icon btn-action-edit js-edit-role"
                        data-toggle="modal" data-target="#editRoleModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        title="Edit Role Name">
                    <i class="fa fa-edit"></i>
                </button>
                @if(!$isSystem)
                    <form action="{{ url('role_delete') }}" method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this custom role?');">
                        @csrf
                        <input type="hidden" name="delete_id" value="{{ $row->id }}">
                        <button type="submit" class="btn-action-icon btn-action-delete" title="Delete Role">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                @else
                    <button type="button" class="btn-action-icon" style="background:#f1f5f9; color:#94a3b8; border-color:#e2e8f0; cursor:not-allowed;" disabled title="Core system roles cannot be deleted">
                        <i class="fa fa-lock"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="6">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-user-circle"></i></div>
                <div class="empty-title">No Roles Found</div>
                <div class="empty-desc">No roles match your current search or filter criteria.</div>
            </div>
        </td>
    </tr>
@endforelse