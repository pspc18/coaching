@forelse($complaints as $complaint)
    @php
        $student = $complaint->student;
        $studentName = trim(($student->first_name ?? '').' '.($student->last_name ?? '')) ?: 'Student';
        $firstChar = strtoupper(mb_substr(trim($student->first_name ?? 'S'), 0, 1));
        $hasImage = !empty($student->image) && file_exists(public_path('images/student/'.$student->image));
    @endphp
    <tr>
        {{-- 1. Ticket No --}}
        <td class="text-center font-weight-bold">
            <a class="ticket-link" href="{{ url('complaints-management/'.$complaint->id) }}" title="Open ticket #{{ $complaint->ticket_no }}">
                <i class="fa fa-ticket"></i> {{ $complaint->ticket_no }}
            </a>
        </td>

        {{-- 2. Submitted By / Student Details --}}
        <td>
            <div class="student-cell">
                @if($hasImage)
                    <img src="{{ asset('public/images/student/'.$student->image) }}" class="student-avatar-img" alt="{{ $studentName }}">
                @else
                    <div class="student-avatar-fallback">{{ $firstChar }}</div>
                @endif
                <div class="student-info-box">
                    <span class="student-name-text">{{ $studentName }}</span>
                    <span class="student-sub-text">
                        @if(!empty($student->admissionNo))
                            <span class="text-secondary mr-1">Adm: {{ $student->admissionNo }}</span> &bull;
                        @endif
                        As {{ ucfirst($complaint->submitted_as ?? 'student') }}
                    </span>
                </div>
            </div>
        </td>

        {{-- 3. Subject & Concern --}}
        <td>
            <span class="subject-text" title="{{ $complaint->subject }}">
                {{ $complaint->subject }}
            </span>
        </td>

        {{-- 4. Category --}}
        <td class="text-center">
            <span class="category-pill">
                {{ \App\Models\SupportComplaint::CATEGORIES[$complaint->category] ?? ucfirst($complaint->category) }}
            </span>
        </td>

        {{-- 5. Priority --}}
        <td class="text-center">
            <span class="priority-pill priority-{{ $complaint->priority }}">
                @if($complaint->priority === 'urgent')
                    <i class="fa fa-exclamation-triangle"></i>
                @endif
                {{ ucfirst($complaint->priority) }}
            </span>
        </td>

        {{-- 6. Status --}}
        <td class="text-center">
            <span class="status-pill status-pill-{{ $complaint->status }}">
                <span class="status-dot"></span>
                {{ \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status) }}
            </span>
        </td>

        {{-- 7. Reply Count --}}
        <td class="text-center">
            <span class="replies-count-box" title="{{ $complaint->replies_count }} replies recorded">
                <i class="fa fa-comment-o"></i> {{ $complaint->replies_count }}
            </span>
        </td>

        {{-- 8. Last Activity --}}
        <td>
            <span class="time-main">{{ optional($complaint->last_replied_at)->diffForHumans() ?? 'N/A' }}</span>
            <span class="time-sub">{{ optional($complaint->last_replied_at)->format('d M Y, h:i A') }}</span>
        </td>

        {{-- 9. Sticky Action Column --}}
        <td class="text-center fixed_action_col">
            <a class="btn-view-ticket" href="{{ url('complaints-management/'.$complaint->id) }}" title="View Ticket Details">
                <i class="fa fa-eye"></i> View
            </a>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="9" class="text-center py-5">
            <div class="empty-state-box">
                <div class="empty-state-icon">
                    <i class="fa fa-ticket"></i>
                </div>
                <h6 class="empty-state-title">No complaints found</h6>
                <p class="empty-state-desc">No support tickets match your active filter criteria. Try clearing or adjusting your filters.</p>
                <button type="button" class="btn-clear-empty-state" onclick="$('#btn-clear-filters').click();">
                    <i class="fa fa-refresh mr-1"></i> Reset Filters
                </button>
            </div>
        </td>
    </tr>
@endforelse
