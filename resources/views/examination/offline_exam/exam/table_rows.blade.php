@php
    $date = date('Y-m-d');
    $permission = $permission ?? Helper::permissioncheck(8);
    $startIndex = $startIndex ?? 0;
@endphp

@forelse($data as $item)
    @php
        $examDateFormatted = !empty($item->exam_date)
            ? \Carbon\Carbon::parse($item->exam_date)->format('d M Y')
            : 'Not Set';
        $examDateIso = !empty($item->exam_date)
            ? \Carbon\Carbon::parse($item->exam_date)->format('Y-m-d')
            : $date;
        $createdFormatted = !empty($item->created_at)
            ? \Carbon\Carbon::parse($item->created_at)->format('d M Y')
            : '-';
        $statusType = $item->is_published ? 'published' : 'pending';
        $codeStr = $item->code ?: ($item->exam_code ?: 'EXAM-'.$item->id);
    @endphp
    <tr class="exam-row" 
        data-id="{{ $item->id }}"
        data-name="{{ strtolower($item->name ?? '') }}" 
        data-class="{{ strtolower($item->class_name ?? '') }}"
        data-status="{{ $statusType }}">
        
        {{-- 1. S.No. --}}
        <td class="text-center font-weight-bold text-muted row-index" style="vertical-align: middle;">
            {{ $startIndex + $loop->iteration }}
        </td>
        
        {{-- 2. Exam Name & Scanner Code --}}
        <td style="vertical-align: middle;">
            <div class="exam-title-cell">
                <a href="{{ url('edit/exam/'.$item->id) }}" class="exam-name-link font-weight-600" style="color: #002C54; font-size: 13px;" title="Click to edit exam">
                    {{ $item->name ?? '' }}
                </a>
                <div class="exam-date-meta d-flex align-items-center mt-1" style="gap: 6px;">
                    <span class="badge badge-light border text-primary font-weight-600 px-1 py-0" style="font-size: 10px; letter-spacing: 0.3px;">
                        <i class="fa fa-barcode mr-1"></i>{{ $codeStr }}
                    </span>
                    <span class="text-muted" style="font-size: 11px;">
                        <i class="fa fa-calendar-o mr-1"></i>{{ $createdFormatted }}
                    </span>
                </div>
            </div>
        </td>

        {{-- 3. Course --}}
        <td style="vertical-align: middle;">
            @if(!empty($item->class_name))
                <span class="badge-class-pill font-weight-600" style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-size: 11.5px; border: 1px solid #bae6fd; display: inline-flex; align-items: center;">
                    <i class="fa fa-graduation-cap mr-1"></i> {{ $item->class_name }}
                </span>
            @else
                <span class="text-muted" style="font-size: 11px;">Unassigned</span>
            @endif
        </td>

        {{-- 4. Included Subjects --}}
        <td style="vertical-align: middle;">
            @if(!empty($item->subject_names) && count($item->subject_names) > 0)
                <div class="d-flex flex-wrap" style="gap: 4px; max-width: 220px;">
                    @foreach($item->subject_names as $subName)
                        <span class="badge badge-light border text-dark font-weight-normal px-2 py-1" style="font-size: 10.5px; border-radius: 3px; background: #f8fafc;">
                            {{ $subName }}
                        </span>
                    @endforeach
                </div>
            @else
                <span class="text-muted" style="font-size: 11px;"><i class="fa fa-book mr-1"></i> All Course Subjects</span>
            @endif
        </td>

        {{-- 5. Exam Date --}}
        <td class="text-center font-weight-500" style="vertical-align: middle; font-size: 11.5px; color: #0f172a; white-space: nowrap;">
            <i class="fa fa-calendar-check-o text-primary mr-1"></i> {{ $examDateFormatted }}
        </td>

        {{-- 6. Result Published Status & Publish Action --}}
        <td class="text-center" style="vertical-align: middle; white-space: nowrap;">
            @if($item->is_published)
                <button type="button" 
                        class="btn-pub-action btn-pub-published py-1 px-2 openPublishModalBtn" 
                        style="height: 24px; font-size: 10.5px; border-radius: 4px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; cursor: pointer; font-weight: 600;" 
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-class-id="{{ $item->class_type_id }}"
                        data-class-name="{{ $item->class_name }}"
                        data-published="1"
                        data-date="{{ $item->published_date_iso }}"
                        title="Result published on {{ $item->published_at ?? '-' }}. Click to change date or unpublish.">
                    <i class="fa fa-check-circle mr-1 text-success"></i> Published
                    @if(!empty($item->published_at) && $item->published_at !== 'Published')
                        <span style="opacity: 0.9; font-weight: 500;">({{ $item->published_at }})</span>
                    @endif
                </button>
            @else
                <button type="button" 
                        class="btn-pub-action btn-pub-pending py-1 px-2 openPublishModalBtn" 
                        style="height: 24px; font-size: 10.5px; border-radius: 4px; cursor: pointer; font-weight: 500;"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-class-id="{{ $item->class_type_id }}"
                        data-class-name="{{ $item->class_name }}"
                        data-published="0"
                        data-date="{{ $date }}"
                        title="Click to publish result for this exam">
                    <i class="fa fa-clock-o mr-1 text-warning"></i> Publish Result
                </button>
            @endif
        </td>

        {{-- 7. Marks Entry Dropdown Menu --}}
        <td class="text-center" style="vertical-align: middle;">
            <div class="dropdown d-inline-block">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle py-1 px-2 font-weight-500" 
                        type="button" 
                        id="marksDropdown_{{ $item->id }}" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                        style="font-size: 11px; height: 26px; border-radius: 4px;">
                    <i class="fa fa-pencil-square-o mr-1"></i> Marks Entry
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-sm border py-1" 
                     aria-labelledby="marksDropdown_{{ $item->id }}" 
                     style="min-width: 175px; font-size: 11.5px; border-radius: 4px; z-index: 1050;">
                    <div class="dropdown-header text-uppercase text-muted font-weight-bold px-3 py-1" style="font-size: 9.5px; letter-spacing: 0.5px;">
                        {{ $item->class_name ?? 'Course Options' }}
                    </div>
                    <a class="dropdown-item px-3 py-2 d-flex align-items-center" 
                       href="{{ url('fill-marks-by-excel?class_type_id='.$item->class_type_id.'&exam_id='.$item->id) }}" 
                       title="Upload marks using Excel sheet">
                        <i class="fa fa-file-excel-o text-success mr-2" style="width: 14px;"></i> Upload by Excel
                    </a>
                    <a class="dropdown-item px-3 py-2 d-flex align-items-center" 
                       href="{{ url('fill_marks?class_type_id='.$item->class_type_id.'&exam_id='.$item->id) }}" 
                       title="Manual marks entry">
                        <i class="fa fa-pencil text-primary mr-2" style="width: 14px;"></i> Manual Fill Marks
                    </a>
                    <a class="dropdown-item px-3 py-2 d-flex align-items-center" 
                       href="{{ url('upload_omr?class_type_id='.$item->class_type_id.'&exam_id='.$item->id) }}" 
                       title="Upload scanned OMR sheets">
                        <i class="fa fa-check-square-o text-warning mr-2" style="width: 14px;"></i> Upload OMR Sheet
                    </a>
                </div>
            </div>
        </td>

        {{-- 8. Actions (Sticky Fixed Column) --}}
        <td class="text-center fixed_action_col" style="vertical-align: middle;">
            <div class="table-actions">
                {{-- Edit Exam --}}
                @if($permission->edit ?? true)
                    <a href="{{ url('edit/exam/'.$item->id) }}" class="table-btn btn-action-edit" title="Edit Exam Details">
                        <i class="fa fa-edit"></i>
                    </a>
                @endif

                {{-- Duplicate / Copy Exam --}}
                @if($permission->add ?? true)
                    <button type="button" class="table-btn btn-action-copy copyExamData" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->copy_name }}" 
                            data-class-id="{{ $item->class_type_id }}"
                            data-class-name="{{ $item->class_name }}"
                            data-date="{{ $examDateIso }}" 
                            title="Duplicate / Copy Exam">
                        <i class="fa fa-copy"></i>
                    </button>
                @endif

                {{-- Delete Exam (Themed Modal) --}}
                @if($permission->delete ?? true)
                    <button type="button" class="table-btn btn-action-delete deleteExamBtn" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->name }}" 
                            data-class="{{ $item->class_name }}"
                            data-date="{{ $examDateFormatted }}" 
                            title="Delete Exam">
                        <i class="fa fa-trash-o"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="8" class="p-0">
            <div class="dash-empty-state text-center py-4">
                <div class="empty-icon text-muted mb-2"><i class="fa fa-leanpub fa-3x"></i></div>
                <div class="empty-title font-weight-bold" style="font-size: 15px;">No Examinations Found</div>
                <div class="empty-desc text-muted" style="font-size: 12px;">No examination records match your current filter criteria.</div>
            </div>
        </td>
    </tr>
@endforelse
