@php
    $permissionTypes = ['add','edit','view','delete','status','print'];
@endphp

<style>
.perm-matrix-wrapper {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.perm-toolbar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.perm-table-container {
    max-height: 65vh;
    overflow-y: auto;
    overflow-x: auto;
    background: #ffffff;
}
.perm-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.perm-table thead th {
    background: #002C54;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 8px 10px;
    border: 1px solid #08335c;
    position: sticky;
    top: 0;
    z-index: 20;
    white-space: nowrap;
}
.perm-table thead th.col-action-type {
    text-align: center;
    width: 90px;
}
.perm-table tbody td {
    padding: 7px 10px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    vertical-align: middle;
}
.perm-table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}
.perm-table tbody tr:hover {
    background-color: #f0f9ff !important;
}

/* Checkbox styling */
.custom-chk {
    cursor: pointer;
    width: 15px;
    height: 15px;
    accent-color: #0284c7;
    vertical-align: middle;
}

/* Action Checkbox Types */
.custom-chk.add { accent-color: #10b981; }
.custom-chk.edit { accent-color: #0284c7; }
.custom-chk.view { accent-color: #06b6d4; }
.custom-chk.delete { accent-color: #ef4444; }
.custom-chk.status { accent-color: #8b5cf6; }
.custom-chk.print { accent-color: #f59e0b; }

.module-title-cell {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.module-name {
    font-weight: 700;
    color: #0f172a;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}
.submodule-btn {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 2px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    font-weight: 600;
}
.submodule-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
}

/* Modal Footer Toolbar */
.perm-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
</style>

<form id="permissionForm" method="post" action="{{ url("role/permission/$role_id") }}" class="perm-matrix-wrapper">
    @csrf
    <input type="hidden" name="role_id" value="{{ $role_id }}" />

    {{-- Top Toolbar --}}
    <div class="perm-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">
                <i class="fa fa-sliders text-primary mr-1"></i> Quick Controls:
            </span>
            <button type="button" class="btn btn-xs btn-outline-primary" id="btnCheckAllPerms">
                <i class="fa fa-check-square-o mr-1"></i> Grant Full Access
            </button>
            <button type="button" class="btn btn-xs btn-outline-info" id="btnCheckViewOnly">
                <i class="fa fa-eye mr-1"></i> View Only
            </button>
            <button type="button" class="btn btn-xs btn-outline-secondary" id="btnClearAllPerms">
                <i class="fa fa-times-circle mr-1"></i> Clear All
            </button>
        </div>
        <div style="font-size: 11px; color: #64748b;">
            <i class="fa fa-info-circle mr-1"></i> Check column headers to grant privilege across all modules.
        </div>
    </div>

    {{-- Permissions Table --}}
    <div class="perm-table-container">
        <table class="perm-table" id="permissionMatrixTable">
            <thead>
                <tr>
                    <th style="min-width: 260px;">
                        <div class="d-flex align-items-center gap-2">
                            <input type="checkbox" class="custom-chk" id="masterRowCheckAll" title="Select / Deselect all modules">
                            <span>Module / Screen</span>
                        </div>
                    </th>
                    @foreach($permissionTypes as $type)
                        <th class="col-action-type">
                            <label class="mb-0 d-flex align-items-center justify-content-center gap-1" style="cursor: pointer;" title="Toggle all {{ strtoupper($type) }}">
                                <span>{{ ucfirst($type) }}</span>
                                <input type="checkbox" class="custom-chk check-type {{ $type }}" data-type="{{ $type }}">
                            </label>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $module)
                    @php
                        $modulePermissions = $rolePermissions[$module->id] ?? null;
                        $subModules = $subs[$module->id] ?? collect();
                        $subSelected = $modulePermissions ? explode(',', $modulePermissions->sub_sidebar_id ?? '') : [];
                        $collapseId = 'subModule'.$module->id;
                    @endphp
                    <tr>
                        <td>
                            <div class="module-title-cell">
                                <label class="mb-0 d-inline-flex align-items-center gap-2" style="cursor: pointer;">
                                    <input type="checkbox" 
                                           class="custom-chk row-select-all" 
                                           data-module-id="{{ $module->id }}" 
                                           id="rowSelect{{ $module->id }}">
                                    <span class="module-name">
                                        <i class="{{ $module->ican ?? 'fa fa-folder-o' }} text-primary"></i> 
                                        {{ $module->name }}
                                    </span>
                                </label>
                                @if($subModules->isNotEmpty())
                                    <button class="submodule-btn" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                        <i class="fa fa-sitemap mr-1"></i> Sub-modules ({{ $subModules->count() }})
                                    </button>
                                @endif
                            </div>

                            @if($subModules->isNotEmpty())
                                <div class="collapse mt-2" id="{{ $collapseId }}">
                                    <div style="background: #f1f5f9; padding: 6px; border-radius: 2px; border: 1px solid #cbd5e1;">
                                        <label style="font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 2px; display: block;">
                                            Allowed Sub-Modules:
                                        </label>
                                        <select 
                                            class="form-control form-control-sm select2-multiple" 
                                            name="sub_modules[{{ $module->id }}][]" 
                                            multiple="multiple"
                                            data-placeholder="Select Sub Modules"
                                            style="width: 100%;"
                                        >
                                            @foreach($subModules as $sub)
                                                <option value="{{ $sub->id }}" {{ in_array($sub->id, $subSelected) ? 'selected' : '' }}>
                                                    {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </td>

                        @foreach($permissionTypes as $type)
                            <td class="text-center">
                                <input type="checkbox" 
                                       class="custom-chk permission-checkbox {{ $type }}" 
                                       data-module-id="{{ $module->id }}"  
                                       name="modules[{{ $module->id }}][]"  
                                       value="{{ $type }}" 
                                       {{ $modulePermissions && $modulePermissions->$type ? 'checked' : '' }}>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer Actions --}}
    <div class="perm-footer">
        <div style="font-size: 11px; color: #64748b;">
            <i class="fa fa-lock mr-1"></i> Changes will immediately take effect on next login/page reload for users with this role.
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="dash-btn dash-btn-outline text-dark" data-bs-dismiss="modal" style="border: 1px solid #cbd5e1; background: #fff;">
                Cancel
            </button>
            <button type="submit" class="dash-btn dash-btn-primary" id="btnSavePermissions">
                <i class="fa fa-save mr-1"></i> Save Role Permissions
            </button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize select2
    if ($.fn.select2) {
        $('.select2-multiple').select2({
            width: '100%',
            closeOnSelect: false,
            allowClear: true,
            placeholder: 'Select sub modules'
        });
    }

    // Grant Full Access (Check Everything)
    $('#btnCheckAllPerms').on('click', function() {
        $('.permission-checkbox').prop('checked', true);
        $('.row-select-all, .check-type, #masterRowCheckAll').prop('checked', true);
        $('select.select2-multiple option').prop('selected', true);
        if ($.fn.select2) {
            $('select.select2-multiple').trigger('change');
        }
    });

    // View Only
    $('#btnCheckViewOnly').on('click', function() {
        $('.permission-checkbox').prop('checked', false);
        $('.permission-checkbox.view').prop('checked', true);
        $('.row-select-all, .check-type, #masterRowCheckAll').prop('checked', false);
        $('.check-type.view').prop('checked', true);
    });

    // Clear All
    $('#btnClearAllPerms').on('click', function() {
        $('.permission-checkbox, .row-select-all, .check-type, #masterRowCheckAll').prop('checked', false);
        $('select.select2-multiple option').prop('selected', false);
        if ($.fn.select2) {
            $('select.select2-multiple').trigger('change');
        }
    });

    // Master Header Check All
    $('#masterRowCheckAll').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.row-select-all').prop('checked', isChecked).trigger('change');
    });

    // AJAX Form Submission
    $('#permissionForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btnSavePermissions');
        var originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'Role Permissions saved successfully!');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Role Permissions saved successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(response.message || 'Role Permissions saved successfully!');
                }
                $('#permissionModal').modal('hide');
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                var errorMsg = 'Failed to save permissions.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }
        });
    });
});
</script>