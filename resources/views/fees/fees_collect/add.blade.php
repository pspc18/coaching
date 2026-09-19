@php
$getstudents = Helper::getstudents();
$classType = Helper::classType();
$getPaymentMode = Helper::getPaymentMode();
$array = [];
@endphp
@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   Arise ERP - Fee Collection Desk (Screen-Height Fitted & Zero Window Scroll)
   Strictly aligned with admissionView design system
   ========================================================================== */
.admission-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.admission-page * {
    box-sizing: border-box;
}
.admission-page-layout {
    height: calc(100vh - 62px);
    max-height: calc(100vh - 62px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner - High Contrast Light Text on Dark Background */
.admission-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(0,44,84,.15);
    margin-bottom: 6px;
    flex-shrink: 0;
}
.admission-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #93c5fd !important;
    font-weight: 700;
    display: block;
    line-height: 1.2;
    margin-bottom: 2px;
}
.admission-title {
    font-size: 14px;
    font-weight: 800;
    margin: 0;
    line-height: 1.2;
    color: #ffffff !important;
}
.admission-subtitle {
    font-size: 10.5px;
    color: #e2e8f0 !important;
    margin: 2px 0 0 0;
    line-height: 1.2;
}

/* Hotkey Hints & Badges */
.kbd-hint {
    font-size: 9px;
    background: rgba(0,44,84,.6);
    border: 1px solid rgba(255,255,255,.3);
    color: #ffffff !important;
    padding: 1px 5px;
    border-radius: 3px;
    font-family: monospace;
    margin-left: 4px;
}

/* Unified Dark Navy Cards */
.dash-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden;
}
.dash-card-header {
    padding: 6px 10px;
    background: #002342;
    color: #ffffff;
    border-bottom: 1px solid rgba(255,255,255,.14);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    height: 34px;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff !important;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10px;
    font-weight: 600;
    background: rgba(255,255,255,.16);
    color: #ffffff !important;
    padding: 2px 8px;
    border-radius: 3px;
    border: 1px solid rgba(255,255,255,.22);
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* ==========================================================================
   Fixed Header (thead) & Scrolling tbody for Student Directory Table
   ========================================================================== */
.table-scroll-wrap {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
    background: #ffffff;
}

.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}

/* Sticky thead container */
.dash-table thead.sticky-thead {
    position: sticky;
    top: 0;
    z-index: 30;
}

/* Row 1: Header Titles - Locked at top: 0 */
.header-titles-row {
    height: 28px;
}
.header-titles-row th {
    position: sticky;
    top: 0;
    z-index: 32;
    height: 28px;
    background: #002C54 !important;
    color: #ffffff !important;
    padding: 5px 8px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.15);
    border-bottom: 1px solid rgba(0,0,0,0.2);
    white-space: nowrap;
    vertical-align: middle;
    box-shadow: inset 0 -1px 0 rgba(255,255,255,0.1);
}

/* Row 2: In-Column Excel Filters - Locked at top: 28px */
.excel-filter-row {
    height: 32px;
}
.excel-filter-row th {
    position: sticky;
    top: 28px;
    z-index: 31;
    height: 32px;
    background: #08335c !important;
    padding: 3px 6px !important;
    border-right: 1px solid rgba(255,255,255,.12) !important;
    border-bottom: 2px solid #001f3d !important;
    box-shadow: 0 3px 6px rgba(0,0,0,0.22) !important;
    vertical-align: middle;
}

/* Table Body Rows */
.dash-table tbody td {
    padding: 6px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11px;
}
.dash-table tbody tr:nth-child(odd) td {
    background: #f8fafc;
}
.dash-table tbody tr:nth-child(even) td {
    background: #edf2f7;
}
.dash-table tbody tr:hover td {
    background: #e2e8f0 !important;
}
.dash-table tbody tr.active-row td {
    background: #e0f2fe !important;
    border-bottom-color: #38bdf8;
    color: #0369a1 !important;
    font-weight: 600;
}
.dash-table tbody tr.active-row {
    outline: 2px solid #0284c7;
    outline-offset: -2px;
}

