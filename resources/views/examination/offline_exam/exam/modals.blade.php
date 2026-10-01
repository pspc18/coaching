{{-- Publish Result Modals (Screen-Centered & Themed) --}}
@foreach($data as $examItem)
<div class="modal fade" id="publishResultModal{{ $examItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:3px; border:1px solid #001f3d; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.25);">
            <div class="modal-header d-flex align-items-center justify-content-between" style="background:#002C54; color:#fff; padding:8px 14px; border-bottom:1px solid #001f3d;">
                <div class="d-flex align-items-center">
                    <span class="badge badge-light text-navy font-weight-bold mr-2" style="font-size:9.5px; padding:2px 6px; letter-spacing:.04em; color:#002C54;">RESULT PUBLISHING</span>
                    <h5 class="modal-title font-weight-bold mb-0" style="font-size:13px; color:#fff;">
                        <i class="fa fa-bullhorn text-info mr-1"></i> {{ $examItem->name }}
                    </h5>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.9; outline:none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" style="background:#ffffff;">
                <div class="alert alert-info py-2 px-3 mb-3" style="font-size:11.5px; border-radius:2px; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;">
                    <i class="fa fa-info-circle mr-1 text-primary"></i> Publish results class-wise. Students in published classes will instantly see their marksheets on their student portal and mobile app.
                </div>

                <div class="table-responsive">
                    <table class="dash-table table-bordered mb-0" style="width:100%; border:1px solid #e2e8f0; font-size:11.5px;">
                        <thead>
                            <tr style="background:#002C54; color:#fff;">
                                <th style="text-align:left; padding:7px 10px; font-size:11px; text-transform:uppercase;">Assigned Class</th>
                                <th style="width:140px; text-align:center; padding:7px 10px; font-size:11px; text-transform:uppercase;">Current Status</th>
                                <th style="width:170px; text-align:center; padding:7px 10px; font-size:11px; text-transform:uppercase;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="publishModalTbody{{ $examItem->id }}">
                            @forelse($examItem->assigned_classes as $assignedClass)
                                <tr class="publish-class-row" data-exam-id="{{ $examItem->id }}" data-class-id="{{ $assignedClass->class_type_id }}">
                                    <td style="text-align:left; padding:7px 10px; font-weight:600; color:#0f172a;">
                                        <i class="fa fa-graduation-cap text-primary mr-1"></i> {{ $assignedClass->class_name }}
                                        @if(!empty($assignedClass->exam_date))
                                            <small class="text-muted ml-1" style="font-size:10px;">({{ date('d M Y', strtotime($assignedClass->exam_date)) }})</small>
                                        @endif
                                    </td>
                                    <td style="text-align:center; padding:7px 10px;" class="status-cell">
                                        @if($assignedClass->is_published)
                                            <span class="badge badge-success px-2 py-1" style="font-size:10.5px; background:#10b981;">
                                                <i class="fa fa-check-circle mr-1"></i> Published
                                            </span>
                                        @else
                                            <span class="badge badge-secondary px-2 py-1" style="font-size:10.5px; background:#94a3b8;">
                                                <i class="fa fa-clock-o mr-1"></i> Not Published
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align:center; padding:7px 10px;" class="action-cell">
                                        @if($assignedClass->is_published)
                                            <button type="button" 
                                                    class="btn btn-xs btn-outline-danger btn-ajax-toggle-publish" 
                                                    data-action="unpublish" 
                                                    data-url="{{ route('exam.result.reset-publication') }}"
                                                    data-exam-id="{{ $examItem->id }}" 
                                                    data-class-id="{{ $assignedClass->class_type_id }}" 
                                                    data-exam-name="{{ $examItem->name }}" 
                                                    data-class-name="{{ $assignedClass->class_name }}"
                                                    style="font-size:11px; padding:2px 8px; font-weight:600; border-radius:2px;">
                                                <i class="fa fa-undo mr-1"></i> Unpublish
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-xs btn-success btn-ajax-toggle-publish" 
                                                    data-action="publish" 
                                                    data-url="{{ route('exam.result.publish') }}"
                                                    data-exam-id="{{ $examItem->id }}" 
                                                    data-class-id="{{ $assignedClass->class_type_id }}" 
                                                    data-exam-name="{{ $examItem->name }}" 
                                                    data-class-name="{{ $assignedClass->class_name }}"
                                                    style="font-size:11px; padding:2px 8px; font-weight:600; border-radius:2px; background:#002C54; border-color:#001f3f;">
                                                <i class="fa fa-paper-plane mr-1"></i> Publish Result
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3" style="font-size:11px;">
                                        <i class="fa fa-info-circle mr-1"></i> No classes assigned to this exam.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 d-flex align-items-center justify-content-between" style="background:#f8fafc; border-top:1px solid #cbd5e1;">
                <span class="text-muted" style="font-size:10.5px;">
                    <i class="fa fa-bolt text-warning mr-1"></i> Instant 1-click update without page refresh.
                </span>
                <button type="button" class="dash-btn" style="background:#e2e8f0; color:#1e293b; border:1px solid #cbd5e1; padding:2px 10px; font-size:11px;" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach