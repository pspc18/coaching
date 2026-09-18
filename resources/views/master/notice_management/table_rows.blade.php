@php
    $i = $startIndex ?? 0;
    $noticesList = $data ?? ($notices ?? []);
    $today = \Carbon\Carbon::today()->toDateString();
@endphp

@if(!empty($noticesList) && count($noticesList) > 0)
    @foreach($noticesList as $notice)
        @php
            $noticeId = $notice->id;
            $creatorName = $notice->creator ? trim($notice->creator->first_name . ' ' . $notice->creator->last_name) : 'User #' . $notice->created_by;
            $reviewerName = $notice->reviewer ? trim($notice->reviewer->first_name . ' ' . $notice->reviewer->last_name) : null;
            $recipientCount = $notice->recipients ? $notice->recipients->count() : 0;
            $sampleRecipients = $notice->recipients ? $notice->recipients->take(3)->pluck('recipient_name')->filter()->implode(', ') : '';
            if ($recipientCount > 3) {
                $sampleRecipients .= ' +' . ($recipientCount - 3) . ' more';
            }

            $fromDate = $notice->from_date ? \Carbon\Carbon::parse($notice->from_date)->format('Y-m-d') : null;
            $toDate = $notice->to_date ? \Carbon\Carbon::parse($notice->to_date)->format('Y-m-d') : null;
            
            $validityStatus = 'active';
            $validityBadge = 'Active';
            $validityClass = 'badge-validity-active';
            if ($toDate && $toDate < $today) {
                $validityStatus = 'expired';
                $validityBadge = 'Expired';
                $validityClass = 'badge-validity-expired';
            } elseif ($fromDate && $fromDate > $today) {
                $validityStatus = 'upcoming';
                $validityBadge = 'Upcoming';
                $validityClass = 'badge-validity-upcoming';
            }

            $status = $notice->status ?? 'pending';
            $attachmentSizeFormatted = '';
            if ($notice->attachment_size) {
                $attachmentSizeFormatted = $notice->attachment_size >= 1048576 
                    ? number_format($notice->attachment_size / 1048576, 1) . ' MB' 
                    : number_format($notice->attachment_size / 1024, 0) . ' KB';
            }
        @endphp
        <tr class="notice-row {{ $status === 'approved' ? 'row-notice-approved' : ($status === 'rejected' ? 'row-notice-rejected' : 'row-notice-pending') }}"
            data-id="{{ $noticeId }}"
            data-title="{{ strtolower($notice->title) }}"
            data-status="{{ $status }}"
            data-audience="{{ $notice->audience_type }}"
            data-from="{{ $fromDate }}"
            data-to="{{ $toDate }}"
            data-creator="{{ strtolower($creatorName) }}">

            {{-- 1. Serial Number --}}
            <td class="text-center serial-cell font-weight-bold text-muted" style="width: 44px;">
                {{ ++$i }}
            </td>

            {{-- 2. Notice Title & Content Preview --}}
            <td style="min-width: 260px; max-width: 360px;">
                <div class="notice-title-box">
                    <a href="javascript:void(0);" 
                       class="notice-title-link view-notice-btn" 
                       data-notice='@json($notice)'
                       data-creator="{{ $creatorName }}"
                       data-reviewer="{{ $reviewerName }}"
                       title="Click to view full notice details">
                        <i class="fa fa-file-text-o mr-1 text-primary"></i> {{ $notice->title }}
                    </a>
                    
                    <p class="notice-preview-msg mb-1" title="{{ strip_tags($notice->message) }}">
                        {{ \Illuminate\Support\Str::limit(strip_tags($notice->message), 85) }}
                    </p>

                    @if(!empty($notice->attachment_path))
                        <div class="notice-attachment-pill-wrap">
                            <a href="{{ url('notice-management/' . $noticeId . '/attachment') }}" 
                               target="_blank" 
                               class="notice-pdf-pill" 
                               title="Download / View Attachment: {{ $notice->attachment_name ?: 'PDF Attachment' }}">
                                <i class="fa fa-file-pdf-o text-danger mr-1"></i>
                                <span class="attachment-name">{{ \Illuminate\Support\Str::limit($notice->attachment_name ?: 'PDF File', 26) }}</span>
                                @if($attachmentSizeFormatted)
                                    <span class="attachment-size">({{ $attachmentSizeFormatted }})</span>
                                @endif
                            </a>
                        </div>
                    @endif
                </div>
            </td>

            {{-- 3. Target Audience --}}
            <td style="min-width: 145px;">
                <div class="d-flex flex-column gap-1">
                    <div>
                        @if($notice->audience_type === 'role')
                            <span class="badge-audience badge-audience-role"><i class="fa fa-user-circle mr-1"></i> By Role</span>
                        @elseif($notice->audience_type === 'class')
                            <span class="badge-audience badge-audience-class"><i class="fa fa-graduation-cap mr-1"></i> By Class</span>
                        @else
                            <span class="badge-audience badge-audience-specific"><i class="fa fa-users mr-1"></i> Specific People</span>
                        @endif
                    </div>
                    
                    <div class="audience-recipients-sub" title="{{ $sampleRecipients ?: ($recipientCount . ' recipients') }}">
                        <i class="fa fa-envelope-o text-muted mr-1"></i>
                        <b class="text-dark">{{ $recipientCount }}</b> <span class="text-muted font-size-10">Recipients</span>
                        @if($sampleRecipients)
                            <span class="d-block text-truncate text-muted font-size-10" style="max-width: 150px;">
                                {{ $sampleRecipients }}
                            </span>
                        @endif
                    </div>
                </div>
            </td>

            {{-- 4. Validity Period --}}
            <td style="min-width: 155px;">
                <div class="validity-box">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="validity-badge {{ $validityClass }}">{{ $validityBadge }}</span>
                    </div>
                    <div class="validity-dates">
                        <span class="validity-line">
                            <span class="lbl-dim">From:</span> 
                            <b>{{ $notice->from_date ? \Carbon\Carbon::parse($notice->from_date)->format('d M Y') : '-' }}</b>
                        </span>
                        <span class="validity-line">
                            <span class="lbl-dim">To:</span> 
                            <b>{{ $notice->to_date ? \Carbon\Carbon::parse($notice->to_date)->format('d M Y') : '-' }}</b>
                        </span>
                    </div>
                </div>
            </td>

            {{-- 5. Status --}}
            <td class="text-center" style="width: 120px;">
                @if($status === 'approved')
                    <span class="badge-status-pill badge-status-approved" title="Approved &amp; Published">
                        <i class="fa fa-check-circle mr-1"></i> Approved
                    </span>
                    @if($notice->published_at)
                        <span class="d-block font-size-10 text-muted mt-1" title="Published Time">
                            {{ \Carbon\Carbon::parse($notice->published_at)->format('d M, h:i A') }}
                        </span>
                    @endif
                @elseif($status === 'rejected')
                    <span class="badge-status-pill badge-status-rejected" title="Rejected by Admin">
                        <i class="fa fa-times-circle mr-1"></i> Rejected
                    </span>
                    @if($notice->reviewed_at)
                        <span class="d-block font-size-10 text-muted mt-1">
                            {{ \Carbon\Carbon::parse($notice->reviewed_at)->format('d M, h:i A') }}
                        </span>
                    @endif
                @else
                    <span class="badge-status-pill badge-status-pending" title="Pending Admin Approval">
                        <i class="fa fa-clock-o mr-1"></i> Pending
                    </span>
                @endif
            </td>

            {{-- 6. Creator & Reviewer --}}
            <td style="min-width: 140px;">
                <div class="creator-box">
                    <div class="creator-row" title="Created by {{ $creatorName }}">
                        <i class="fa fa-user text-muted mr-1"></i>
                        <span class="creator-name font-weight-bold text-dark">{{ $creatorName }}</span>
                    </div>
                    @if($reviewerName && $status !== 'pending')
                        <div class="reviewer-row mt-1" title="Reviewed by {{ $reviewerName }}: {{ $notice->review_notes ?: 'No notes' }}">
                            <span class="badge-reviewer {{ $status === 'approved' ? 'badge-reviewer-approved' : 'badge-reviewer-rejected' }}">
                                <i class="fa {{ $status === 'approved' ? 'fa-check' : 'fa-times' }} mr-1"></i> {{ $reviewerName }}
                            </span>
                        </div>
                    @endif
                </div>
            </td>

            {{-- 7. Created Date --}}
            <td style="min-width: 110px;" class="text-muted font-size-11">
                <span>{{ $notice->created_at ? \Carbon\Carbon::parse($notice->created_at)->format('d M Y') : '-' }}</span>
                <span class="d-block font-size-10 text-muted">{{ $notice->created_at ? \Carbon\Carbon::parse($notice->created_at)->format('h:i A') : '' }}</span>
            </td>

            {{-- 8. Actions (Sticky Right Column) --}}
            <td class="text-center fixed_action_col" style="width: 110px;">
                <div class="table-actions">
                    {{-- View Details Modal Button --}}
                    <button type="button" 
                            class="table-btn btn-action-view view-notice-btn"
                            data-notice='@json($notice)'
                            data-creator="{{ $creatorName }}"
                            data-reviewer="{{ $reviewerName }}"
                            title="View Full Notice">
                        <i class="fa fa-eye"></i>
                    </button>

                    {{-- Admin Review Button (Pending only) --}}
                    @if(!empty($isAdmin) && $status === 'pending')
                        <button type="button" 
                                class="table-btn btn-action-edit review-notice-btn"
                                data-id="{{ $noticeId }}"
                                data-title="{{ $notice->title }}"
                                data-creator="{{ $creatorName }}"
                                title="Review / Approve / Reject Notice">
                            <i class="fa fa-gavel"></i>
                        </button>
                    @endif

                    {{-- Attachment Download Button --}}
                    @if(!empty($notice->attachment_path))
                        <a href="{{ url('notice-management/' . $noticeId . '/attachment') }}" 
                           target="_blank" 
                           class="table-btn btn-action-print" 
                           title="Download PDF Attachment">
                            <i class="fa fa-download"></i>
                        </a>
                    @endif

                    {{-- Delete Notice (Admin or Creator) --}}
                    @if(!empty($isAdmin) || (int)Session::get('id') === (int)$notice->created_by)
                        <button type="button" 
                                class="table-btn btn-action-delete delete-notice-btn"
                                data-id="{{ $noticeId }}"
                                data-title="{{ $notice->title }}"
                                title="Delete Notice">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="8" class="text-center py-5">
            <div class="dash-empty-state">
                <div class="empty-icon">
                    <i class="fa fa-bell-slash-o"></i>
                </div>
                <div class="empty-title">No Notice Records Found</div>
                <div class="empty-desc">
                    No notices match your selected filter criteria. Try adjusting the search text, target audience, or status filters above.
                </div>
                <div class="mt-3">
                    <button type="button" class="dash-btn dash-btn-outline text-primary border-primary" id="btn-empty-clear-filters">
                        <i class="fa fa-refresh mr-1"></i> Reset Filters
                    </button>
                    <a href="{{ url('notice-management/create') }}" class="dash-btn dash-btn-primary ml-2">
                        <i class="fa fa-plus mr-1"></i> Create Notice
                    </a>
                </div>
            </div>
        </td>
    </tr>
@endif
