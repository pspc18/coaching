@php
    $date = date('Y-m-d');
    $permission = $permission ?? Helper::permissioncheck(8);
    $startIndex = $startIndex ?? 0;
@endphp

@forelse($data as $item)
    @php
        $classes = $item->assigned_classes ?? collect();
        $dateGroups = $classes->groupBy(function ($assignedClass) {
            return !empty($assignedClass->exam_date)
                ? \Carbon\Carbon::parse($assignedClass->exam_date)->format('d-m-Y')
                : 'No Date Set';
        });
        $publishedCount = $classes->where('is_published', true)->count();
        $totalClassCount = count($classes);
        $hasPublished = $publishedCount > 0;
        $isAllPublished = $totalClassCount > 0 && $publishedCount === $totalClassCount;
        
        $statusType = 'unassigned';
        if ($totalClassCount > 0) {
            if ($publishedCount > 0) {
                $statusType = 'published';
            } else {
                $statusType = 'pending';
            }
        }
    @endphp
    <tr class="exam-row" 
        data-id="{{ $item->id }}"
        data-name="{{ strtolower($item->name ?? '') }}" 
        data-term="{{ strtolower($item->exam_term_name ?? '') }}"
        data-classes="{{ strtolower($classes->pluck('class_name')->implode(' ')) }}"
        data-status="{{ $statusType }}"
        data-has-assigned="{{ $totalClassCount > 0 ? 'yes' : 'no' }}">
        
        {{-- S.No --}}
        <td class="exam-sno-cell" style="text-align: center; font-weight: 700; color:#64748b;">
            {{ $startIndex + $loop->iteration }}
        </td>
        
        {{-- Exam Name --}}
        <td style="text-align: left; padding-left: 10px;">
            <div class="d-flex align-items-center" style="gap:7px;">
                <div style="width:24px; height:24px; border-radius:2px; background:#002C54; color:#fff; display:flex; align-items:center; justify-content:center; font-size:10.5px; font-weight:700; flex-shrink:0;">
                    <i class="fa fa-book"></i>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color:#0f172a; line-height:1.2;">{{ $item->name ?? '' }}</div>
                    @if(!empty($item->exam_date))
                        <span class="text-muted" style="font-size:10px;">
                            <i class="fa fa-calendar text-primary"></i> {{ date('d-m-Y', strtotime($item->exam_date)) }}
                        </span>
                    @endif
                </div>
            </div>
        </td>

        {{-- Assigned Classes & Dates --}}
        <td style="text-align: left; padding-left: 10px;">
            @if($classes->isNotEmpty())
                @foreach($dateGroups as $examDateLabel => $dateGroup)
                    <div class="exam-date-group">
                        <div class="exam-date-label">
                            <i class="fa fa-calendar-check-o text-primary"></i>
                            {{ $examDateLabel }}
                        </div>
                        <div class="exam-class-badges">
                            @foreach($dateGroup as $dateClass)
                                <span class="exam-class-pill {{ $dateClass->is_published ? 'published' : '' }}" 
                                      title="{{ $dateClass->is_published ? 'Result published' : 'Result pending publication' }}">
                                    @if($dateClass->is_published)
                                        <i class="fa fa-check-circle text-success"></i>
                                    @else
                                        <i class="fa fa-clock-o text-muted"></i>
                                    @endif
                                    {{ $dateClass->class_name ?? '' }}
                                    @if($dateClass->is_published)
                                        <strong style="font-size:9.5px;">(Live)</strong>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-muted" style="font-size:11px;">
                    <i class="fa fa-info-circle mr-1"></i> No classes assigned. 
                    <a href="{{ url('assign/exam/'.$item->id) }}" class="text-primary font-weight-bold ml-1">Assign classes &rarr;</a>
                </div>
            @endif
        </td>

        {{-- Result Publish Status & Button --}}
        <td style="text-align: center;">
            <button type="button" 
                    class="btn btn-publish-trigger {{ $hasPublished ? ($isAllPublished ? 'btn-success' : 'btn-info') : 'btn-warning' }}" 
                    data-toggle="modal" 
                    data-target="#publishResultModal{{ $item->id }}"
                    {{ $classes->isEmpty() ? 'disabled' : '' }}
                    title="{{ $classes->isEmpty() ? 'Please assign classes first' : 'Click to manage class-wise result publication' }}">
                <i class="fa fa-bullhorn"></i> 
                @if($classes->isEmpty())
                    Not Assigned
                @else
                    Publish ({{ $publishedCount }}/{{ $totalClassCount }})
                @endif
            </button>
        </td>

        {{-- Action Buttons (Sticky Fixed Column) --}}
        <td class="fixed_action_col" style="text-align: center;">
            <div class="action-btn-group">
                {{-- Assign Classes --}}
                <a href="{{ url('assign/exam/'.$item->id) }}" class="act-btn act-btn-assign" title="Assign Classes to this Exam">
                    <i class="fa fa-tag"></i>
                </a>

                {{-- Fill Marks via Excel --}}
                <a href="{{ url('fill-marks-by-excel?exam_id='.$item->id) }}" class="act-btn act-btn-excel" title="Fill Marks via Excel">
                    <i class="fa fa-file-excel-o"></i>
                </a>

                {{-- Manual Marks --}}
                <a href="{{ url('fill_marks?exam_id='.$item->id) }}" class="act-btn act-btn-marks" title="Manual Marks Entry">
                    <i class="fa fa-pencil"></i>
                </a>

                {{-- Copy Exam --}}
                @if($permission->add ?? true)
                    <button type="button" class="act-btn act-btn-copy copyExamData" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->copy_name }}" 
                            data-date="{{ $item->exam_date ?? $date }}" 
                            data-toggle="modal" 
                            data-target="#copyExamModal" 
                            title="Duplicate / Copy Exam">
                        <i class="fa fa-copy"></i>
                    </button>
                @endif

                {{-- Edit Exam --}}
                @if($permission->edit ?? true)
                    <a href="{{ url('edit/exam/'.$item->id) }}" class="act-btn act-btn-edit" title="Edit Exam">
                        <i class="fa fa-edit"></i>
                    </a>
                @endif

                {{-- Delete Exam --}}
                @if($permission->delete ?? true)
                    <button type="button" class="act-btn act-btn-delete deleteData" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->name }}" 
                            data-toggle="modal" 
                            data-target="#deleteExamModal" 
                            title="Delete Exam">
                        <i class="fa fa-trash-o"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr id="noDataRow">
        <td colspan="5" class="text-center text-muted py-4">
            <i class="fa fa-leanpub mr-1" style="font-size:24px;"></i>
            <div class="mt-2 font-weight-bold">No examination records found.</div>
            <div class="small">Click "+ Add Exam" to create an examination.</div>
        </td>
    </tr>
@endforelse