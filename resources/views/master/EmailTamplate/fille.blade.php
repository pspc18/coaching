@extends('layout.app') 
@section('content')


 

 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Email Tamplate </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
              
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
<div class="card">
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>Sr. No.</th>
                    <th>Titel</th>
                    <th>subject</th>
                    
                    <th>Edit</th>
          </thead>
          <tbody>
              
              @if(!empty($data))
                @php
                   $i=1
                  
                @endphp
                @foreach ($data  as $item)
               
                <tr>
                        <td>{{ $i++ }}</td>
                        
                        <td>{{ $item['name']  }}</td>
                        <td>{{ $item['subject']  }}</td>
                        
                        <td>
                                   <a href="{{url('EmailTamplateEdit',$item->id)}}"><li class="dropdown-item text-primary">Edit</li></a>
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
        
   

@endsection 