/* In-Table Column Filters - Light Text on High-Contrast Navy Background */
.excel-col-filter {
    width: 100%;
    height: 24px;
    padding: 2px 6px;
    font-size: 10.5px;
    font-weight: 500;
    border: 1px solid rgba(255,255,255,.3);
    background: #04182e !important;
    color: #ffffff !important;
    border-radius: 3px;
    outline: none;
    transition: all .15s ease;
    color-scheme: dark;
}
.excel-col-filter::placeholder {
    color: rgba(226, 232, 240, 0.75) !important;
}
.excel-col-filter:focus {
    background: #020f1e !important;
    color: #ffffff !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.3) !important;
}
select.excel-col-filter {
    background-color: #04182e !important;
    color: #ffffff !important;
    cursor: pointer;
}
select.excel-col-filter option {
    background-color: #002C54 !important;
    color: #ffffff !important;
}

/* Filter Reset Button */
.btn-clear-filters {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 3px;
    border: 1px solid rgba(255,255,255,.3);
    background: #04182e;
    color: #ffffff;
    cursor: pointer;
    transition: all .15s ease;
    padding: 0;
}
.btn-clear-filters:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}

/* Student Badges with Proper Padding & Margins */
.badge-adm-no {
    display: inline-block;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 3px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    white-space: nowrap;
}
.badge-class {
    display: inline-block;
    padding: 2px 7px;
    font-size: 9.5px;
    font-weight: 600;
    border-radius: 3px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    white-space: nowrap;
}

/* Empty State */
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    padding: 36px 20px;
    text-align: center;
    color: #475569;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
}
.dash-empty-state .empty-icon {
    font-size: 46px;
    color: #002C54;
    margin-bottom: 12px;
    line-height: 1;
}
.dash-empty-state .empty-title {
    font-size: 14px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 6px;
}
.dash-empty-state .empty-desc {
    font-size: 11.5px;
    color: #64748b;
    max-width: 400px;
    line-height: 1.5;
    margin: 0;
}
</style>
@endsection

@section('content')

