@forelse ($rows as $item)
    @php
        $isActive = (Session::get('session_id') == $item->id);
    @endphp
    <tr>
        <td class="text-center font-weight-bold text-muted">{{ $item->id }}</td>
        <td>
            <div class="d-flex align-items-center" style="gap: 8px;">
                <span class="d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; border-radius: 4px; background: {{ $isActive ? '#ecfdf5' : '#f1f5f9' }}; color: {{ $isActive ? '#059669' : '#002C54' }}; font-size: 12px;">
                    <i class="fa fa-calendar-check-o"></i>
                </span>
                <div>
                    <span class="font-weight-bold text-dark" style="font-size: 12px;">
                        {{ $item->from_year }} - {{ $item->to_year }}
                    </span>
                    @if($isActive)
                        <span class="badge badge-success ml-2" style="font-size: 9.5px; padding: 2px 6px; font-weight: 600; border-radius: 2px;">Active Session</span>
                    @endif
                </div>
            </div>
        </td>
        <td class="text-center font-weight-600 text-dark">{{ $item->from_year ?? '—' }}</td>
        <td class="text-center font-weight-600 text-dark">{{ $item->to_year ?? '—' }}</td>
        <td class="text-center">
            @if($isActive)
                <span class="badge" style="background:#ecfdf5; color:#047857; border: 1px solid #a7f3d0; font-size: 10px; padding: 2px 7px; border-radius: 2px;">
                    <i class="fa fa-check-circle mr-1"></i> Current Active
                </span>
            @else
                <span class="badge" style="background:#f8fafc; color:#64748b; border: 1px solid #cbd5e1; font-size: 10px; padding: 2px 7px; border-radius: 2px;">
                    Standard
                </span>
            @endif
        </td>
        <td class="text-center fixed_action_col">
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
                    <i class="fa fa-trash-o"></i>
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