@php
   $classType = Helper::classType();
      $getSection = Helper::getSection();
      $getgenders = Helper::getgender();
         $getState = Helper::getState();
        $getCity = Helper::getCity();
        $getCountry = Helper::getCountry();
   @endphp

@if(!empty($data))
<form action="{{url('')}}" method="post">
    @if($data->count() > 0)
            @php
               $i=1
            @endphp
            @foreach ($data  as $item)
            
            <tr>
                <td>{{ $i++ }}</td>
                <td><img src="
                
                
                
                
                {{ !empty($item['image']) ? env('IMAGE_SHOW_PATH').'profile/'.$item['image'] :  env('IMAGE_SHOW_PATH').'default/user_image.jpg'}}" width="50px" height="50px" /> <input type="file" name="image[]"  /></td>
                <td><input type="text" name="admissionNo[]" value="{{ $item['admissionNo'] ?? '' }}" disabled/></td>
                <td><input type="text" name="first_name[]" value="{{ $item['name'] ?? '' }}" /></td>
                <td>	<select class="form-control select2" id="class_type_id" name="class_type_id" >
            			<option value="">{{ __('messages.Select') }}</option>
                         @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{$item['class_type_id'] == $type->id ? 'selected' : ''}} >{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
                        </td>
                <td>			<select class="select2 form-control section_id" id="" name="section_id" >
            			   <option value="">{{ __('messages.Select') }}</option>
                         @if(!empty($getSection)) 
                              @foreach($getSection as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{$item['section_id'] == $type->id ? 'selected' : ''}}>{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
                        </td>
                        <td><input type="date" name="dob[]" value="{{ $item['dob'] ?? '' }}" /></td>
                        <td>
                        	<select class="form-control invalid" id="gender_id" name="gender_id">
											<option value="">{{ __('messages.Select') }}</option>
											@if(!empty($getgenders))
											@foreach($getgenders as $value)
											<option value="{{ $value->id}}" {{ ($value->id == $item['gender_id']) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
                        
                        </td>
                        <td><input type="text" name="email[]" value="{{ $item['email'] ?? '' }}" /></td>
                        <td>{{ $item['userName'] ?? '' }}</td>
                        <td>{{ $item['confirm_password'] ?? '' }}</td>
                        <td><input type="text" name="mobile[]" value="{{ $item['mobile'] ?? '' }}" /></td>
                       <td>
                           
                           <img src="{{ !empty($item['father_img']) ? env('IMAGE_SHOW_PATH').'father_image/'.$item['father_img'] :  env('IMAGE_SHOW_PATH').'default/user_image.jpg'}}" width="50px" height="50px" /> <input type="file" name="father_img[]"  /></td>
                        <td><input type="text" name="father_name[]" value="{{ $item['father_name'] ?? '' }}" /></td>
                        <td><input type="text" name="father_mobile[]" value="{{ $item['father_mobile'] ?? '' }}" /></td>
                         <td><img src="{{ !empty($item['mother_img']) ? env('IMAGE_SHOW_PATH').'mother_image/'.$item['mother_img'] :  env('IMAGE_SHOW_PATH').'default/user_image.jpg'}}" width="50px" height="50px" /> <input type="file" name="mother_img[]"  /></td>
                        <td><input type="text" name="mother_name[]" value="{{ $item['mother_name'] ?? '' }}" /></td>
                        <td><input type="text" name="aadhaar[]" value="{{ $item['aadhaar'] ?? '' }}" /></td>
                        <td><input type="text" name="roll_no[]" value="{{ $item['roll_no'] ?? '' }}" /></td>
                        <td><textarea type="text" name="address[]"  >{{ $item['address'] ?? '' }}</textarea></td>
                        <td>
                            
                            		<select class="form-control" name="country" id="country_id">
											<option value="">{{ __('messages.Select') }}</option>
											@if(!empty($getCountry))
											@foreach($getCountry as $country)
											<option value="{{ $country->id ?? ''  }}" {{ ($country->id == $item['country_id']) ? 'selected' : '' }}>{{ $country->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
                        </td>
                        <td>
                            
                            		<select class="form-control stateId " id="state_id" name="state">
											<option value="">{{ __('messages.Select') }}</option>
											@if(!empty($getState))
											@foreach($getState as $state)
											<option value="{{ $state->id ?? ''}}" {{ ($state->id == $item['state_id']) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
											@endforeach
											@endif
										</select>
                        </td>
                        <td>
                            
                            	<select class="form-control cityId " name="city" id="city_id">
											<option value="">{{ __('messages.Select') }}</option>
											@if(!empty($getCity))
											@foreach($getCity as $cities)
											<option value="{{ $cities->id ?? ''  }}" {{ ($cities->id == $item['city_id']) ? 'selected' : '' }}>{{ $cities->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
                        </td>
                         <td><input type="text" name="village_city[]" value="{{ $item['village_city'] ?? '' }}" /></td>
                         <td><input type="text" name="pincode[]" value="{{ $item['pincode'] ?? '' }}" /></td>
                        <td>
                             
                            
                            <select class="form-control invalid" id="admission_type_id" name="admission_type_id">
											<option value="">{{ __('messages.Select') }}</option>
											<option value="1" {{ (1 == $item['admission_type_id']) ? 'selected' : '' }}>Regular</option>
											<option value="2" {{ (2 == $item['admission_type_id']) ? 'selected' : '' }}>Non</option>
											<option value="3" {{ (3 == $item['admission_type_id']) ? 'selected' : '' }}>Other</option>
										</select>
                        </td>
                       
                         <td><input type="text" name="remark_1[]" value="{{ $item['remark_1'] ?? '' }}" /></td>
                <td>
                @php
                $stu_all = Helper::stuAddDetails($item->id);
                $stu_hostel = Helper::hostel($item->id);
                $stu_transport = Helper::transport($item->id);
                $stu_fees = Helper::stuFeesDetails($item->id);
                @endphp
                    <!--<button class="getAllData btn btn-primary"-->
                    <!--data-photo="{{$stu_all->image ?? ''}}"-->
                    <!--data-data="<div class='tabs'>-->
                    <!--            <div class='tab-header'>-->
                    <!--                <div class='tab-item active'>Personal Details</div>-->
                    <!--                <div class='tab-item'>Guardian Details</div>-->
                    <!--                <div class='tab-item'>Transport Details</div>-->
                    <!--                <div class='tab-item'>Hostel Details</div>-->
                    <!--                <div class='tab-item'>Assign Fees Details</div>-->
                    <!--                <div class='tab-item'>Collect Fees Details</div>-->
                    <!--            </div>-->
                    <!--            <div class='tab-content'>-->
                    <!--                <div class='tab-pane active' style='overflow-x: scroll;'>-->
                    <!--                <table class='table table-bordered student_new_table'>-->
                    <!--                    <thead>-->
                    <!--                      <tr>-->
                    <!--                        <th>Admission No.</th>-->
                    <!--                        <th>Name</th>-->
                    <!--                        <th>Mobile</th>-->
                    <!--                        <th>Email</th>-->
                    <!--                        <th>Class</th>-->
                    <!--                        <th>Stream</th>-->
                    <!--                        <th>Aadhaar</th>-->
                    <!--                        <th>Address</th>-->
                    <!--                        <th>Dob</th>-->
                    <!--                        <th>Admission Date</th>-->
                    <!--                      </tr>-->
                    <!--                    </thead>-->
                    <!--                    <tbody>-->
                    <!--                      <tr>-->
                    <!--                        <td>{{$stu_all->admissionNo ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->name ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->mobile ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->email ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->class_name ?? ''}}({{$stu_all->section_name ?? '--'}})</td>-->
                    <!--                        <td>{{$stu_all->stream_subject ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->aadhaar ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->address ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->dob ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->admission_date ?? '--'}}</td>-->
                    <!--                      </tr>-->
                    <!--                    </tbody>-->
                    <!--                  </table>-->
                    <!--                </div>-->
                                    
                                    
                    <!--                <div class='tab-pane'>-->
                    <!--                <table class='table table-bordered student_new_table'>-->
                    <!--                    <thead>-->
                    <!--                      <tr>-->
                    <!--                        <th>Father's Name</th>-->
                    <!--                        <th>Mother's Name</th>-->
                    <!--                        <th>Father Mobile</th>-->
                    <!--                      </tr>-->
                    <!--                    </thead>-->
                    <!--                    <tbody>-->
                    <!--                      <tr>-->
                    <!--                        <td>{{$stu_all->father_name ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->mother_name ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_all->father_mobile ?? '--'}}</td>-->
                    <!--                      </tr>-->
                    <!--                    </tbody>-->
                    <!--                  </table>-->
                    <!--                </div>-->
                                    
                                    
                    <!--                <div class='tab-pane'>-->
                    <!--                <table class='table table-bordered student_new_table'>-->
                    <!--                    <thead>-->
                    <!--                      <tr>-->
                    <!--                        <th>Bus Name</th>-->
                    <!--                        <th>Bus No.</th>-->
                    <!--                        <th>Bus Owmer Name</th>-->
                    <!--                        <th>Owner No.</th>-->
                    <!--                        <th>Route Name</th>-->
                    <!--                      </tr>-->
                    <!--                    </thead>-->
                    <!--                    <tbody>-->
                    <!--                      <tr>-->
                    <!--                        <td>{{$stu_transport->busName ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_transport->bus_no ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_transport->bus_owmer_name ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_transport->owner_no ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_transport->routeName ?? '--'}}</td>-->
                    <!--                      </tr>-->
                    <!--                    </tbody>-->
                    <!--                  </table>-->
                    <!--                </div>-->
                                    
                    <!--                <div class='tab-pane'>-->
                    <!--                  <table class='table table-bordered student_new_table'>-->
                    <!--                    <thead>-->
                    <!--                      <tr>-->
                    <!--                        <th>Hostel Name</th>-->
                    <!--                        <th>Hostel Building Name</th>-->
                    <!--                        <th>Hostel Floor Name</th>-->
                    <!--                        <th>Hostel Room Name</th>-->
                    <!--                        <th>Hostel Bed Name</th>-->
                    <!--                      </tr>-->
                    <!--                    </thead>-->
                    <!--                    <tbody>-->
                    <!--                      <tr>-->
                    <!--                        <td>{{$stu_hostel->hostelName ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_hostel->hostelBuildingName ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_hostel->hostelFloorName ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_hostel->hostelRoomName ?? '--'}} ({{$stu_hostel->roomCategory ?? '--'}})</td>-->
                    <!--                        <td>{{$stu_hostel->hostelBedName ?? '--'}}</td>-->
                    <!--                      </tr>-->
                    <!--                    </tbody>-->
                    <!--                  </table>-->
                    <!--                </div>-->
                                    
                    <!--                <div class='tab-pane'>-->
                    <!--                <table class='table table-bordered student_new_table'>-->
                    <!--                    <thead>-->
                    <!--                      <tr>-->
                    <!--                        <th>Net Amount</th>-->
                    <!--                        <th>Total Amount</th>-->
                    <!--                        <th>Dis. On Total Amt.</th>-->
                    <!--                      </tr>-->
                    <!--                    </thead>-->
                    <!--                    <tbody>-->
                    <!--                      <tr>-->
                    <!--                        <td>{{$stu_fees['assign']->net_amount ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_fees['assign']->total_amount ?? '--'}}</td>-->
                    <!--                        <td>{{$stu_fees['assign']->dis_on_total_amt ?? '--'}}</td>-->
                    <!--                      </tr>-->
                    <!--                    </tbody>-->
                    <!--                  </table>-->
                    <!--                </div>-->
                                    
                    <!--                <div class='tab-pane'>-->
                    <!--                        <table class='table table-bordered student_new_table'>-->
                    <!--                            <thead>-->
                    <!--                              <tr>-->
                    <!--                                <th>Amount</th>-->
                    <!--                                <th>Discount</th>-->
                    <!--                                <th>Date</th>-->
                    <!--                                <th>Payment Mode</th>-->
                    <!--                               </tr>-->
                    <!--                            </thead>-->
                    <!--                            <tbody>-->
                    <!--                              @foreach($stu_fees['collect'] as $items)-->
                    <!--                                <tr>-->
                    <!--                                <td>{{$items->amount ?? '--'}}</td>-->
                    <!--                                <td>{{$items->discount ?? '--'}}</td>-->
                    <!--                                <td>{{$items->date ?? '--'}}</td>-->
                    <!--                                <td>{{$items->paymentName ?? '--'}}</td>-->
                    <!--                                </tr>-->
                    <!--                              @endforeach-->
                    <!--                            </tbody>-->
                    <!--                          </table>-->
                    <!--                </div>-->
                                    
                                    
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--"data-toggle="modal" data-target="#StuDetails">View</button>-->
                </td>
            </tr>
       @endforeach
       </form>
    @else
        <tr>
            <td colspan="12" class="text-center">No Students Found !</td>
        </tr>
@endif
                
@endif   

<style>
    .student_new_table{
        white-space:nowrap;
    }
    
    .student_new_table th, .student_new_table td{
        padding:10px;
    }
</style>

                 