<div class="content-wrapper admission-page">
    <section class="content p-1">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">

                <!-- Top Hero Banner (High Contrast Light Text on Dark Navy) -->
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker">
                            <i class="fa fa-calculator mr-1"></i> Fee Collection Terminal
                        </span>
                        <h1 class="admission-title">{{ __('fees.Collect Student Fees') }}</h1>
                        <p class="admission-subtitle">Fast keyboard-enabled settlement, instant receipts &amp; smart ledger</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-total-records">
                            <i class="fa fa-keyboard-o mr-1"></i> Shortcuts: <span class="kbd-hint">Alt+P Print</span> <span class="kbd-hint">Alt+F Full Due</span> <span class="kbd-hint">/ Search</span>
                        </span>
                        <span class="badge-total-records">
                            <i class="fa fa-calendar-check-o mr-1"></i> Session: #{{ Session::get('session_id') }}
                        </span>
                    </div>
                </div>

                <!-- Hidden Form for Legacy Fallback -->
                <form id="quickForm" method="post" action="{{ url('Fees/add') }}" style="display: none;">
                    @csrf
                    <input type="hidden" id="admission_type_id" name="admission_type_id" value="{{ $search['admission_type_id'] ?? '' }}">
                    <input type="hidden" id="class_type_id" name="class_type_id" value="{{ $search['class_type_id'] ?? '' }}">
                    <input type="hidden" id="search_type" name="search_type" value="{{ $search['search_type'] ?? '' }}">
                    <input type="text" id="name" name="name" value="{{ $search['name'] ?? '' }}">
                </form>

                <!-- Main Split Terminal Screen (Fits exact remaining available height) -->
                <div class="row terminal-grid" style="flex: 1; min-height: 0; margin: 0 -3px; overflow: hidden;">
                    <!-- Left Split: Student Directory (Col 12 / Col lg 4) -->
                    <div class="col-12 col-lg-4 px-1" style="height: 100%; min-height: 0; display: flex; flex-direction: column; overflow: hidden;">
                        <div class="dash-card d-flex flex-column" style="height: 100%; min-height: 0; margin-bottom: 0; overflow: hidden;">
                            <div class="dash-card-header">
                                <h3 class="dash-card-title">
                                    <i class="fa fa-users text-info mr-1"></i> Students Directory
                                </h3>
                                <span class="badge-total-records" id="student_count_badge">
                                    <i class="fa fa-user-circle mr-1"></i> {{ count($data ?? []) }} Students
                                </span>
                            </div>

                            <!-- Scrollable Student Table with Locked Fixed Header (thead) -->
                            <div class="table-scroll-wrap">
                                <table class="dash-table" id="student_directory_table">
                                    <thead class="sticky-thead">
                                        <!-- Row 1: Header Titles (Locked top: 0) -->
                                        <tr class="header-titles-row">
                                            <th style="width: 58px;" class="text-center">Adm No</th>
                                            <th>Student Details</th>
                                            <th style="width: 75px;" class="text-center">Class</th>
                                            <th style="width: 65px;" class="text-center">Type</th>
                                            <th style="width: 32px;" class="text-center"><i class="fa fa-filter"></i></th>
                                        </tr>

                                        <!-- Row 2: In-Column Excel Filters (Locked top: 28px) -->
                                        <tr class="excel-filter-row">
                                            <th class="text-center">
                                                <input type="text"
                                                       id="filter_adm_no"
                                                       class="excel-col-filter text-center"
                                                       placeholder="Adm#"
                                                       autocomplete="off" />
                                            </th>
                                            <th>
                                                <input type="text"
                                                       id="live_student_search"
                                                       class="excel-col-filter"
                                                       placeholder="Name, Mobile, Father..."
                                                       autocomplete="off" />
                                            </th>
                                            <th>
                                                <select class="excel-col-filter" id="filter_class_id">
                                                    <option value="">All</option>
                                                    @if(!empty($classType))
                                                        @foreach($classType as $type)
                                                            <option value="{{ $type->id ?? '' }}" {{ (string) old('class_type_id', $search['class_type_id'] ?? '') === (string) $type->id ? 'selected' : '' }}>
                                                                {{ $type->name ?? '' }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </th>
                                            <th>
                                                <select class="excel-col-filter" id="filter_rte">
                                                    <option value="">All</option>
                                                    <option value="1">Non-RTE</option>
                                                    <option value="2">RTE</option>
                                                </select>
                                            </th>
                                            <th class="text-center">
                                                <button type="button" id="btn_clear_filters" class="btn-clear-filters" title="Reset In-Table Filters">
                                                    <i class="fa fa-refresh"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="student_tbody">
                                        @if(!empty($data) && count($data) > 0)
                                            @foreach ($data as $item)
                                                @php
                                                    $array[$item->id] = $item;
                                                @endphp
                                                <tr class="quickCollect pointer"
                                                    data-id="{{ $item->id ?? '' }}"
                                                    data-unique="{{ $item['unique_system_id'] ?? '' }}"
                                                    onclick="selectStudentAndLoad({{ $item->id }}, '{{ $item['unique_system_id'] ?? '' }}', '{{ Session::get('session_id') }}', this)"
                                                    title="Click to open fee desk for {{ $item['first_name'] ?? '' }}">
                                                    <td class="text-center">
                                                        <span class="badge-adm-no">{{ $item['admissionNo'] ?? '' }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold" style="font-size: 11.5px; color: #002C54; margin-bottom: 2px;">
                                                            {{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}
                                                        </div>
                                                        <div class="small text-muted" style="font-size: 10px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                                            <span><i class="fa fa-user mr-1 text-secondary"></i> F: {{ $item['father_name'] ?? '—' }}</span>
                                                            @if(!empty($item['mobile']))
                                                                <span><i class="fa fa-phone mr-1 text-success"></i> {{ $item['mobile'] }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge-class">{{ $item['ClassTypes']['name'] ?? '—' }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if(($item['admission_type_id'] ?? '') == 2)
                                                            <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 9px; border-radius: 3px; padding: 2px 6px;">RTE</span>
                                                        @else
                                                            <span class="text-muted font-weight-bold" style="font-size: 9.5px;">Non-RTE</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-muted" style="font-size: 11px;">
                                                        <i class="fa fa-chevron-right"></i>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    No matching students found
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Split: POS Fee Desk & Ledger Canvas (Col 12 / Col lg 8) -->
                    <div class="col-12 col-lg-8 px-1" style="height: 100%; min-height: 0; display: flex; flex-direction: column; overflow: hidden;">
                        <div class="dash-card d-flex flex-column" style="height: 100%; min-height: 0; margin-bottom: 0; overflow: hidden;">
                            <div class="dash-card-header">
                                <h3 class="dash-card-title">
                                    <i class="fa fa-calculator text-info mr-1"></i> Fee Collection Terminal &amp; Settlement
                                </h3>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge-total-records">
                                        <i class="fa fa-shield mr-1 text-success"></i> Fast POS Counter
                                    </span>
                                </div>
                            </div>

                            <!-- Dynamic Injected Container (Full available height flex child) -->
                            <div id="student_fees_detail" style="flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; padding: 4px;">
                                <!-- Welcome Screen -->
                                <div class="dash-empty-state">
                                    <i class="fa fa-user-circle-o empty-icon"></i>
                                    <div class="empty-title">Select a Student to Collect Fees</div>
                                    <div class="empty-desc">
                                        Click on any student from the left directory or search by Name, Mobile, Class, Adm No or RTE directly in the table header to open their fee desk.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- End .admission-page-layout -->
        </div>
    </section>
</div>

<script>
var studentsStore = @json($array);
var searchTimeout = null;
var activeSearchAjax = null;
var BASEURL = "{{ url('/') }}";
var CURRENT_SESSION_ID = "{{ Session::get('session_id') }}";
var IMAGE_SHOW_PATH = "{{ env('IMAGE_SHOW_PATH') }}";

/* Select Student & Trigger Loading */
function selectStudentAndLoad(admissionId, unique_system_id, session_id, element) {
    if (element) {
        $('#student_tbody tr').removeClass('active-row');
        $(element).addClass('active-row');
    }
    showData(admissionId, unique_system_id, session_id);
}

/* Show Data via AJAX to /student_fees_onclick */
function showData(admissionId, unique_system_id, session_id) {
    $('#student_fees_detail').html(`
        <div class="p-4 text-center m-auto">
            <i class="fa fa-spinner fa-spin fa-2x mb-2" style="color: #002C54 !important;"></i>
            <h5 class="font-weight-bold" style="font-size: 13px; color: #002C54; margin-bottom: 4px;">Loading Student Fee Ledger...</h5>
            <p class="text-muted small mb-0">Computing fee heads, outstanding balances &amp; receipt records</p>
        </div>
    `);

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }, 
        type: 'post',
        url: BASEURL + '/student_fees_onclick',
        data: {
            admission_id: admissionId,
            unique_system_id: (unique_system_id && unique_system_id !== 'null') ? unique_system_id : '',
            session_id: session_id,
        },
        success: function(data) {
            if (data == 0) {
                toastr.warning('Please assign fee structure for this student in master settings.');
                $('#student_fees_detail').html(`
                    <div class="dash-empty-state">
                        <i class="fa fa-exclamation-triangle text-warning empty-icon"></i>
                        <div class="empty-title">No Fees Assigned for this Session</div>
                        <div class="empty-desc">Please assign fee structure in settings before collecting payment.</div>
                    </div>
                `);
            } else {
                $('#student_fees_detail').html(data);
            }
        },
        error: function() {
            toastr.error('Failed to fetch fee ledger. Please try again.');
            $('#student_fees_detail').html(`
                <div class="dash-empty-state">
                    <i class="fa fa-times-circle text-danger empty-icon"></i>
                    <div class="empty-title">Error Loading Ledger</div>
                    <div class="empty-desc">An error occurred while loading this student's fee details.</div>
                </div>
            `);
        }
    });
}

/* Trigger search across all in-table column filters with debouncing */
function triggerSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        var admNo = $('#filter_adm_no').val().trim();
        var nameQuery = $('#live_student_search').val().trim();
        var classId = $('#filter_class_id').val();
        var rte = $('#filter_rte').val();

        performLiveSearch(admNo, nameQuery, classId, rte);
    }, 200);
}

