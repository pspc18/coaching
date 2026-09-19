@php
    $permissionTypes = [
        'add' => 'ADD',
        'edit' => 'EDIT',
        'view' => 'VIEW',
        'delete' => 'DEL',
        'status' => 'STAT',
        'print' => 'PRN'
    ];

    if (!function_exists('resolvePermIcon')) {
        function resolvePermIcon($rawIcon, $default = 'fa fa-circle-o') {
            $icon = trim((string)$rawIcon);
            if (empty($icon)) {
                return $default;
            }

            if (str_starts_with($icon, 'ion-')) {
                return match($icon) {
                    'ion-bag' => 'fa fa-briefcase',
                    'ion-stats-bars' => 'fa fa-bar-chart',
                    'ion-person-add' => 'fa fa-user-plus',
                    'ion-pie-graph' => 'fa fa-pie-chart',
                    default => 'fa fa-circle-o'
                };
            }

            if (str_starts_with($icon, 'fa fa-') || str_starts_with($icon, 'fa ')) {
                return $icon;
            }

            if (str_starts_with($icon, 'fa-')) {
                return 'fa ' . $icon;
            }

            return 'fa fa-' . ltrim($icon, 'fa-');
        }
    }
@endphp

<div class="perm-compact-wrapper" data-role-id="{{ $role_id }}" data-role-name="{{ $role->name ?? 'Role' }}">
    <style>
        .perm-compact-wrapper {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 11.5px;
        }
        /* Segment / Subheader Switcher */
        .perm-subnav-bar {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            flex-shrink: 0;
        }
        .perm-seg-group {
            display: inline-flex;
            align-items: center;
            background: #e2e8f0;
            padding: 2px;
            border-radius: 3px;
            gap: 3px;
        }
        .perm-seg-btn {
            border: none;
            background: transparent;
            color: #475569;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 2px;
            cursor: pointer;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            line-height: 1.2;
        }
        .perm-seg-btn i {
            margin-right: 5px;
        }
        .perm-seg-btn:hover {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.5);
        }
        .perm-seg-btn.active {
            background: #002C54;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }
        .perm-badge-tag {
            font-size: 10.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 2px;
            display: inline-flex;
            align-items: center;
            line-height: 1.2;
            margin-left: 4px;
        }
        .perm-badge-tag i {
            margin-right: 4px;
        }
        .perm-badge-role {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .perm-badge-staff {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        /* Compact Toolbar */
        .perm-compact-toolbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            flex-shrink: 0;
            min-height: 34px;
        }
        .presets-wrap {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
        }
        .perm-btn-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 600;
            border-radius: 2px;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            transition: all .12s ease;
            line-height: 1.2;
            text-decoration: none !important;
        }
        .perm-btn-pill i {
            margin-right: 4px;
        }
        .perm-btn-pill:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .perm-btn-pill.pill-full:hover {
            background: #ecfdf5;
            color: #047857;
            border-color: #10b981;
        }
        .perm-btn-pill.pill-std:hover {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #3b82f6;
        }
        .perm-btn-pill.pill-view:hover {
            background: #f0f9ff;
            color: #0369a1;
            border-color: #0ea5e9;
        }
        .perm-btn-pill.pill-clear:hover {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #ef4444;
        }
        .perm-btn-pill.pill-clone:hover {
            background: #faf5ff;
            color: #7e22ce;
            border-color: #a855f7;
        }
        .perm-compact-search {
            position: relative;
            width: 200px;
        }
        .perm-compact-search input {
            width: 100%;
            height: 25px;
            padding: 2px 8px 2px 26px;
            font-size: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 2px;
            outline: none;
            background: #f8fafc;
        }
        .perm-compact-search input:focus {
            background: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 1px #0284c7;
        }
        .perm-compact-search i {
            position: absolute;
            left: 8px;
            top: 6px;
            font-size: 11px;
            color: #94a3b8;
        }

        /* Compact Table Layout */
        .perm-compact-table-box {
            flex: 1;
            min-height: 220px;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: auto;
            position: relative;
            background: #ffffff;
        }
        .perm-c-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 11.5px;
        }
        .perm-c-table thead th {
            background: #002C54;
            color: #ffffff;
            padding: 5px 8px;
            border: 1px solid #08335c;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
            vertical-align: middle;
        }
        .perm-c-table thead th.col-c-act {
            text-align: center;
            width: 62px;
            min-width: 62px;
            padding: 4px 6px;
        }
        .col-header-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }
        .col-header-title {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .04em;
            line-height: 1;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .perm-c-table tbody td {
            padding: 5px 10px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #f1f5f9;
            vertical-align: middle;
            line-height: 1.3;
        }
        .perm-c-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .perm-c-table tbody tr:hover {
            background-color: #f0f9ff !important;
        }
        .perm-c-table tbody tr.row-hidden {
            display: none !important;
        }

        /* Checkbox & Module Label Styling with Generous Spacing */
        .c-chk {
            cursor: pointer;
            width: 15px;
            height: 15px;
            vertical-align: middle;
            margin: 0;
            accent-color: #0284c7;
        }
        .c-chk.add { accent-color: #10b981; }
        .c-chk.edit { accent-color: #0284c7; }
        .c-chk.view { accent-color: #06b6d4; }
        .c-chk.delete { accent-color: #ef4444; }
        .c-chk.status { accent-color: #8b5cf6; }
        .c-chk.print { accent-color: #f59e0b; }

        .perm-row-label {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            margin-bottom: 0;
            vertical-align: middle;
        }
        .perm-row-label .c-chk {
            margin-right: 8px !important;
        }
        .perm-module-icon {
            width: 16px;
            min-width: 16px;
            text-align: center;
            margin-right: 7px;
            font-size: 12px;
            color: #0284c7;
        }
        .perm-module-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 11.5px;
        }

        /* Submodule Pills & Collapse */
        .btn-sub-badge {
            font-size: 9.5px;
            padding: 2px 7px;
            border-radius: 2px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            cursor: pointer;
            font-weight: 600;
            line-height: 1.2;
            transition: all .12s;
            margin-left: 6px;
        }
        .btn-sub-badge:hover {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Hierarchy Tree Styling */
        .perm-hierarchy-tree {
            margin-top: 5px;
            margin-left: 22px;
            padding: 5px 8px 6px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            position: relative;
        }
        .tree-quick-bar {
            font-size: 9.5px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .tree-quick-title {
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .tree-quick-actions a {
            font-size: 10px;
            text-decoration: none;
        }
        .tree-quick-actions a:hover {
            text-decoration: underline;
        }
        .tree-nodes-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 5px;
            position: relative;
            padding-left: 10px;
            border-left: 1.5px solid #cbd5e1;
        }
        .tree-node-item {
            display: flex;
            align-items: center;
            position: relative;
            padding: 2px 4px 2px 14px;
            font-size: 11px;
            border-radius: 2px;
            transition: all .12s;
        }
        .tree-node-item::before {
            content: "";
            position: absolute;
            left: -10px;
            top: 50%;
            width: 14px;
            height: 1.5px;
            background: #cbd5e1;
        }
        .tree-node-item:hover {
            background: #f1f5f9;
        }
        .tree-node-item.node-highlight {
            background: #fef08a !important;
            font-weight: 600;
        }
        .tree-node-label {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            margin: 0;
            color: #334155;
            font-weight: 500;
        }
        .tree-node-label:hover {
            color: #0284c7;
        }
        .tree-node-label .c-chk {
            margin-right: 6px !important;
            width: 13px;
            height: 13px;
        }
        .tree-node-icon {
            font-size: 11px;
            width: 14px;
            min-width: 14px;
            text-align: center;
            margin-right: 6px;
            color: #0284c7;
            display: inline-block;
        }

        /* Preset Pills additions: Reset & Tree */
        .perm-btn-pill.pill-reset:hover {
            background: #fffbeb;
            color: #b45309;
            border-color: #f59e0b;
        }
        .perm-btn-pill.pill-tree:hover {
            background: #f0fdf4;
            color: #15803d;
            border-color: #22c55e;
        }

        /* Compact Footer & Sync Radios */
        .perm-compact-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            flex-shrink: 0;
        }
        .sync-option-label {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            cursor: pointer;
            margin-bottom: 0;
        }
        .sync-option-label input[type="radio"] {
            margin-right: 4px;
            cursor: pointer;
        }
        .sync-label {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            color: #002C54;
            cursor: pointer;
            margin-bottom: 0;
        }
        .sync-label .c-chk {
            margin-right: 8px !important;
        }
        .sync-label i {
            margin-right: 4px;
        }
        .footer-action-btns {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .user-compact-select-bar {
            background: #eff6ff;
            border-bottom: 1px solid #bfdbfe;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>

    {{-- Subnav Bar: Tab Switcher + Badges --}}
    <div class="perm-subnav-bar">
        <div class="perm-seg-group">
            <button type="button" class="perm-seg-btn active" id="tabBtnRoleMode" data-target="#paneRolePermissions">
                <i class="fa fa-shield"></i>Role Default
            </button>
            <button type="button" class="perm-seg-btn" id="tabBtnUserMode" data-target="#paneUserPermissions">
                <i class="fa fa-user"></i>Staff Custom ({{ $users->count() }})
            </button>
        </div>

        <div class="d-flex align-items-center">
            <span class="perm-badge-tag perm-badge-role">
                <i class="fa fa-shield"></i>{{ $role->name ?? 'Role' }} (#{{ $role_id }})
            </span>
            <span class="perm-badge-tag perm-badge-staff">
                <i class="fa fa-users"></i>{{ $users->count() }} Staff Assigned
            </span>
        </div>
    </div>

    {{-- ==================== TAB 1: ROLE DEFAULT PERMISSIONS ==================== --}}
    <div class="perm-tab-pane" id="paneRolePermissions" style="display: flex; flex-direction: column; flex: 1;">
        <form id="formRolePermissions" method="POST" action="{{ url("role/permission/$role_id") }}" style="display: flex; flex-direction: column; flex: 1; margin: 0;">
            @csrf
            <input type="hidden" name="role_id" value="{{ $role_id }}" />

            {{-- Compact Presets Toolbar --}}
            <div class="perm-compact-toolbar">
                <div class="presets-wrap">
                    <span style="font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; margin-right: 4px;">
                        <i class="fa fa-bolt text-warning mr-1"></i>Presets:
                    </span>
                    <button type="button" class="perm-btn-pill pill-full js-preset-full" data-target-table="#tableRoleMatrix">
                        <i class="fa fa-check text-success"></i>Full
                    </button>
                    <button type="button" class="perm-btn-pill pill-std js-preset-standard" data-target-table="#tableRoleMatrix">
                        <i class="fa fa-pencil text-primary"></i>Standard
                    </button>
                    <button type="button" class="perm-btn-pill pill-view js-preset-view" data-target-table="#tableRoleMatrix">
                        <i class="fa fa-eye text-info"></i>View
                    </button>
                    <button type="button" class="perm-btn-pill pill-clear js-preset-clear" data-target-table="#tableRoleMatrix">
                        <i class="fa fa-times text-danger"></i>Clear
                    </button>
                    <span style="border-left: 1px solid #cbd5e1; height: 16px; margin: 0 4px;"></span>
                    <button type="button" class="perm-btn-pill pill-reset js-btn-reset-initial" data-target-form="#formRolePermissions" title="Revert to initial loaded permissions">
                        <i class="fa fa-undo text-warning"></i>Reset
                    </button>
                    <button type="button" class="perm-btn-pill pill-tree js-toggle-all-hierarchy" data-target-table="#tableRoleMatrix" title="Toggle sub-modules hierarchy tree">
                        <i class="fa fa-sitemap text-primary"></i><span class="tree-toggle-txt">Expand All</span>
                    </button>
                </div>

                {{-- Real-time Search Box --}}
                <div class="perm-compact-search">
                    <i class="fa fa-search"></i>
                    <input type="text" class="js-matrix-search" data-target-table="#tableRoleMatrix" placeholder="Search module or sub-module..." autocomplete="off" />
                </div>
            </div>

            {{-- Compact Table Container --}}
            <div class="perm-compact-table-box">
                <table class="perm-c-table" id="tableRoleMatrix">
                    <thead>
                        <tr>
                            <th style="min-width: 230px;">
                                <label class="mb-0 d-inline-flex align-items-center" style="cursor: pointer;">
                                    <input type="checkbox" class="c-chk js-master-table-check mr-2" title="Toggle all visible modules" />
                                    <span style="font-weight: 700; letter-spacing: .03em; font-size: 10px;">MODULE / SUB-MODULE HIERARCHY</span>
                                </label>
                            </th>
                            @foreach($permissionTypes as $key => $label)
                                <th class="col-c-act">
                                    <div class="col-header-box">
                                        <span class="col-header-title">{{ $label }}</span>
                                        <input type="checkbox" class="c-chk js-col-check {{ $key }}" data-type="{{ $key }}" title="Toggle all {{ $label }}" />
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                            @php
                                $perm = $rolePermissions[$module->id] ?? null;
                                $subModules = $subs[$module->id] ?? collect();
                                $subSelected = $perm ? array_filter(explode(',', $perm->sub_sidebar_id ?? '')) : [];
                                $subBoxId = 'roleSubBox_' . $module->id;
                                $activeSubCount = count(array_intersect($subModules->pluck('id')->map(fn($v) => (string)$v)->toArray(), array_map('strval', $subSelected)));
                            @endphp
                            <tr data-module-id="{{ $module->id }}" data-module-name="{{ strtolower($module->name) }}">
                                <td>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="perm-row-label">
                                            <input type="checkbox" class="c-chk js-row-check" data-module-id="{{ $module->id }}" title="Toggle all for {{ $module->name }}" />
                                            <i class="{{ resolvePermIcon($module->ican, 'fa fa-folder-o') }} perm-module-icon"></i>
                                            <span class="perm-module-title">{{ $module->name }}</span>
                                        </label>

                                        @if($subModules->isNotEmpty())
                                            <div class="d-flex align-items-center" style="gap: 4px;">
                                                <span class="badge badge-light border js-sub-counter-badge" style="font-size: 9.5px; padding: 2px 5px; color: #475569;" title="Active sub-modules count">
                                                    <span class="js-sub-count-active">{{ $activeSubCount }}</span>/{{ $subModules->count() }}
                                                </span>
                                                <button type="button" class="btn-sub-badge js-toggle-sub" data-target="#{{ $subBoxId }}" title="Toggle sub-modules hierarchy tree">
                                                    <i class="fa fa-sitemap mr-1"></i>Sub <i class="fa fa-caret-down ml-1"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    @if($subModules->isNotEmpty())
                                        <div class="perm-hierarchy-tree js-sub-container" id="{{ $subBoxId }}" style="display: none;">
                                            <div class="tree-quick-bar">
                                                <span class="tree-quick-title"><i class="fa fa-level-down mr-1"></i>Sub-screens & Menus:</span>
                                                <div class="tree-quick-actions">
                                                    <a href="javascript:void(0)" class="js-sub-check-all text-primary font-weight-bold mr-2"><i class="fa fa-check-square-o mr-1"></i>Select All</a>
                                                    <a href="javascript:void(0)" class="js-sub-clear-all text-danger font-weight-bold"><i class="fa fa-square-o mr-1"></i>Clear</a>
                                                </div>
                                            </div>
                                            <div class="tree-nodes-list">
                                                @foreach($subModules as $sub)
                                                    <div class="tree-node-item" data-sub-name="{{ strtolower($sub->name) }}">
                                                        <label class="tree-node-label">
                                                            <input type="checkbox" name="sub_modules[{{ $module->id }}][]" value="{{ $sub->id }}" {{ in_array((string)$sub->id, array_map('strval', $subSelected)) ? 'checked' : '' }} class="js-sub-chk c-chk" />
                                                            <i class="{{ resolvePermIcon($sub->ican, 'fa fa-circle-o') }} tree-node-icon"></i>
                                                            <span class="tree-node-name">{{ $sub->name }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                @foreach($permissionTypes as $key => $label)
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" 
                                               class="c-chk js-perm-check {{ $key }}" 
                                               data-module-id="{{ $module->id }}" 
                                               data-type="{{ $key }}" 
                                               name="modules[{{ $module->id }}][]" 
                                               value="{{ $key }}" 
                                               {{ $perm && $perm->$key ? 'checked' : '' }} />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Compact Sticky Footer with Selective Sync & Reset --}}
            <div class="perm-compact-footer">
                <div class="sync-section">
                    @php
                        $totalStaff = $users->count();
                        $customStaffCount = count($customUserIds);
                        $defaultStaffCount = $totalStaff - $customStaffCount;
                    @endphp

                    @if($totalStaff === 0)
                        <div class="text-muted" style="font-size: 11px;">
                            <i class="fa fa-info-circle mr-1 text-primary"></i>Save as default role template (no staff currently assigned).
                            <input type="hidden" name="sync_mode" value="none" />
                        </div>
                    @elseif($customStaffCount === 0)
                        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                            <span style="font-size: 11px; font-weight: 700; color: #002C54;">
                                <i class="fa fa-refresh mr-1 text-primary"></i>Sync on Save:
                            </span>
                            <label class="sync-option-label mb-0" style="color: #1e40af; font-weight: 600;">
                                <input type="radio" name="sync_mode" value="all" checked class="mr-1" />
                                <span>
                                    @if($totalStaff === 1)
                                        @php
                                            $singleUser = $users->first();
                                            $singleName = trim(($singleUser->first_name ?? '') . ($singleUser->userName ? ' (' . $singleUser->userName . ')' : ''));
                                        @endphp
                                        Sync to <strong>{{ $singleName }}</strong> immediately
                                    @else
                                        Sync to all <strong>{{ $totalStaff }} staff</strong> in this role
                                    @endif
                                </span>
                            </label>
                            <label class="sync-option-label mb-0 text-muted">
                                <input type="radio" name="sync_mode" value="none" class="mr-1" />
                                <span>Role template only (don't sync)</span>
                            </label>
                        </div>
                    @else
                        {{-- When staff with custom overrides exist --}}
                        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                            <span style="font-size: 11px; font-weight: 700; color: #002C54;">
                                <i class="fa fa-refresh mr-1 text-primary"></i>Sync on Save:
                            </span>
                            <label class="sync-option-label mb-0" style="color: #1e40af; font-weight: 600;" title="Update staff who follow role defaults, preserving any customized staff">
                                <input type="radio" name="sync_mode" value="default_only" checked class="mr-1" />
                                <span>
                                    <i class="fa fa-shield text-primary mr-1"></i>Skip Custom: Apply to <strong>{{ $defaultStaffCount }} default staff</strong>
                                    <span class="badge badge-info ml-1" style="font-size: 9px; font-weight: 600;">Skips {{ $customStaffCount }} custom</span>
                                </span>
                            </label>
                            <label class="sync-option-label mb-0 text-dark" title="Overwrite all staff including customized ones">
                                <input type="radio" name="sync_mode" value="all" class="mr-1" />
                                <span>
                                    <i class="fa fa-users text-warning mr-1"></i>Overwrite All ({{ $totalStaff }} staff)
                                </span>
                            </label>
                            <label class="sync-option-label mb-0 text-muted" title="Update role template only">
                                <input type="radio" name="sync_mode" value="none" class="mr-1" />
                                <span>Role template only</span>
                            </label>
                        </div>
                    @endif
                </div>

                <div class="footer-action-btns">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border js-btn-reset-initial" data-target-form="#formRolePermissions" style="height:27px; padding:0 12px; font-size:11px;" title="Reset permissions back to initial loaded state">
                        <i class="fa fa-undo mr-1 text-warning"></i>Reset
                    </button>
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" data-bs-dismiss="modal" style="height:27px; padding:0 12px; font-size:11px;">
                        <i class="fa fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="dash-btn dash-btn-primary" id="btnSaveRolePerms" style="height:27px; padding:0 14px; font-size:11px; background:#002C54; border-color:#002C54;">
                        <i class="fa fa-check mr-1"></i>Save Role Permissions
                    </button>
                </div>
            </div>
        </form>
    </div>


    {{-- ==================== TAB 2: SPECIFIC USER-WISE PERMISSIONS ==================== --}}
    <div class="perm-tab-pane" id="paneUserPermissions" style="display: none; flex-direction: column; flex: 1;">
        {{-- User Selection Bar --}}
        <div class="user-compact-select-bar">
            <div class="d-flex align-items-center flex-grow-1" style="gap: 8px;">
                <label for="selectTargetStaff" style="font-size: 11px; font-weight: 700; color: #1e40af; margin: 0; white-space: nowrap;">
                    <i class="fa fa-user-circle mr-1"></i>Staff Member:
                </label>
                <select id="selectTargetStaff" class="form-select form-select-sm" style="max-width: 300px; height: 26px; font-size: 11px; border-color: #93c5fd; padding-top: 1px; padding-bottom: 1px;">
                    <option value="">-- Select staff to customize --</option>
                    @foreach($users as $usr)
                        @php
                            $hasCustom = in_array($usr->id, $customUserIds);
                        @endphp
                        <option value="{{ $usr->id }}" data-custom="{{ $hasCustom ? '1' : '0' }}" data-name="{{ $usr->first_name }} {{ $usr->userName }}">
                            {{ $usr->first_name }} ({{ $usr->userName }}) {{ $hasCustom ? '— [Customized]' : '— [Role Default]' }}
                        </option>
                    @endforeach
                </select>

                <div id="userAccessBadge" style="display: none;"></div>
            </div>

            <div class="text-muted" style="font-size: 10.5px;">
                <i class="fa fa-info-circle mr-1"></i>Overrides role default for this specific user.
            </div>
        </div>

        {{-- User Unselected Placeholder --}}
        <div id="userPlaceholder" class="p-4 text-center" style="background: #f8fafc; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 250px;">
            <i class="fa fa-user-plus text-muted mb-2" style="font-size: 28px; opacity: 0.5;"></i>
            <div style="font-weight: 600; color: #475569; font-size: 12.5px;">No Staff Member Selected</div>
            <p class="text-muted mb-0 mt-1" style="font-size: 11px; max-width: 340px;">
                Please select a staff member from the dropdown above to view and customize their individual privileges.
            </p>
        </div>

        {{-- User Permissions Form (Shown when staff selected) --}}
        <form id="formUserPermissions" method="POST" action="" style="display: none; flex-direction: column; flex: 1; margin: 0;">
            @csrf
            <input type="hidden" id="permTargetUserId" name="user_id" value="" />

            {{-- Compact Toolbar for User --}}
            <div class="perm-compact-toolbar">
                <div class="presets-wrap">
                    <button type="button" class="perm-btn-pill pill-clone js-user-clone-role" title="Reset all checkboxes to match current role default matrix">
                        <i class="fa fa-clone" style="color: #9333ea;"></i>Copy Role
                    </button>
                    <span style="border-left: 1px solid #cbd5e1; height: 16px; margin: 0 4px;"></span>
                    <button type="button" class="perm-btn-pill pill-full js-preset-full" data-target-table="#tableUserMatrix">
                        <i class="fa fa-check text-success"></i>Full
                    </button>
                    <button type="button" class="perm-btn-pill pill-std js-preset-standard" data-target-table="#tableUserMatrix">
                        <i class="fa fa-pencil text-primary"></i>Standard
                    </button>
                    <button type="button" class="perm-btn-pill pill-view js-preset-view" data-target-table="#tableUserMatrix">
                        <i class="fa fa-eye text-info"></i>View
                    </button>
                    <button type="button" class="perm-btn-pill pill-clear js-preset-clear" data-target-table="#tableUserMatrix">
                        <i class="fa fa-times text-danger"></i>Clear
                    </button>
                    <span style="border-left: 1px solid #cbd5e1; height: 16px; margin: 0 4px;"></span>
                    <button type="button" class="perm-btn-pill pill-reset js-btn-reset-initial" data-target-form="#formUserPermissions" title="Revert staff checkboxes to loaded state">
                        <i class="fa fa-undo text-warning"></i>Reset
                    </button>
                    <button type="button" class="perm-btn-pill pill-tree js-toggle-all-hierarchy" data-target-table="#tableUserMatrix" title="Toggle sub-modules hierarchy tree">
                        <i class="fa fa-sitemap text-primary"></i><span class="tree-toggle-txt">Expand All</span>
                    </button>
                </div>

                {{-- Real-time Search Box --}}
                <div class="perm-compact-search">
                    <i class="fa fa-search"></i>
                    <input type="text" class="js-matrix-search" data-target-table="#tableUserMatrix" placeholder="Search module or sub-module..." autocomplete="off" />
                </div>
            </div>

            {{-- Compact Table Container for User --}}
            <div class="perm-compact-table-box">
                <table class="perm-c-table" id="tableUserMatrix">
                    <thead>
                        <tr>
                            <th style="min-width: 230px;">
                                <label class="mb-0 d-inline-flex align-items-center" style="cursor: pointer;">
                                    <input type="checkbox" class="c-chk js-master-table-check mr-2" title="Toggle all visible modules" />
                                    <span style="font-weight: 700; letter-spacing: .03em; font-size: 10px;">MODULE / SUB-MODULE HIERARCHY</span>
                                </label>
                            </th>
                            @foreach($permissionTypes as $key => $label)
                                <th class="col-c-act">
                                    <div class="col-header-box">
                                        <span class="col-header-title">{{ $label }}</span>
                                        <input type="checkbox" class="c-chk js-col-check {{ $key }}" data-type="{{ $key }}" title="Toggle all {{ $label }}" />
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                            @php
                                $subModules = $subs[$module->id] ?? collect();
                                $userSubBoxId = 'userSubBox_' . $module->id;
                            @endphp
                            <tr data-module-id="{{ $module->id }}" data-module-name="{{ strtolower($module->name) }}">
                                <td>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="perm-row-label">
                                            <input type="checkbox" class="c-chk js-row-check" data-module-id="{{ $module->id }}" title="Toggle all for {{ $module->name }}" />
                                            <i class="{{ resolvePermIcon($module->ican, 'fa fa-folder-o') }} perm-module-icon"></i>
                                            <span class="perm-module-title">{{ $module->name }}</span>
                                        </label>

                                        @if($subModules->isNotEmpty())
                                            <div class="d-flex align-items-center" style="gap: 4px;">
                                                <span class="badge badge-light border js-sub-counter-badge" style="font-size: 9.5px; padding: 2px 5px; color: #475569;" title="Active sub-modules count">
                                                    <span class="js-sub-count-active">0</span>/{{ $subModules->count() }}
                                                </span>
                                                <button type="button" class="btn-sub-badge js-toggle-sub" data-target="#{{ $userSubBoxId }}" title="Toggle sub-modules hierarchy tree">
                                                    <i class="fa fa-sitemap mr-1"></i>Sub <i class="fa fa-caret-down ml-1"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    @if($subModules->isNotEmpty())
                                        <div class="perm-hierarchy-tree js-sub-container" id="{{ $userSubBoxId }}" style="display: none;">
                                            <div class="tree-quick-bar">
                                                <span class="tree-quick-title"><i class="fa fa-level-down mr-1"></i>Sub-screens & Menus:</span>
                                                <div class="tree-quick-actions">
                                                    <a href="javascript:void(0)" class="js-sub-check-all text-primary font-weight-bold mr-2"><i class="fa fa-check-square-o mr-1"></i>Select All</a>
                                                    <a href="javascript:void(0)" class="js-sub-clear-all text-danger font-weight-bold"><i class="fa fa-square-o mr-1"></i>Clear</a>
                                                </div>
                                            </div>
                                            <div class="tree-nodes-list">
                                                @foreach($subModules as $sub)
                                                    <div class="tree-node-item" data-sub-name="{{ strtolower($sub->name) }}">
                                                        <label class="tree-node-label">
                                                            <input type="checkbox" name="sub_modules[{{ $module->id }}][]" value="{{ $sub->id }}" class="js-user-sub-chk c-chk" />
                                                            <i class="{{ resolvePermIcon($sub->ican, 'fa fa-circle-o') }} tree-node-icon"></i>
                                                            <span class="tree-node-name">{{ $sub->name }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                @foreach($permissionTypes as $key => $label)
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" 
                                               class="c-chk js-perm-check {{ $key }}" 
                                               data-module-id="{{ $module->id }}" 
                                               data-type="{{ $key }}" 
                                               name="modules[{{ $module->id }}][]" 
                                               value="{{ $key }}" />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Compact Sticky Footer for User --}}
            <div class="perm-compact-footer">
                <div style="font-size: 11px; color: #475569;">
                    <i class="fa fa-check text-success mr-1"></i>User-specific permissions override role defaults immediately.
                </div>

                <div class="footer-action-btns">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border js-btn-reset-initial" data-target-form="#formUserPermissions" style="height:27px; padding:0 12px; font-size:11px;" title="Reset staff permissions back to loaded state">
                        <i class="fa fa-undo mr-1 text-warning"></i>Reset
                    </button>
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" data-bs-dismiss="modal" style="height:27px; padding:0 12px; font-size:11px;">
                        <i class="fa fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="dash-btn dash-btn-primary" id="btnSaveUserPerms" style="height:27px; padding:0 14px; font-size:11px; background:#059669; border-color:#059669;">
                        <i class="fa fa-check mr-1"></i>Save User Permissions
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>