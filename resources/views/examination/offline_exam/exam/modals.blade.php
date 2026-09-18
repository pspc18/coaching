{{-- Publish Result Modals --}}
@foreach($data as $examItem)
<div class="modal fade" id="publishResultModal{{ $examItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:2px; border:1px solid #cbd5e1; overflow:hidden;">
            <div class="modal-header modal-header-navy">
                <h5 class="modal-title font-weight-bold" style="font-size:13.5px;">
                    <i class="fa fa-bullhorn mr-1 text-warning"></i> Publish Result — {{ $examItem->name }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-info py-2 px-3 mb-3" style="font-size:11.5px; border-radius:2px;">
                    <i class="fa fa-info-circle mr-1"></i> Publish results class-wise. Students in published classes will instantly see their marksheets on their student portal and mobile app.
                </div>

                <div class="table-responsive">
                    <table class="erp-table table-bordered">
                        <thead>
                            <tr style="background:#002C54; color:#fff;">
                                <th style="text-align:left; padding:6px 10px;">Assigned Class</th>
                                <th style="width:140px; text-align:center;">Current Status</th>
                                <th style="width:160px; text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examItem->assigned_classes as $assignedClass)
                                <tr>
                                    <td style="text-align:left; padding:6px 10px; font-weight:700;">
                                        <i class="fa fa-graduation-cap text-primary mr-1"></i> {{ $assignedClass->class_name }}
                                    </td>
                                    <td style="text-align:center;">
                                        @if($assignedClass->is_published)
                                            <span class="badge badge-success" style="border-radius:2px; font-size:11px; padding:3px 8px;">
                                                <i class="fa fa-check mr-1"></i> Published
                                            </span>
                                        @else
                                            <span class="badge badge-secondary" style="border-radius:2px; font-size:11px; padding:3px 8px;">
                                                <i class="fa fa-clock-o mr-1"></i> Not Published
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($assignedClass->is_published)
                                            <form method="POST" action="{{ route('exam.result.reset-publication') }}" class="d-inline"
                                                  onsubmit="return confirm('Reset publication for {{ addslashes($examItem->name) }} — {{ addslashes($assignedClass->class_name) }}? Students will no longer see their result.');">
                                                @csrf
                                                <input type="hidden" name="exam_id" value="{{ $examItem->id }}">
                                                <input type="hidden" name="class_type_id" value="{{ $assignedClass->class_type_id }}">
                                                <button type="submit" class="btn btn-danger btn-xs" style="border-radius:2px; font-size:11px; padding:3px 8px;">
                                                    <i class="fa fa-undo mr-1"></i> Unpublish
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('exam.result.publish') }}" class="d-inline"
                                                  onsubmit="return confirm('Publish {{ addslashes($examItem->name) }} result for {{ addslashes($assignedClass->class_name) }}?');">
                                                @csrf
                                                <input type="hidden" name="exam_id" value="{{ $examItem->id }}">
                                                <input type="hidden" name="class_type_id" value="{{ $assignedClass->class_type_id }}">
                                                <button type="submit" class="btn btn-primary btn-xs" style="border-radius:2px; font-size:11px; padding:3px 8px; background:#002C54; border-color:#001f3f;">
                                                    <i class="fa fa-paper-plane mr-1"></i> Publish Result
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No classes assigned to this exam.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:2px; font-size:11.5px;">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach