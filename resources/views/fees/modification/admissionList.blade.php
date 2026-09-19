@if(!empty($data) && count($data) > 0)
    @foreach($data as $key =>$item)
        @php
            $fees_assign_details = DB::table('fees_assign_details')->whereNull('deleted_at')->where('admission_id',$item->id)->get();
            $fees_group_ids = [];
            if(!empty($fees_assign_details)){
                foreach($fees_assign_details as $fees){
                    $fees_group_ids[] = $fees->fees_group_id;
                }
            }
            
            $feesGroupData = [];
            if(count($fees_group_ids) != 0){
                $feesGroupData = DB::table('fees_group')->whereNull('deleted_at')->whereIn('id',$fees_group_ids)->get();
            }
        @endphp
        <tr>
            <td class="text-center" style="width:36px; vertical-align:middle;">
                <input type='checkbox' name="admissionIds[]" class="student_select_checkbox" value="{{ $item->id ?? '' }}" style="cursor:pointer;" />
            </td>
            <td class="text-left font-weight-bold text-dark" style="vertical-align:middle;">
                {{ $item->first_name ?? '' }} {{ $item->last_name ?? '' }}
                @if(!empty($item->address))
                    <div style="font-size:10px; color:#64748b; font-weight:normal; margin-top:1px;"><i class="fa fa-map-marker text-muted mr-1"></i>{{ $item->address }}</div>
                @endif
            </td>
            <td style="vertical-align:middle;"><span class="badge-sub-code">{{ $item->admissionNo ?? '' }}</span></td>
            <td style="vertical-align:middle;">{{ $item->mobile ?? '---' }}</td>
            <td class="text-left text-muted" style="vertical-align:middle;">{{ $item->father_name ?? '---' }}</td>
            <td class="text-left" style="vertical-align:middle;">
                @if(count($feesGroupData) != 0)
                    @foreach($feesGroupData as $fees_group)
                        <span class="badge-head" style="margin:1px 2px 1px 0; font-size:10px;">{{ $fees_group->name ?? '' }}</span>
                    @endforeach
                @else
                    <span class="text-muted" style="font-size:10.5px;">No Heads</span>
                @endif
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <i class="fa fa-info-circle text-info mr-1"></i> Either no enrolled students found or fees already paid.
        </td>
    </tr>
@endif