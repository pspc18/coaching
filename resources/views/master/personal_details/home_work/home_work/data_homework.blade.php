                    <div class="card-body table-responsive p-0" style="height: 420px; overflow-y: scroll;">
                        <table class="table table-head-fixed table-bordered table-striped dataTable" style="margin-top:0px !important;">
                            <thead>
                                <tr>
        							<th>Sr. No.</th>
        							<th>Date</th>
        							<th width="10%">Message</th>
        							<th>Assignments</th>
        							@if(Session::get('role_id') !== 3)
        							<th>Action</th>
        							@endif
                                </tr>
                        </thead>
        					<tbody>
            					@if(!empty($data)) 
            						@php 
            						    $i=1 
            						@endphp 
            					@foreach ($data as $key1=>$type)
                                    @php
                                    $hwDocument = Helper::getHwDocument($type['id']);
                                 
                                    @endphp    
                                  
            						<tr>
            						    <td id="stuName" data-first_name="{{ $type['Admission']['first_name'] ?? '' }} {{ $type['Admission']['last_name'] ?? '' }}">{{ $i++ }}</td>
            							<td>{{ $type['submission_date'] ?? '' }}
            							@if(Session::get('role_id') == 3)
                                            @if($type['email_status'] == 1)
                                            <small class="badge badge-success"><i class="fa fa-check"></i> Email Sent</small>
                                            @else
                                            <button type="button" id="resendEmail" class="btn btn-primary btn-xs">Resend-Email</button>
                                            @endif            							
            							@endif
            							</td>
            							<td>{{ $type['message'] ?? '' }}</td>
                                        <td class="row">
                                            @if(!empty($hwDocument)) 
                                                @foreach($hwDocument as $key=>$info)
                                                    <div class="col-md-3 p-1">
                                                    <img src="{{ env('IMAGE_SHOW_PATH').'uploadHomework/'.$info->content_file }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/bus_photo/pdf_image.webp' }}'">
                                                    <span class="">
                                                    <a href="{{ url('download_assignment') }}/{{$info->content_file }}" class="btn btn-primary  btn-xs ml-3" title="Download Assignments" ><i class="fa fa-download"></i></a>
                                                    <a href="{{ env('IMAGE_SHOW_PATH').'uploadHomework/'.$info->content_file }}" target="blank" class="btn btn-warning btn-xs" title="View Assignment" ><i class="fa fa-eye"></i></a>     
                                                    </span>
                    									<!--<input type="text" class="form-control submit_{{$key1}}" id="message_{{ $key }}" name="message" placeholder="Type Review" data-id="{{$info->id }}" value="{{ $info->hw_review ?? '' }}">-->
                    							    
                    							    @if(Session::get('role_id') == 3)
                    							    <textarea class="form-control" placeholder="Your Review By Teacher" readonly>{{ $info->hw_review ?? '' }}</textarea>
                    							    @else
                    							    <textarea class="form-control submit_{{$key1}}" id="message_{{ $key }}" name="message" placeholder="Type Review" data-id="{{$info->id }}" value="">{{ $info->hw_review ?? '' }}</textarea>
                    							    @endif
                    							    </div>                                                
                                                @endforeach
                                            @endif
                                        </td>
                                        @if(Session::get('role_id') !== 3)
                                        <td class="text-right pl-3"> <button type="submit" class="btn btn-primary btn-xs submitReview " data-submit="{{$key1}}">Submit</button></td>
                                        @endif
                                   </tr>
                                
            					@endforeach 
            					@endif 
        				    </tbody>
                        </table>
                    </div>        				
  