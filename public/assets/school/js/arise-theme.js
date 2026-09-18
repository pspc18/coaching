/**
 * ==============================================================================
 * Arise ERP Custom Theme Engine
 * Ultra-fast, lightweight JavaScript replacement for AdminLTE.
 * Handles sidebar toggling, mobile drawer, treeview navigation, and card widgets.
 * ==============================================================================
 */

(function ($) {
    'use strict';

    var AriseTheme = {
        init: function () {
            this.initBackdrop();
            this.initPushMenu();
            this.initTreeview();
            this.initCardWidgets();
            this.initDropdownsAndModals();
            this.initResponsiveState();
        },

        // Dropdown & Modal interactive handlers
        initDropdownsAndModals: function () {
            // Dropdown toggle
            $(document).on('click', '[data-toggle="dropdown"]', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $toggle = $(this);
                var $parent = $toggle.closest('.dropdown, .nav-item');
                var $menu = $parent.children('.dropdown-menu');
                if ($menu.length === 0) {
                    $menu = $parent.find('.dropdown-menu').first();
                }
                var isShown = $menu.hasClass('show');

                $('.dropdown-menu.show').not($menu).removeClass('show');
                $('.dropdown.show, .nav-item.show').not($parent).removeClass('show');

                if (!isShown) {
                    $parent.addClass('show');
                    $menu.addClass('show');
                } else {
                    $parent.removeClass('show');
                    $menu.removeClass('show');
                }
            });

            // Close dropdowns on click outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.dropdown, .dropdown-menu').length) {
                    $('.dropdown-menu.show').removeClass('show');
                    $('.dropdown.show, .nav-item.show').removeClass('show');
                }
            });

            // Modal open
            $(document).on('click', '[data-toggle="modal"]', function (e) {
                e.preventDefault();
                var target = $(this).attr('data-target') || $(this).attr('href');
                if (target && $(target).length) {
                    $(target).addClass('show').css('display', 'block');
                    if ($('.modal-backdrop').length === 0) {
                        $('body').append('<div class="modal-backdrop fade show"></div>');
                    }
                    $('body').addClass('modal-open');
                }
            });

            // Modal dismiss
            $(document).on('click', '[data-dismiss="modal"]', function (e) {
                e.preventDefault();
                var $modal = $(this).closest('.modal');
                $modal.removeClass('show').css('display', 'none');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });
        },

        // Ensure backdrop overlay exists in the DOM
        initBackdrop: function () {
            if ($('.sidebar-backdrop').length === 0) {
                $('body').append('<div class="sidebar-backdrop"></div>');
            }

            $(document).on('click', '.sidebar-backdrop', function () {
                AriseTheme.closeMobileSidebar();
            });
        },

        // PushMenu: Mobile drawer and desktop sidebar collapse
        initPushMenu: function () {
            $(document).on('click', '[data-widget="pushmenu"], .sidebar-toggle-btn', function (e) {
                e.preventDefault();
                var windowWidth = $(window).width();

                if (windowWidth <= 991) {
                    // Mobile & tablet drawer
                    if ($('body').hasClass('sidebar-open')) {
                        AriseTheme.closeMobileSidebar();
                    } else {
                        AriseTheme.openMobileSidebar();
                    }
                } else {
                    // Desktop toggle collapse
                    $('body').toggleClass('sidebar-collapse');
                }
            });

            // Mobile close button
            $(document).on('click', '.mobile-close-sidebar', function (e) {
                e.preventDefault();
                AriseTheme.closeMobileSidebar();
            });

            // Auto-close drawer on mobile when clicking regular nav link
            $(document).on('click', '.nav-sidebar .nav-item:not(.has-treeview) > a', function () {
                if ($(window).width() <= 991) {
                    AriseTheme.closeMobileSidebar();
                }
            });
        },

        openMobileSidebar: function () {
            $('body').addClass('sidebar-open').removeClass('sidebar-collapse');
        },

        closeMobileSidebar: function () {
            $('body').removeClass('sidebar-open');
        },

        // Treeview accordion navigation
        initTreeview: function () {
            // Click handler for treeview parents (delegated on document)
            $(document).on('click', '.nav-sidebar .has-treeview > .nav-link, .nav-sidebar .has-treeview > a', function (e) {
                var $link = $(this);
                var $parent = $link.closest('.has-treeview');
                var $treeview = $parent.children('.nav-treeview');

                if ($treeview.length === 0) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                // If desktop sidebar is collapsed in mini-mode, expand sidebar and open this submenu
                if ($(window).width() > 991 && $('body').hasClass('sidebar-collapse')) {
                    $('body').removeClass('sidebar-collapse');
                    $parent.siblings('.has-treeview').removeClass('menu-open').children('.nav-treeview').hide();
                    $parent.addClass('menu-open');
                    $treeview.stop(true, true).slideDown(180);
                    return;
                }

                var isOpen = $parent.hasClass('menu-open') || $treeview.is(':visible');

                if (isOpen) {
                    // Close this submenu
                    $treeview.stop(true, true).slideUp(180, function () {
                        $parent.removeClass('menu-open');
                    });
                } else {
                    // Accordion mode: close other open menus at the same level
                    $parent.siblings('.has-treeview').each(function () {
                        var $sibling = $(this);
                        if ($sibling.hasClass('menu-open') || $sibling.children('.nav-treeview').is(':visible')) {
                            $sibling.children('.nav-treeview').stop(true, true).slideUp(180, function () {
                                $sibling.removeClass('menu-open');
                            });
                        }
                    });

                    // Open this submenu
                    $parent.addClass('menu-open');
                    $treeview.stop(true, true).slideDown(180);
                }
            });
        },

        // Support for standard card tools (collapse, remove)
        initCardWidgets: function () {
            // Card Collapse
            $(document).on('click', '[data-card-widget="collapse"]', function (e) {
                e.preventDefault();
                var $btn = $(this);
                var $card = $btn.closest('.card');
                var $body = $card.children('.card-body, .card-footer');

                $body.stop(true, true).slideToggle(180, function () {
                    $card.toggleClass('collapsed-card');
                    var isCollapsed = $card.hasClass('collapsed-card');
                    $btn.find('i.fa')
                        .toggleClass('fa-minus', !isCollapsed)
                        .toggleClass('fa-plus', isCollapsed);
                });
            });

            // Card Remove
            $(document).on('click', '[data-card-widget="remove"]', function (e) {
                e.preventDefault();
                var $card = $(this).closest('.card');
                $card.fadeOut(200, function () {
                    $(this).remove();
                });
            });
        },

        // Handle window resizing
        initResponsiveState: function () {
            var resizeTimer;
            $(window).on('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function () {
                    if ($(window).width() > 991) {
                        $('body').removeClass('sidebar-open');
                    }
                }, 100);
            });
        }
    };

    // Auto initialize on DOM ready
    $(document).ready(function () {
        AriseTheme.init();
    });

    // Expose AriseTheme globally if needed
    window.AriseTheme = AriseTheme;

})(jQuery);

// Automatically ensure modals are attached to body to prevent stacking context traps
$(document).on('show.bs.modal', '.modal', function () {
    if (!$(this).parent().is('body')) {
        $(this).appendTo('body');
    }
});