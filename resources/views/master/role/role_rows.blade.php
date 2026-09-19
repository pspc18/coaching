@forelse($rows as $row)
    @php
        $isSystem = (int)$row->id <= 5;
        $assignedUsers = $userCounts[$row->id] ?? 0;
    @endphp
    <tr data-role-id="{{ $row->id }}">
        <td class="text-center">
            <span class="badge-role-id">#{{ $row->id }}</span>
        </td>
        <td>
            <div class="font-weight-bold text-dark" style="font-size:12px;">{{ $row->name }}</div>
        </td>
        <td class="text-center">
            @if($isSystem)
                <span class="badge-role-system">
                    <i class="fa fa-shield"></i> Core System
                </span>
            @else
                <span class="badge-role-custom">
                    <i class="fa fa-tag"></i> Custom Role
                </span>
            @endif
        </td>
        <td class="text-center">
            <span class="badge-staff-count {{ $assignedUsers > 0 ? 'has-users' : '' }}">
                <i class="fa fa-users {{ $assignedUsers > 0 ? 'text-primary' : 'text-muted' }}"></i> {{ $assignedUsers }} {{ Str::plural('Staff', $assignedUsers) }}
            </span>
        </td>
        <td class="text-center">
            <a href="{{ url('role_permission/' . $row->id) }}" class="btn-role-permissions js-btn-permissions" data-role-id="{{ $row->id }}" data-role-name="{{ $row->name }}" title="Configure module access permissions">
                <i class="fa fa-key"></i> <span>Permissions</span>
            </a>
        </td>
        <td class="text-center fixed_action_col" style="white-space:nowrap;">
            <div class="d-inline-flex align-items-center" style="gap:4px;">
                <button type="button" class="btn-action-icon btn-action-edit js-edit-role"
                        data-toggle="modal" data-target="#editRoleModal"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        title="Edit Role Name">
                    <i class="fa fa-edit"></i>
                </button>
                @if(!$isSystem)
                    <button type="button" class="btn-action-icon btn-action-delete js-delete-role"
                            data-toggle="modal" data-target="#deleteRoleModal"
                            data-id="{{ $row->id }}"
                            data-name="{{ $row->name }}"
                            title="Delete Role">
                        <i class="fa fa-trash"></i>
                    </button>
                @else
                    <button type="button" class="btn-action-icon btn-action-disabled" disabled title="Core system roles cannot be deleted">
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