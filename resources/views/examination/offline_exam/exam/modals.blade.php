{{-- Dynamic Single Publish Result Modal --}}
<div class="modal fade" id="publishResultModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
        <div class="modal-content" style="border-radius: 4px; border: 1px solid #002C54; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25);">
            <div class="modal-header d-flex align-items-center justify-content-between" style="background:#002C54; color:#fff; padding:10px 16px;">
                <div class="d-flex align-items-center">
                    <span class="badge badge-light text-navy font-weight-bold mr-2" style="font-size:9.5px; padding:2px 6px; letter-spacing:.04em; color:#002C54;">RESULT PUBLISHING</span>
                    <h5 class="modal-title font-weight-bold mb-0" style="font-size:13.5px; color:#fff;">
                        <span id="pub_modal_exam_title">Examination</span>
                    </h5>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.9; outline:none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" style="background:#ffffff;">
                <input type="hidden" id="pub_modal_exam_id">
                <input type="hidden" id="pub_modal_class_id">
                
                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div>
                        <span class="text-muted" style="font-size: 11px;">Course:</span>
                        <strong id="pub_modal_class_name" class="ml-1" style="color: #002C54; font-size: 12.5px;"></strong>
                    </div>
                    <div>
                        <span class="text-muted" style="font-size: 11px;">Current Status:</span>
                        <span id="pub_modal_status_badge" class="ml-1"></span>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label style="font-size: 11.5px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                        <i class="fa fa-calendar text-primary mr-1"></i> Result Publication Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" id="pub_modal_date" class="form-control" style="height: 34px; font-size: 12px; border-radius: 3px; border: 1px solid #cbd5e1; font-weight: 500; color: #0f172a;">
                    <div class="text-muted mt-1" style="font-size: 10.5px;">
                        Marksheets will be visible to students and parents on their student portal and mobile app from this date.
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 11px; border-radius: 3px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                    <i class="fa fa-bolt text-warning mr-1"></i> Instant 1-click update without page refresh.
                </div>
            </div>
            <div class="modal-footer py-2 px-3 d-flex align-items-center justify-content-between" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size: 11px; padding: 4px 12px;">Close</button>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <button type="button" class="btn btn-sm btn-outline-danger" id="pub_modal_btn_unpublish" style="font-size: 11px; padding: 4px 12px; display: none;">
                        <i class="fa fa-undo mr-1"></i> Unpublish Result
                    </button>
                    <button type="button" class="btn btn-sm btn-success" id="pub_modal_btn_submit" style="font-size: 11px; padding: 4px 16px; font-weight: 600;">
                        <i class="fa fa-paper-plane mr-1"></i> Publish Result
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
