@if(!empty($data))
    @foreach($data as $key =>$item)
        @php
            $fees_assign_details = DB::table('fees_assign_details')->whereNull('deleted_at')->where('admission_id',$item->id)->get();
            $fees_group_ids = [];
            $fees_master_ids = [];
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
        <tr style="background-color:{{$key%2 == 0 ? '#c8c8c8' : ''}}">
               <td><input type='checkbox' name="admissionIds[]" class="student_select_checkbox" value="{{ $item->id ?? '' }}" /></td>
                 <td style="line-height: 1.2; padding: 2px 4px;">
                    {{ $item->first_name ?? '' }} {{ $item->last_name ?? '' }}<br>
                    <small style="font-size: 12px;">Address: {{ $item->address ?? '' }}</small>
                </td>               <td>{{$item->admissionNo ?? ''}}</td>
               <td>{{$item->mobile ?? ''}}</td>
               <td>{{$item->father_name ?? ''}}</td>
               <td>
                   @if(count($feesGroupData) != 0)
                      @foreach($feesGroupData as $fees_group)
                        {{ $fees_group->name ?? '' }} <br>
                      @endforeach
                   @endif
               </td>
        </tr>
@endforeach
@else
<tr class="text-center">
    <td colspan="12">
        <p class="note_text"><i class="fa fa-warning"></i><br> Either the fees have been paid or there is no data</p>
    </td>
</tr>
@endif        
    
   
    
   
    