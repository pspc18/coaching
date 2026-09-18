@if(!empty($data) && count($data) > 0)
    <div class="class-subject-box">
        <div class="col-3 homework_panel_{{$data[0]->class_type_id ?? ''}} text-primary">
            <h3 class="font_text">{{$data[0]->class_name ?? ''}}</h3>
        </div>
        <div class="col-9 homework_panel_{{$data[0]->class_type_id ?? ''}} mb-2"></div>

        <div class="row">
            @foreach($data as $item)
                <div class="col-3 homework_panel_{{$data[0]->class_type_id ?? ''}} borderd">
                    <div class="form-group">
                        <input type="checkbox" 
                               name="subjects_id[]" 
                               value="{{$item->id}}" 
                               {{ in_array($item->id, $subjectType) ? 'checked' : '' }}>
                        <label class="small_sub_text">
                            {{$item->class_type_id > 13 ?  $item->sub_name." [".$item->name."]" : $item->name}}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
