@if(count($data) != 0)
    @foreach($data as $key =>$item)
        @php
            $feesAssignedDetails = DB::table('fees_assign_details')->where('admission_id',$item->admission_id)->whereNull('deleted_at')->get();
            $count = 0;
            $rowspan = count($feesAssignedDetails);
        @endphp
        
        @if(!empty($feesAssignedDetails))
            @foreach($feesAssignedDetails as $details)
                @php
                    $feeGroupName = DB::table('fees_group')->where('id',$details->fees_group_id)->whereNull('deleted_at')->first();
                    $deletable = DB::table('fees_detail')->where('admission_id',$item->admission_id)->where('fees_group_id',$details->fees_group_id)->whereNull('deleted_at')->whereIn('status',[0,1])->sum('total_amount');
                @endphp
                <tr>
                    @if($count < 1)
                        <td rowspan="{{ $rowspan }}" class="font-weight-bold text-dark text-left align-middle" style="background:#ffffff; border-right:1px solid #cbd5e1;">
                            {{ $item->first_name ?? '' }} {{ $item->last_name ?? '' }}
                        </td>
                        <td rowspan="{{ $rowspan }}" class="align-middle" style="background:#ffffff; border-right:1px solid #cbd5e1;">
                            <span class="badge-sub-code">{{ $item->admissionNo ?? '' }}</span>
                        </td>
                        <td rowspan="{{ $rowspan }}" class="align-middle text-muted" style="background:#ffffff; border-right:1px solid #cbd5e1;">
                            {{ $item->mobile ?? '---' }}
                        </td>
                    @endif
                   
                    <td class="text-left font-weight-bold text-dark" style="vertical-align:middle;">
                        <div class="d-flex align-items-center justify-content-between" style="gap:6px;">
                            <span style="color:#002C54; font-size:11.5px;">{{ $feeGroupName->name ?? 'Fee Head' }}</span>
                            <div class="input-group input-group-sm" style="width:105px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text py-0 px-1" style="font-size:10px; background:#f1f5f9; color:#475569; height:24px; border-radius:2px 0 0 2px;">₹</span>
                                </div>
                                <input type='text' name='fees_group_amount' data-pay_fees="{{ $deletable ?? '' }}" class='form-control form-control-compact fees_assign_detail text-right font-weight-bold' data-old_value="{{ $details->fees_group_amount ?? '' }}" data-detail_id='{{ $details->id }}' value="{{ $details->fees_group_amount ?? '' }}" style="height:24px; font-size:11px; padding:2px 4px; border-radius:0 2px 2px 0; border:1px solid #cbd5e1;" />
                            </div>
                        </div>
                    </td>
                    <td style="vertical-align:middle;">
                        @if(!empty($deletable))
                            <span class="badge badge-light border text-muted px-2 py-1" style="font-size:10.5px;">₹{{ $details->discount ?? 0 }}</span>
                        @else
                            <input type='text' name='discount' class='form-control form-control-compact fees_assign_detail text-right' data-detail_id='{{ $details->id }}' data-old_value='{{ $details->discount ?? 0 }}' value="{{ $details->discount ?? '' }}" placeholder="0" style="height:24px; font-size:11px; padding:2px 5px; border-radius:2px; border:1px solid #cbd5e1;" />
                        @endif
                    </td>
                    <td style="vertical-align:middle;">
                        @if(!empty($deletable))
                            <span class="badge badge-light border text-muted px-2 py-1" style="font-size:10.5px;">@if(!empty($details->installment_due_date)) {{ date('d-M-Y', strtotime($details->installment_due_date)) }} @else - @endif</span>
                        @else
                            <input type='date' name='installment_due_date' class='form-control form-control-compact fees_assign_detail' data-detail_id='{{ $details->id }}' data-old_value='{{ $details->installment_due_date ?? 0 }}' value="{{ $details->installment_due_date ?? '' }}" style="height:24px; font-size:11px; padding:1px 3px; border-radius:2px; border:1px solid #cbd5e1;" />
                        @endif
                    </td>
                    <td style="vertical-align:middle;">
                        @if(!empty($deletable))
                            <span class="badge badge-light border text-muted px-2 py-1" style="font-size:10.5px;">{{ $details->installment_fine ?? 0 }}%</span>
                        @else
                            <input type='number' min="0" max="100" name='installment_fine' class='form-control form-control-compact fees_assign_detail text-center' data-detail_id='{{ $details->id }}' data-old_value='{{ $details->installment_fine ?? 0 }}' value="{{ $details->installment_fine ?? 0 }}" style="height:24px; font-size:11px; padding:2px 4px; border-radius:2px; border:1px solid #cbd5e1;" />
                        @endif
                    </td>
                    <td class="text-center" style="vertical-align:middle;">
                        <input type="checkbox" name="fees_refund" id="refund_fees_value_{{ $details->id ?? '' }}" 
                            {{ $details->fees_refund == 'yes' ? 'checked' : '' }} onchange="updateRefundFees(this, {{ $details->id ?? '' }})"
                            data-detail_id='{{ $details->id }}' data-old_value="{{ $details->fees_refund ?? '' }}"
                            value="{{ $details->fees_refund ?? 'no' }}" style="cursor:pointer; transform:scale(1.05);">
                    </td>
                    <td class="text-center" style="vertical-align:middle;">
                        @if(empty($deletable))
                            <button type="button" class="table-btn text-danger delete_assigned" data-detail_id='{{ $details->id }}' title="Remove Fee Head" style="background:#fee2e2; border:1px solid #fca5a5; width:22px; height:22px; border-radius:2px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center;">
                                <i class="fa fa-times" style="font-size:11px;"></i>
                            </button>
                        @else
                            <span class="text-muted" title="Fee collected (Protected)" style="font-size:11px;"><i class="fa fa-lock"></i></span>
                        @endif
                    </td>
                </tr>
                @php
                    $count++;
                @endphp
            @endforeach
        @endif
    @endforeach
@else
    <tr>
        <td colspan="9" class="text-center py-4 text-muted" style="font-size:11.5px;">
            <i class="fa fa-info-circle text-info mr-1"></i> No matching student fee records found.
        </td>
    </tr>
@endif