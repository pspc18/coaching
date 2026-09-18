@extends('layout.app') 
@section('content')

<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-support"></i> &nbsp; {{ __('master.View Prayer') }}</h3>
							<div class="card-tools ml-3"> <a href="https://demo3.rusoft.in/master_dashboard" class="btn btn-primary  btn-sm" title="Back "><i class="fa fa-arrow-left"></i>Back   </a> </div>
							<div class="card-tools"> <a href="{{url('prayer_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a> </div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="card-body">
									<table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
										<thead class="bg-primary">
											<tr>
												<th>{{ __('common.SR.NO') }}</td>
                                                <th>{{ __('common.Name') }}</th>
												<th>{{ __('master.Prayer') }}</th>
												<th>{{ __('common.Action') }}</th>

											</tr>
										</thead>
										<tbody> 
										@if(!empty($data)) 
										    @php 
										        $i=1 
										    @endphp 
										    @foreach ($data as $item)
											<tr>
												<td>{{ $i++ }}</td>
												<td>{{ $item['name'] ?? ''}}</td>
												<td>
												<div id="log">{{ $item['prayer'] ?? ''}}</div>
													<div id="divMain"></div>
												</td>
											<td class=d-flex>
												    <a href="{{url('prayer_edit') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(9)->edit ? '' : 'd-none' }}" title1="Edit Sports"><i class="fa fa-edit"></i></a> 
												    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(9)->delete ? '' : 'd-none' }}" title1="Delete Sports"><i class="fa fa-trash-o"></i></a> 
												</td>
											</tr> 
											@endforeach 
										@endif 
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div> 





<script>
    $('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});
</script>

<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{__('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('prayer_delete') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{__('common.Are you sure you want to delete') }}  ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{__('common.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{__('common.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
    var support = (function() {
        if (!window.DOMParser) return false;
        var parser = new DOMParser();
        try {
            parser.parseFromString('x', 'text/html');
        } catch (err) {
            return false;
        }
        return true;
    })();

    var textToHTML = function(str) {

        // check for DOMParser support
        if (support) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(str, 'text/html');
            return doc.body.innerHTML;
        }

        // Otherwise, create div and append HTML
        var dom = document.createElement('div');
        dom.innerHTML = str;
        return dom;

    };

    var myValue9 = document.getElementById("log").innerText;

    document.getElementById("divMain").innerHTML = textToHTML(myValue9);

    document.getElementById("log").innerText = "";
</script>
@endsection