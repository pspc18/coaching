<script>
function initPermissionsComponent(container) {
    if (!container) return;

    // Helper: Update sub-module counter badge for a row
    function updateSubBadge(row) {
        if (!row) return;
        const subChks = row.querySelectorAll('.js-sub-chk, .js-user-sub-chk');
        const checkedCount = Array.from(subChks).filter(c => c.checked).length;
        const badgeActive = row.querySelector('.js-sub-count-active');
        if (badgeActive) {
            badgeActive.textContent = checkedCount;
        }
    }

    // Helper: Update all sub-module badges in a table
    function updateAllSubBadges(table) {
        if (!table) return;
        table.querySelectorAll('tbody tr').forEach(row => updateSubBadge(row));
    }

    // Helper: get visible rows in target table
    function getVisibleRows(table) {
        return Array.from(table.querySelectorAll('tbody tr:not(.row-hidden)'));
    }

    // Capture initial state of checkboxes & radios
    function captureInitialState(form) {
        if (!form) return;
        form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(inp => {
            inp.setAttribute('data-initial-checked', inp.checked ? '1' : '0');
        });
    }

    // Reset form inputs back to initial loaded state
    function resetToInitial(form) {
        if (!form) return;
        form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(inp => {
            if (inp.hasAttribute('data-initial-checked')) {
                inp.checked = (inp.getAttribute('data-initial-checked') === '1');
            }
        });

        // Update all sub badges in this form
        const table = form.querySelector('table');
        if (table) updateAllSubBadges(table);

        // Reset search input if any
        const pane = form.closest('.perm-tab-pane');
        if (pane) {
            const searchInput = pane.querySelector('.js-matrix-search');
            if (searchInput) {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
            }
        }

        if (typeof toastr !== 'undefined') {
            toastr.info('Permissions reverted to initial loaded state.');
        }
    }

    // Initialize initial state for Role Form
    const formRole = container.querySelector('#formRolePermissions');
    if (formRole) {
        captureInitialState(formRole);
    }

    // Event listener for Reset buttons
    container.querySelectorAll('.js-btn-reset-initial').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetFormSelector = this.getAttribute('data-target-form');
            const form = container.querySelector(targetFormSelector);
            if (form) {
                resetToInitial(form);
            }
        });
    });

    // 1. Tab Switching (Role vs User Permissions)
    const tabBtns = container.querySelectorAll('.perm-seg-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const targetPaneId = this.getAttribute('data-target');
            container.querySelectorAll('.perm-tab-pane').forEach(pane => {
                pane.style.display = 'none';
            });
            const activePane = container.querySelector(targetPaneId);
            if (activePane) {
                activePane.style.display = 'flex';
            }
        });
    });

    // 2. Real-time Search Filtering (Searches both Module and Sub-module names)
    container.querySelectorAll('.js-matrix-search').forEach(input => {
        input.addEventListener('input', function () {
            const tableSelector = this.getAttribute('data-target-table');
            const targetTable = container.querySelector(tableSelector);
            if (!targetTable) return;

            const q = this.value.trim().toLowerCase();
            const rows = targetTable.querySelectorAll('tbody tr');

            rows.forEach(tr => {
                const moduleName = tr.getAttribute('data-module-name') || '';
                const subTree = tr.querySelector('.perm-hierarchy-tree');
                let hasMatchingSub = false;

                if (subTree) {
                    const subItems = subTree.querySelectorAll('.tree-node-item');
                    subItems.forEach(item => {
                        const subName = item.getAttribute('data-sub-name') || '';
                        if (q && subName.includes(q)) {
                            hasMatchingSub = true;
                            item.classList.add('node-highlight');
                        } else {
                            item.classList.remove('node-highlight');
                        }
                    });
                }

                if (!q || moduleName.includes(q) || hasMatchingSub) {
                    tr.classList.remove('row-hidden');
                    // Auto-expand tree if a sub-module matched
                    if (q && hasMatchingSub && subTree) {
                        subTree.style.display = 'block';
                        const caret = tr.querySelector('.btn-sub-badge .fa-caret-down, .btn-sub-badge .fa-caret-up');
                        if (caret) caret.className = 'fa fa-caret-up ml-1';
                    }
                } else {
                    tr.classList.add('row-hidden');
                }
            });
        });
    });

    // 3. Toggle All Hierarchy (Expand / Collapse All Sub-modules)
    container.querySelectorAll('.js-toggle-all-hierarchy').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableSelector = this.getAttribute('data-target-table');
            const table = container.querySelector(tableSelector);
            if (!table) return;

            const isExpanded = this.getAttribute('data-expanded') === '1';
            const newExpanded = !isExpanded;
            this.setAttribute('data-expanded', newExpanded ? '1' : '0');

            const labelSpan = this.querySelector('.tree-toggle-txt');
            if (labelSpan) {
                labelSpan.textContent = newExpanded ? 'Collapse All' : 'Expand All';
            }

            table.querySelectorAll('.perm-hierarchy-tree').forEach(tree => {
                tree.style.display = newExpanded ? 'block' : 'none';
            });

            table.querySelectorAll('.btn-sub-badge .fa-caret-down, .btn-sub-badge .fa-caret-up').forEach(caret => {
                caret.className = newExpanded ? 'fa fa-caret-up ml-1' : 'fa fa-caret-down ml-1';
            });
        });
    });

    // 4. Presets: Full Access
    container.querySelectorAll('.js-preset-full').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableSelector = this.getAttribute('data-target-table');
            const table = container.querySelector(tableSelector);
            if (!table) return;

            getVisibleRows(table).forEach(row => {
                row.querySelectorAll('.js-perm-check').forEach(chk => chk.checked = true);
                row.querySelectorAll('.js-sub-chk, .js-user-sub-chk').forEach(chk => chk.checked = true);
                const rowChk = row.querySelector('.js-row-check');
                if (rowChk) rowChk.checked = true;
                updateSubBadge(row);
            });
        });
    });

    // 5. Presets: Standard (View + Add + Edit)
    container.querySelectorAll('.js-preset-standard').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableSelector = this.getAttribute('data-target-table');
            const table = container.querySelector(tableSelector);
            if (!table) return;

            getVisibleRows(table).forEach(row => {
                row.querySelectorAll('.js-perm-check').forEach(chk => {
                    const type = chk.getAttribute('data-type');
                    chk.checked = (type === 'view' || type === 'add' || type === 'edit');
                });
                row.querySelectorAll('.js-sub-chk, .js-user-sub-chk').forEach(chk => chk.checked = true);
                const rowChk = row.querySelector('.js-row-check');
                if (rowChk) rowChk.checked = false;
                updateSubBadge(row);
            });
        });
    });

    // 6. Presets: View Only
    container.querySelectorAll('.js-preset-view').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableSelector = this.getAttribute('data-target-table');
            const table = container.querySelector(tableSelector);
            if (!table) return;

            getVisibleRows(table).forEach(row => {
                row.querySelectorAll('.js-perm-check').forEach(chk => {
                    chk.checked = (chk.getAttribute('data-type') === 'view');
                });
                row.querySelectorAll('.js-sub-chk, .js-user-sub-chk').forEach(chk => chk.checked = true);
                const rowChk = row.querySelector('.js-row-check');
                if (rowChk) rowChk.checked = false;
                updateSubBadge(row);
            });
        });
    });

    // 7. Presets: Clear All
    container.querySelectorAll('.js-preset-clear').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableSelector = this.getAttribute('data-target-table');
            const table = container.querySelector(tableSelector);
            if (!table) return;

            getVisibleRows(table).forEach(row => {
                row.querySelectorAll('.js-perm-check').forEach(chk => chk.checked = false);
                row.querySelectorAll('.js-sub-chk, .js-user-sub-chk').forEach(chk => chk.checked = false);
                const rowChk = row.querySelector('.js-row-check');
                if (rowChk) rowChk.checked = false;
                updateSubBadge(row);
            });
        });
    });

    // 8. Row Check-All (Also toggles that row's submodules)
    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('js-row-check')) {
            const isChecked = e.target.checked;
            const row = e.target.closest('tr');
            if (row) {
                row.querySelectorAll('.js-perm-check').forEach(chk => chk.checked = isChecked);
                row.querySelectorAll('.js-sub-chk, .js-user-sub-chk').forEach(chk => chk.checked = isChecked);
                updateSubBadge(row);
            }
        }
    });

    // 9. Column Check-All
    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('js-col-check')) {
            const isChecked = e.target.checked;
            const type = e.target.getAttribute('data-type');
            const table = e.target.closest('table');
            if (table) {
                getVisibleRows(table).forEach(row => {
                    const chk = row.querySelector(`.js-perm-check.${type}`);
                    if (chk) chk.checked = isChecked;
                });
            }
        }
    });

    // 10. Master Table Check
    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('js-master-table-check')) {
            const isChecked = e.target.checked;
            const table = e.target.closest('table');
            if (table) {
                getVisibleRows(table).forEach(row => {
                    row.querySelectorAll('.js-perm-check').forEach(chk => chk.checked = isChecked);
                    row.querySelectorAll('.js-sub-chk, .js-user-sub-chk').forEach(chk => chk.checked = isChecked);
                    const rowChk = row.querySelector('.js-row-check');
                    if (rowChk) rowChk.checked = isChecked;
                    updateSubBadge(row);
                });
            }
        }
    });

    // 11. Sub-module Checkbox Change Listener
    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('js-sub-chk') || e.target.classList.contains('js-user-sub-chk')) {
            const row = e.target.closest('tr');
            if (row) {
                updateSubBadge(row);
            }
        }
    });

    // 12. Submodules Toggle & Batch Check
    container.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('.js-toggle-sub');
        if (toggleBtn) {
            const targetSelector = toggleBtn.getAttribute('data-target');
            const panel = container.querySelector(targetSelector);
            if (panel) {
                const isHidden = panel.style.display === 'none' || !panel.style.display;
                panel.style.display = isHidden ? 'block' : 'none';
                const caret = toggleBtn.querySelector('.fa-caret-down, .fa-caret-up');
                if (caret) {
                    caret.className = isHidden ? 'fa fa-caret-up ml-1' : 'fa fa-caret-down ml-1';
                }
            }
            return;
        }

        const btnAll = e.target.closest('.js-sub-check-all');
        if (btnAll) {
            const panel = btnAll.closest('.js-sub-container');
            if (panel) {
                panel.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = true);
                updateSubBadge(panel.closest('tr'));
            }
            return;
        }

        const btnNone = e.target.closest('.js-sub-clear-all');
        if (btnNone) {
            const panel = btnNone.closest('.js-sub-container');
            if (panel) {
                panel.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
                updateSubBadge(panel.closest('tr'));
            }
            return;
        }
    });

    // 13. User Dropdown Selection (Tab 2)
    const selectStaff = container.querySelector('#selectTargetStaff');
    const userPlaceholder = container.querySelector('#userPlaceholder');
    const formUserPerms = container.querySelector('#formUserPermissions');
    const userAccessBadge = container.querySelector('#userAccessBadge');
    const tableUserMatrix = container.querySelector('#tableUserMatrix');

    if (selectStaff) {
        selectStaff.addEventListener('change', function () {
            const userId = this.value;
            if (!userId) {
                if (userPlaceholder) userPlaceholder.style.display = 'flex';
                if (formUserPerms) formUserPerms.style.display = 'none';
                if (userAccessBadge) userAccessBadge.style.display = 'none';
                return;
            }

            const targetUserIdInput = container.querySelector('#permTargetUserId');
            if (targetUserIdInput) targetUserIdInput.value = userId;

            // Update user form action
            if (formUserPerms) {
                formUserPerms.action = `{{ url('role/user_permission_save') }}/${userId}`;
            }

            // Fetch user permissions via AJAX
            if (userPlaceholder) {
                userPlaceholder.style.display = 'flex';
                userPlaceholder.innerHTML = '<div class="py-3 text-center"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><div class="mt-2 font-weight-bold" style="font-size:11px;">Loading staff permissions...</div></div>';
            }
            if (formUserPerms) formUserPerms.style.display = 'none';

            fetch(`{{ url('role/user_permission') }}/${userId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const userPerms = res.permissions || {};
                    const rolePerms = res.role_permissions || {};
                    const hasCustom = Object.keys(userPerms).length > 0;

                    if (userAccessBadge) {
                        userAccessBadge.style.display = 'inline-block';
                        userAccessBadge.innerHTML = hasCustom
                            ? '<span class="perm-badge-tag" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;"><i class="fa fa-star text-success mr-1"></i> Custom Access</span>'
                            : '<span class="perm-badge-tag" style="background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;"><i class="fa fa-info-circle mr-1"></i> Role Inherited</span>';
                    }

                    // Populate tableUserMatrix rows
                    if (tableUserMatrix) {
                        tableUserMatrix.querySelectorAll('tbody tr').forEach(row => {
                            const moduleId = row.getAttribute('data-module-id');
                            const permSource = hasCustom ? (userPerms[moduleId] || null) : (rolePerms[moduleId] || null);

                            ['add', 'edit', 'view', 'delete', 'status', 'print'].forEach(type => {
                                const chk = row.querySelector(`.js-perm-check.${type}`);
                                if (chk) {
                                    chk.checked = permSource && Number(permSource[type]) === 1;
                                }
                            });

                            // Submodules selected
                            const subChks = row.querySelectorAll('.js-user-sub-chk');
                            const selectedSubs = (permSource && permSource.sub_sidebar_id) ? permSource.sub_sidebar_id.split(',').map(s => s.trim()) : [];
                            subChks.forEach(chk => {
                                chk.checked = selectedSubs.includes(String(chk.value));
                            });

                            updateSubBadge(row);
                        });
                    }

                    // Capture initial state for Tab 2 user form after loading
                    captureInitialState(formUserPerms);

                    if (userPlaceholder) userPlaceholder.style.display = 'none';
                    if (formUserPerms) formUserPerms.style.display = 'flex';
                } else {
                    if (userPlaceholder) {
                        userPlaceholder.innerHTML = '<div class="alert alert-danger font-size-11 mb-0 p-2">Failed to load user permissions.</div>';
                    }
                }
            })
            .catch(err => {
                console.error('Error fetching user permissions:', err);
                if (userPlaceholder) {
                    userPlaceholder.innerHTML = '<div class="alert alert-danger font-size-11 mb-0 p-2">Error loading permissions.</div>';
                }
            });
        });
    }

    // 14. Reset / Copy From Role Button (Tab 2)
    const btnCloneRole = container.querySelector('.js-user-clone-role');
    if (btnCloneRole) {
        btnCloneRole.addEventListener('click', function () {
            const tableRole = container.querySelector('#tableRoleMatrix');
            const tableUser = container.querySelector('#tableUserMatrix');
            if (!tableRole || !tableUser) return;

            tableRole.querySelectorAll('tbody tr').forEach(roleRow => {
                const moduleId = roleRow.getAttribute('data-module-id');
                const userRow = tableUser.querySelector(`tbody tr[data-module-id="${moduleId}"]`);
                if (userRow) {
                    ['add', 'edit', 'view', 'delete', 'status', 'print'].forEach(type => {
                        const roleChk = roleRow.querySelector(`.js-perm-check.${type}`);
                        const userChk = userRow.querySelector(`.js-perm-check.${type}`);
                        if (roleChk && userChk) {
                            userChk.checked = roleChk.checked;
                        }
                    });

                    // Clone sub-modules
                    const roleSubs = roleRow.querySelectorAll('.js-sub-chk');
                    const userSubs = userRow.querySelectorAll('.js-user-sub-chk');
                    roleSubs.forEach((rSub, idx) => {
                        if (userSubs[idx]) {
                            userSubs[idx].checked = rSub.checked;
                        }
                    });

                    updateSubBadge(userRow);
                }
            });

            if (typeof toastr !== 'undefined') {
                toastr.info('Copied role permissions to staff matrix. Click "Save User Permissions" to apply.');
            }
        });
    }

    // 15. AJAX Submit for Role Permissions Form
    if (formRole) {
        formRole.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = container.querySelector('#btnSaveRolePerms');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving...';

            const formData = new FormData(formRole);

            fetch(formRole.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                if (data.status === 'success') {
                    // Update initial state snapshot so subsequent Reset reverts to this newly saved state
                    captureInitialState(formRole);
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Role permissions saved successfully!');
                    } else {
                        alert(data.message || 'Role permissions saved successfully!');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to save role permissions.');
                    } else {
                        alert(data.message || 'Failed to save role permissions.');
                    }
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                console.error('Error saving role permissions:', err);
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred while saving role permissions.');
                }
            });
        });
    }

    // 16. AJAX Submit for User Permissions Form
    if (formUserPerms) {
        formUserPerms.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = container.querySelector('#btnSaveUserPerms');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving...';

            const formData = new FormData(formUserPerms);

            fetch(formUserPerms.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                if (data.status === 'success') {
                    // Update initial state snapshot for user form
                    captureInitialState(formUserPerms);
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'User permissions saved successfully!');
                    } else {
                        alert(data.message || 'User permissions saved successfully!');
                    }
                    if (userAccessBadge) {
                        userAccessBadge.style.display = 'inline-block';
                        userAccessBadge.innerHTML = '<span class="perm-badge-tag" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;"><i class="fa fa-star text-success mr-1"></i> Custom Access</span>';
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to save user permissions.');
                    } else {
                        alert(data.message || 'Failed to save user permissions.');
                    }
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                console.error('Error saving user permissions:', err);
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred while saving user permissions.');
                }
            });
        });
    }
}
</script>
