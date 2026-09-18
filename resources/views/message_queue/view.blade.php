
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary d-flex justify-content-between align-items-center flex-wrap">
							<h3 class="card-title mb-0"><i class="fa fa-credit-card"></i> &nbsp;{{ __('View Message Queue') }}</h3>	
						</div>

						<div class="card-body">
                            <form id="quickForm" action="{{ url('message_queue') }}" method="post" class="mt-3">
                                @csrf 
                                <div class="row g-2">
                                    <div class="col-md-2 col-sm-6 top">
                                        <div class="form-group">
                                            <label>{{ __('expense.From Date') }}</label>
                                            <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $search['from_date'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6 top">
                                        <div class="form-group">
                                            <label>{{ __('expense.To Date') }}</label>
                                            <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $search['to_date'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6 top">
                                        <div class="form-group">
                                            <label>{{ __('Message Status') }}</label>
                                            <select class="form-control" name="message_status" id="message_status">
                                                <option value="">All</option>
                                                <option value="0" {{ isset($search['message_status']) && $search['message_status']=='0' ? 'selected' : '' }}>In Queue</option>
                                                <option value="1" {{ isset($search['message_status']) && $search['message_status']=='1' ? 'selected' : '' }}>Sent</option>
                                                <option value="2" {{ isset($search['message_status']) && $search['message_status']=='2' ? 'selected' : '' }}>Failed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!--<div class="col-md-1 col-sm-6 d-flex align-items-end">-->
                                    <!--    <button type="submit" class="btn btn-primary w-100">{{ __('common.Search') }}</button>-->
                                    <!--</div>-->
                                    <div class="col-md-1 top">
                                         <label class="text-white">Search</label>
                                         <button type="submit" class="btn btn-primary" >{{ __('common.Search') }}</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive mt-3">
                                <table id="example1" class="table table-bordered table-striped table-hover align-middle" style="font-size: 12px;">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>{{ __('Sr No.') }}</th>
                                            <th>{{ __('Message ID') }}</th>
                                            <th>{{ __('Receiver number') }}</th>
                                            <th>{{ __('Message Type') }}</th>
                                            <th>{{ __('Content') }}</th>
                                            <th>{{ __('Media Link') }}</th>
                                            <th>{{ __('Message status') }}</th>
                                            <th>{{ __('Submitted at') }}</th>
                                            <th>{{ __('Sent at') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if(!empty($data))
                                            @php $i = 1; @endphp
                                            @foreach ($data as $item)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $item['message_id'] ?? '' }}</td>
                                                    <td>{{ $item['receiver_number'] ?? '' }}</td>
                                                    <td>{{ $item['message_type'] ?? '' }}</td>
                                                    <td title="{{ $item['content'] ?? '' }}">{{ substr($item['content'], 0, 10) ?? '' }}..</td>
                                                    <td>{{ $item['media_link'] ?? '' }}</td>
                                                    <td>
                                                        @if($item['message_status'] == 0)
                                                            <span class="badge bg-warning text-dark">In Queue</span>
                                                        @elseif($item['message_status'] == 1)
                                                            <span class="badge bg-success">Sent</span>
                                                        @elseif($item['message_status'] == 2)
                                                            <span class="badge bg-danger">Failed</span>
                                                        @else
                                                            <span class="badge bg-secondary">Unknown</span>
                                                        @endif
                                                    </td>

                                                    <td>{{ !empty($item['submitted_at']) ? date("d-m-Y h:i:s A", strtotime($item['submitted_at'])) : '' }}</td>
                                                    <td>{{ !empty($item['sent_at']) ? date("d-m-Y h:i:s A", strtotime($item['sent_at'])) : '' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">No records found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
						</div>                        
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<style>
    .top {
        margin-top: -12px;
    }

    @media (max-width: 767px) {
        .top {
            margin-top: 0;
        }

        .card-header h3 {
            font-size: 16px;
        }

        form .form-group label {
            font-size: 12px;
        }

        #quickForm .btn {
            width: 100%;
            margin-top: 4px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table thead th {
            white-space: nowrap;
        }
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 10px;
    }
    
    table td,th {
            white-space: nowrap;
            font-size: 15px;
        }
</style>
@endsection
