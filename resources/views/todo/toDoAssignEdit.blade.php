@php
$user = Helper::getUsers();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('To Do List Edit') }}</h3>
        <div class="card-tools">
                <a href="{{url('to_do_assign_view')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-eye"></i> {{ __('common.View') }} </a>
                <a href="{{url('/')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
        </div>
        
        </div>  
     
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
     <form action='{{url("to_do_assign_edit")}}/{{ $data->id }}' method='post'>
         @csrf
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label for="taskTitle">Task Title</label>
                  <input type="text" class="form-control" id="taskTitle" name='title' placeholder="Enter task title" value="{{ $data->name ?? '' }}" required>
                </div>
                  <div class="form-group col-md-3">
                  <label for="taskStatus">Status</label>
                  <select class="form-control" id="taskStatus" name='status' required>
                    <option value="0" {{ 0 == $data->status ? 'selected' : '' }}>Pending</option>
                    <option value="1" {{ 1 == $data->status ? 'selected' : '' }}>Working</option>
                    <option value="2" {{ 2 == $data->status ? 'selected' : '' }}>Completed</option>
                    <option value="3" {{ 3 == $data->status ? 'selected' : '' }}>Verified by Admin</option>
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label for="taskDeadline">Deadline</label>
                  <input type="date" class="form-control" name='deadline' id="taskDeadline" value="{{ $data->deadline ?? '' }}">
                </div>
                <div class="form-group col-md-3">
                  <label for="assignedTo">Assign To</label>
                 	<select class="form-control select2" name="user_id" id="user_id">
						<option value="">{{ __('common.Select') }}</option>
						@if(!empty($user))
						@foreach($user as $item)
						<option value="{{ $item->id ?? ''  }}" {{ $item->id == $data->assign_to ? 'selected' : '' }}>
						    {{ $item->first_name ?? ''  }} {{ $item->last_name ?? ''  }} [{{ $item->role_name ?? ''  }}]
						</option>
						@endforeach
						@endif
					</select>
                </div>
              </div> 
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label for="priorityLevel">Priority Level</label>
                  <select class="form-control" id="priorityLevel"  name='priority' required>
                    <option value="low" {{ "low" == $data->priority ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ "medium" == $data->priority ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ "high" == $data->priority ? 'selected' : '' }}>High</option>
                  </select>
                </div>
                <div class="form-group col-md-9">
                  <label for="taskDescription">Task Description</label>
                  <textarea class="form-control" id="taskDescription" rows="3" name='description' value="{{ $task->description ?? '' }}" placeholder="Enter task description" required>{{ $data->description ?? '' }}</textarea>
                </div>
              </div>
                 <div class="form-group col-md-12">
                  <button class='btn btn-success' >Submit</button>
                </div>
              
            </form>
        </div>
        </div>
    </div>
    </div>
  </div>
</div>
</section>
</div>
        
       

@endsection 