/* Event Handlers for In-Table Column Filters */
$('#filter_adm_no').on('input', triggerSearch);
$('#live_student_search').on('input', triggerSearch);
$('#filter_class_id').on('change', triggerSearch);
$('#filter_rte').on('change', triggerSearch);

/* Clear In-Table Filters */
$('#btn_clear_filters').on('click', function() {
    $('#filter_adm_no').val('');
    $('#live_student_search').val('');
    $('#filter_class_id').val('');
    $('#filter_rte').val('');
    performLiveSearch('', '', '', '');
});

/* Prevent form submit on Enter key inside filter inputs */
$('#filter_adm_no, #live_student_search').on('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        triggerSearch();
    }
});

/* Global hotkey '/' to focus search */
$(document).on('keydown', function(e) {
    if (e.key === '/' && !$(e.target).is('input, textarea, select')) {
        e.preventDefault();
        $('#live_student_search').focus().select();
    }
});

/* Perform AJAX Live Search */
function performLiveSearch(admNo, nameQuery, classId, rte) {
    if (activeSearchAjax) {
        activeSearchAjax.abort();
    }

    $('#student_count_badge').html('<i class="fa fa-spinner fa-spin mr-1"></i> Searching...');

    activeSearchAjax = $.ajax({
        url: BASEURL + '/Fees/add',
        type: 'GET',
        data: {
            ajax_search: 1,
            admission_no: admNo,
            name: nameQuery,
            class_type_id: classId,
            admission_type_id: rte,
            search_type: ''
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.status === 'success') {
                renderStudentDirectory(response.students);
            }
        },
        error: function(xhr, status) {
            if (status !== 'abort') {
                $('#student_count_badge').text('0 Students');
            }
        }
    });
}

