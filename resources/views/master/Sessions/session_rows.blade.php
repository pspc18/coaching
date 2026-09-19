@forelse ($rows as $item)
    @php
        $isActive = (Session::get('session_id') == $item->id);
    @endphp
    <tr>
        <td class="text-center">
            <span class="badge-session-id">#{{ $item->id }}</span>
        </td>
        <td>
            <div class="d-flex align-items-center" style="gap: 8px;">
                <span class="d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; min-width: 26px; border-radius: 2px; background: {{ $isActive ? '#ecfdf5' : '#eff6ff' }}; color: {{ $isActive ? '#047857' : '#1e40af' }}; font-size: 12px; border: 1px solid {{ $isActive ? '#a7f3d0' : '#bfdbfe' }};">
                    <i class="fa fa-calendar-check-o"></i>
                </span>
                <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                    <span class="font-weight-bold text-dark" style="font-size: 12px;">
                        {{ $item->from_year }} - {{ $item->to_year }}
                    </span>
                    @if($isActive)
                        <span class="badge-session-active-pill">
                            <i class="fa fa-check mr-1"></i>Active
                        </span>
                    @endif
                </div>
            </div>
        </td>
        <td class="text-center">
            <span class="badge-session-year">{{ $item->from_year ?? '—' }}</span>
        </td>
        <td class="text-center">
            <span class="badge-session-year">{{ $item->to_year ?? '—' }}</span>
        </td>
        <td class="text-center">
            @if($isActive)
                <span class="badge-session-active">
                    <i class="fa fa-check-circle"></i> Current Active
                </span>
            @else
                <span class="badge-session-standard">
                    <i class="fa fa-clock-o"></i> Standard
                </span>
            @endif
        </td>
        <td class="text-center fixed_action_col" style="white-space: nowrap;">
            <div class="d-inline-flex align-items-center" style="gap: 4px;">
                <button type="button" class="btn-action-icon btn-action-edit btnEditSession"
                    data-id="{{ $item->id }}"
                    data-from="{{ $item->from_year }}"
                    data-to="{{ $item->to_year }}"
                    title="Edit Session">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn-action-icon btn-action-delete btnDeleteSession"
                    data-id="{{ $item->id }}"
                    data-name="{{ $item->from_year }} - {{ $item->to_year }}"
                    title="Delete Session">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="6">
            <div class="dash-empty-state">
                <div class="empty-icon">
                    <i class="fa fa-calendar-times-o"></i>
                </div>
                <div class="empty-title">No Academic Sessions Found</div>
                <div class="empty-desc">There are no academic session records matching your current filter criteria. Click "Add New Session" to create a new session.</div>
            </div>
        </td>
    </tr>
@endforelse