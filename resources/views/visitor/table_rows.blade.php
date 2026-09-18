@php
    $i = $startIndex ?? 0;
    $visitorList = $data ?? ($visitors ?? []);
    $today = date('Y-m-d');
    $permission = Helper::permissioncheck(28);
@endphp

@if(!empty($visitorList) && count($visitorList) > 0)
    @foreach($visitorList as $item)
        @php
            $visitorId = $item->id;
            $visitorName = trim((string)($item->visitor_name ?? ''));
            $visitorMobile = trim((string)($item->visitor_mobile ?? ''));
            $mobileClean = preg_replace('/\D/', '', $visitorMobile);
            if (strlen($mobileClean) > 10) {
                $mobileClean = substr($mobileClean, -10);
            }
            $studentName = trim((string)($item->stu_name ?? ''));
            $className = $item->classType ? ($item->classType->name ?? '') : ($item->class_name ?? '');
            $idName = trim((string)($item->id_name ?? ''));
            $aadharNo = trim((string)($item->aadharNo ?? ''));
            $visitDate = !empty($item->date) ? date('Y-m-d', strtotime($item->date)) : '';
            $isToday = ($visitDate === $today);
            $remark = trim((string)($item->remark ?? ''));
        @endphp
        <tr class="visitor-row {{ $isToday ? 'row-visitor-today' : '' }}"
            data-id="{{ $visitorId }}"
            data-visitor="{{ strtolower($visitorName) }}"
            data-mobile="{{ $mobileClean }}"
            data-student="{{ strtolower($studentName) }}"
            data-class="{{ $item->class_type_id }}"
            data-id-name="{{ strtolower($idName) }}"
            data-aadhar="{{ $aadharNo }}"
            data-date="{{ $visitDate }}"
            data-remark="{{ strtolower($remark) }}">

            {{-- 1. Serial Number --}}
            <td class="text-center serial-cell font-weight-bold text-muted" style="width: 44px;">
                {{ ++$i }}
            </td>

            {{-- 2. Visitor Name & Mobile / WhatsApp --}}
            <td style="min-width: 200px;">
                <div class="visitor-name-box">
                    <a href="javascript:void(0);" 
                       class="visitor-name-link view-visitor-btn" 
                       data-visitor='@json($item)'
                       title="Click to view visitor details">
                        <i class="fa fa-user-circle mr-1 text-primary"></i> {{ $visitorName ?: 'Visitor #' . $visitorId }}
                    </a>
                    
                    @if(!empty($visitorMobile))
                        <div class="visitor-mobile-wrap mt-1">
                            <i class="fa fa-phone text-muted mr-1"></i>
                            <span class="visitor-mobile-text">{{ $visitorMobile }}</span>
                            @if(strlen($mobileClean) === 10)
                                <a href="https://api.whatsapp.com/send?phone=91{{ $mobileClean }}" 
                                   target="_blank" 
                                   class="btn-row-wa" 
                                   title="Send WhatsApp Message">
                                    <i class="fa fa-whatsapp"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </td>

            {{-- 3. Student Name & Class --}}
            <td style="min-width: 170px;">
                <div class="student-info-box">
                    @if(!empty($studentName))
                        <div class="student-name-row font-weight-bold text-dark">
                            <i class="fa fa-graduation-cap text-info mr-1"></i> {{ $studentName }}
                        </div>
                    @else
                        <span class="text-muted font-size-11">No student tagged</span>
                    @endif

                    @if(!empty($className))
                        <div class="mt-1">
                            <span class="badge-class" title="Class: {{ $className }}">
                                {{ $className }}
                            </span>
                        </div>
                    @endif
                </div>
            </td>

            {{-- 4. ID Type & Aadhaar / ID Number --}}
            <td style="min-width: 160px;">
                <div class="id-info-box">
                    @if(!empty($idName))
                        <span class="badge-id-type mb-1">{{ $idName }}</span>
                    @endif
                    @if(!empty($aadharNo))
                        <div class="cred-cell-wrap">
                            <span class="cred-pill cred-pill-id" title="Aadhaar / ID No">
                                <i class="fa fa-id-card-o mr-1"></i> {{ $aadharNo }}
                            </span>
                        </div>
                    @elseif(empty($idName))
                        <span class="text-muted font-size-11">-</span>
                    @endif
                </div>
            </td>

            {{-- 5. Visit Date & Indicator --}}
            <td style="min-width: 130px;">
                <div class="date-info-box">
                    <span class="font-weight-bold text-dark font-size-11">
                        {{ !empty($item->date) ? date('d M Y', strtotime($item->date)) : '-' }}
                    </span>
                    @if($isToday)
                        <span class="badge-today-visit mt-1"><i class="fa fa-star mr-1"></i> Today</span>
                    @endif
                </div>
            </td>

            {{-- 6. Purpose / Remarks --}}
            <td style="min-width: 220px; max-width: 320px;">
                @if(!empty($remark))
                    <span class="visitor-remark-text" title="{{ $remark }}">
                        {{ \Illuminate\Support\Str::limit($remark, 85) }}
                    </span>
                @else
                    <span class="text-muted font-size-11">-</span>
                @endif
            </td>

            {{-- 7. Actions (Sticky Right Column) --}}
            <td class="text-center fixed_action_col" style="width: 100px;">
                <div class="table-actions">
                    {{-- View Details Modal Button --}}
                    <button type="button" 
                            class="table-btn btn-action-view view-visitor-btn"
                            data-visitor='@json($item)'
                            title="View Visitor Details">
                        <i class="fa fa-eye"></i>
                    </button>

                    {{-- Edit Button --}}
                    @if($permission->edit ?? true)
                        <a href="{{ url('visitorEdit', $visitorId) }}" 
                           class="table-btn btn-action-edit" 
                           title="Edit Visitor">
                            <i class="fa fa-edit"></i>
                        </a>
                    @endif

                    {{-- Delete Button --}}
                    @if($permission->delete ?? true)
                        <button type="button" 
                                class="table-btn btn-action-delete delete-visitor-btn"
                                data-id="{{ $visitorId }}"
                                data-name="{{ $visitorName }}"
                                title="Delete Visitor">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="7" class="text-center py-5">
            <div class="dash-empty-state">
                <div class="empty-icon">
                    <i class="fa fa-address-book-o"></i>
                </div>
                <div class="empty-title">No Visitor Records Found</div>
                <div class="empty-desc">
                    No visitor logs match your selected filter criteria. Try adjusting the search text, class, or date filters above.
                </div>
                <div class="mt-3">
                    <button type="button" class="dash-btn dash-btn-outline text-primary border-primary" id="btn-empty-clear-filters">
                        <i class="fa fa-refresh mr-1"></i> Reset Filters
                    </button>
                    @if($permission->add ?? true)
                        <a href="{{ url('visitorAdd') }}" class="dash-btn dash-btn-primary ml-2">
                            <i class="fa fa-plus mr-1"></i> Add Visitor
                        </a>
                    @endif
                </div>
            </div>
        </td>
    </tr>
@endif