/* Render Left Sidebar Directory */
function renderStudentDirectory(students) {
    var count = students ? students.length : 0;
    $('#student_count_badge').html('<i class="fa fa-user-circle mr-1"></i> ' + count + ' Students');
    var tbody = $('#student_tbody');
    tbody.empty();

    if (!students || students.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No matching students found</td></tr>');
        return;
    }

    students.forEach(function(item) {
        studentsStore[item.id] = item;
        var rteBadge = (item.admission_type_id == 2) 
            ? '<span class="badge badge-warning text-dark font-weight-bold" style="font-size: 9px; border-radius: 3px; padding: 2px 6px;">RTE</span>'
            : '<span class="text-muted font-weight-bold" style="font-size: 9.5px;">Non-RTE</span>';

        var safeUnique = item.unique_system_id ? item.unique_system_id : '';

        var rowHtml = `
            <tr class="quickCollect pointer"
                data-id="${item.id}"
                data-unique="${safeUnique}"
                onclick="selectStudentAndLoad(${item.id}, '${safeUnique}', '${CURRENT_SESSION_ID}', this)"
                title="Click to open fee desk for ${item.first_name || ''}">
                <td class="text-center">
                    <span class="badge-adm-no">${item.admissionNo || ''}</span>
                </td>
                <td>
                    <div class="font-weight-bold" style="font-size: 11.5px; color: #002C54; margin-bottom: 2px;">
                        ${item.first_name || ''} ${item.last_name || ''}
                    </div>
                    <div class="small text-muted" style="font-size: 10px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <span><i class="fa fa-user mr-1 text-secondary"></i> F: ${item.father_name || '—'}</span>
                        ${item.mobile && item.mobile !== '-' ? `<span><i class="fa fa-phone mr-1 text-success"></i> ${item.mobile}</span>` : ''}
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge-class">${item.class_name || '—'}</span>
                </td>
                <td class="text-center">
                    ${rteBadge}
                </td>
                <td class="text-center text-muted" style="font-size: 11px;">
                    <i class="fa fa-chevron-right"></i>
                </td>
            </tr>
        `;
        tbody.append(rowHtml);
    });
}
</script>

@endsection
