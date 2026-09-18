@extends('layout.app') 
@section('content')
<style>
    .top{
        margin-top: -12px;
    }
</style>
<div class="content-wrapper">

	<section class="content pt-3"> 
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-credit-card"></i> &nbsp;{{ __('Modules for print file') }}</h3>
							
							
							<div class="card-tools"> 
							    <!--<a href="{{url('printFileAdd')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }}</a> -->
							    <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back </a>
							</div>
							
						</div>
						<div class="card-body">
       <!--                 <form id="quickForm" action="{{ url('printFileAdd') }}" method="post" >-->
       <!--                 @csrf -->
       <!--             <div class="row m-2">-->
       <!--            <div class="col-md-2 mb-2 top">-->
       <!--             		<div class="form-group">-->
       <!--             			<label>{{ __('expense.From Date') }}</label>-->
       <!--     				<input type="date" class="form-control" id="from_date" name="from_date"  value="{{ $_POST['from_date'] ?? '' }}" >-->
       <!--                     </div>-->
       <!--             	</div>-->
       <!--             	<div class="col-md-2 top">-->
       <!--                     <div class="form-group ">-->
       <!--                         <label>{{ __('expense.To Date') }}</label>-->
       <!--     				<input type="date" class="form-control" id="to_date" name="to_date"  value="{{ $_POST['to_date'] ?? '' }}">-->
       <!--         			</div> -->
       <!--                 </div>-->
       <!--     		<div class="col-md-3 top">-->
       <!--     			<div class="form-group">-->
       <!--     				<label>{{ __('common.Search By Keywords') }}</label>-->
       <!--     				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Search By Keywords') }}" value="{{ $search['name'] ?? '' }}">-->
       <!--     		    </div>-->
       <!--     		</div>                     	-->
       <!--                 <div class="col-md-1 top">-->
       <!--                      <label class="text-white">Search</label>-->
       <!--             	    <button type="submit" class="btn btn-primary" >{{ __('common.Search') }}</button>-->
       <!--             	</div>-->
                    			
       <!--             </div>-->
       <!--</form>-->
       
       
                            <div class="col-md-12">
                                <span><button id='edit_button' class='btn btn-primary'>Edit Module's Name</button></span>
                                </div>
                            <div class="col-md-12" id="">
<form action='' method='post'>
    @csrf
                        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                            <thead class="bg-primary">
                                <tr role="row">
                                    <th>{{ __('common.SR.NO') }}</th>
                                    <th>{{ __('Name of Module') }}</th>
                                    <th>{{ __('View') }}</th>

                                </tr>
                            </thead>
                            <tbody>

                               @if(!empty($data))
                               
                               @foreach($data as $key => $item)
                             <tr>
                                    <td>{{$key +1}}</td>
                                    <td class='module_text'> {{ $item->name_show ?? '' }}
                                    </td>
                                    <td class='module_input'>
                                    
                                    <input type='text' name='module[]' value='{{ $item->name_show ?? '' }}' />
                                    <input type='hidden' name='id[]' value='{{ $item->id ?? '' }}' />
                                    
                                    </td>
                                    <td><a href="{{url('printFileModuleWiseView')}}/{{$item->id ?? ''}}" class="btn btn-primary btn-xs tooltip1 {{ Helper::permissioncheck(9)->view ? '' : 'd-none' }}" title1="Start Exam"> &nbsp;<i class="fa fa-eye"></i> </a>
    							 </td>
                                    
                                   
                                </tr>
                                
                                @endforeach
                               @endif
                             
                           
                           
                            </tbody>
                        </table>
         <div class="col-md-12 text-center">
                                <span><button id='module_save' type='submit' class='btn btn-success'>Save</button></span>
                                </div>
                                
                                </form>
                            </div>
                        </div>                        
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>


<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('expenseDelete') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
</div> 

<script>

$('.module_input').hide();
$('#module_save').hide();
$('#edit_button').click(function() {
$('.module_input').toggle();
$('.module_text').toggle();
$('#module_save').toggle();
$(this).text(function(i, text){
            return text === "Edit Module's Name" ? "Edit Cancel" : "Edit Module's Name";
        });
});
$('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});

</script>


@endsection      