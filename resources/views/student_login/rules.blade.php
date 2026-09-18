
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Rules')
@section('page_title', 'RULES')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2">
       <table class="common-table w-100">
                             <thead class=bg-primary>
                                 
                                 <tr>
                                     <th>Sr.No.</th>
                                     <th>Name</th>
                                     <th>Action</th>
                                 </tr>
                             </thead>
            
                              <tbody>
                                 
                                  @if(!empty($data))
                                  
                                  @foreach($data as $key => $item)
                                 <tr>
                                      <td >{{$key+1 ?? ''}}</td>
                                        <td ><b>{{$item->name ?? '' }}</b></td>
                                      <td >
                                          <button type="button" data-data="{!! html_entity_decode($item->description ?? '') !!}"
                                          class="btn btn-primary button1" data-bs-toggle="modal" data-bs-target="#descriptionModal">
                                              <i class="fa fa-eye" aria-hidden="true"></i>

                                                        </button>
                                          
                                        </td></tr>
                                      @endforeach
                                  @endif       
                                    
                              </tbody>
                              </table>
    </div>
<!-- Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">School Rules</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
         
        </button>
      </div>
      <div class="modal-body  description_modal">
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>
</section>
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script>
    $(".button1").click(function(){
 var value = $(this).data('data');
 $(".description_modal").html('');
 $(".description_modal").html(value);

});
</script>
  
@endsection 
