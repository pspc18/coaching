@php
    $tasks = collect($task ?? []);
@endphp

<style>
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    min-height: 160px;
    padding: 16px 12px;
    text-align: center;
    color: #94a3b8;
}
.dash-empty-state .empty-icon {
    font-size: 24px;
    color: #cbd5e1;
    margin-bottom: 6px;
    line-height: 1;
}
.dash-empty-state .empty-title {
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 2px;
}
.dash-empty-state .empty-desc {
    font-size: 11px;
    color: #94a3b8;
    line-height: 1.35;
    max-width: 280px;
}

.task-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 6px 10px;
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
    transition: background 0.15s;
}
.task-item:hover {
    background: #f8fafc;
}
.task-item:last-child {
    border-bottom: none;
}
.task-item-content {
    flex: 1;
    min-width: 0;
}
.task-item-top {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.task-title {
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.3;
}
.task-badges {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.task-badge {
    font-size: 9.5px;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 2px;
    line-height: 1.2;
    display: inline-flex;
    align-items: center;
}
.badge-user { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.badge-days-green { background: #ecfdf5; color: #059669; }
.badge-days-blue { background: #eff6ff; color: #2563eb; }
.badge-days-red { background: #fef2f2; color: #dc2626; }
.badge-prio-low { background: #f0fdf4; color: #16a34a; }
.badge-prio-medium { background: #eff6ff; color: #0284c7; }
.badge-prio-high { background: #fef2f2; color: #dc2626; font-weight: 700; }
.badge-status-0 { background: #fef2f2; color: #dc2626; }
.badge-status-1 { background: #fffbeb; color: #d97706; }
.badge-status-2 { background: #f0fdf4; color: #16a34a; }
.badge-status-3 { background: #ecfdf5; color: #059669; }

.task-desc {
    font-size: 11px;
    color: #64748b;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 380px;
}
.task-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.task-action-btn {
    width: 24px;
    height: 24px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s;
}
.task-action-btn.btn-view {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}
.task-action-btn.btn-view:hover {
    background: #059669;
    color: #fff;
}
.task-action-btn.btn-del {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.task-action-btn.btn-del:hover {
    background: #dc2626;
    color: #fff;
}
</style>

@if($tasks->isEmpty())
    <div class="dash-empty-state">
        <div class="empty-icon"><i class="fa fa-clipboard-check"></i></div>
        <div class="empty-title">No Assigned Tasks Found</div>
        <div class="empty-desc">All operational tasks are completed or none assigned.</div>
    </div>
@else
    @foreach($tasks as $item)
        @php
            $fdate = $item->created_at;
            $tdate = date('Y-m-d H:i:s');
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');
            $priority = strtolower($item->priority ?? 'medium');
            $status = (int)($item->status ?? 0);
            $statusNames = [0 => 'Pending', 1 => 'Working', 2 => 'Completed', 3 => 'Verified'];
            $statusText = $statusNames[$status] ?? 'Pending';
        @endphp
        <li class="task-item" id="_{{ $item->id ?? '' }}">
            <div class="task-item-content">
                <div class="task-item-top">
                    <span class="task-title">{{ $item->name ?? 'Task' }}</span>
                    <div class="task-badges">
                        @if(!empty($item->first_name))
                            <span class="task-badge badge-user"><i class="fa fa-user mr-1"></i>{{ $item->first_name }}</span>
                        @endif
                        <span class="task-badge badge-days-{{ $days <= 2 ? 'green' : ($days <= 6 ? 'blue' : 'red') }}">
                            <i class="fa fa-clock-o mr-1"></i>{{ $days }}d
                        </span>
                        <span class="task-badge badge-prio-{{ $priority }}">
                            {{ ucfirst($priority) }}
                        </span>
                        <span class="task-badge badge-status-{{ $status }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
                @if(!empty($item->description))
                    <div class="task-desc" title="{{ $item->description }}">{{ $item->description }}</div>
                @endif
            </div>
            <div class="task-actions">
                <form action="{{ url('to_do_assign_view') }}" method="post" class="d-inline m-0">
                    @csrf
                    <input type="hidden" name="to_do_list_id" value="{{ $item->id ?? '' }}">
                    <button type="submit" class="task-action-btn btn-view" title="View Details">
                        <i class="fa fa-eye"></i>
                    </button>
                </form>
                @if(Session::get('role_id') == 1)
                    <button type="button" class="task-action-btn btn-del task_delete" data-id="{{ $item->id ?? '' }}" title="Delete Task">
                        <i class="fa fa-trash-o"></i>
                    </button>
                @endif
            </div>
        </li>
    @endforeach
@endif
