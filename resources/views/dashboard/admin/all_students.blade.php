
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-search"></i> &nbsp; Search Results</h3>
							<div class="card-tools"> 
							<!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add User</a>-->
							</div>
						</div>
						<div class="card-body">
                            @if(!empty($data['Student']))
                            @if(count($data['Student']) > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary text-center">
                                        <th colspan="12">Students List</th>
                                    </tr>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>St. Name </th>
                                        <th>Class</th>
                                        <th>F. Name</th>
                                        <th>M. Name</th>
                                        <th>Mob. No.</th>                                  
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                       $i=1;
                                    @endphp
                                    @foreach ($data['Student'] as $item)
                                    <tr style="cursor:pointer; ">
                                        <td class="p-1" >{{ $i++ }}</td>
                                        <td class="p-1" >{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }} </td>
                                        <td class="p-1" >{{ $item['ClassTypes']['name'] ?? '' }} </td>
                                        <td class="p-1" >{{ $item['father_name']  }}</td>
                                        <td class="p-1" >{{ $item['mother_name']  }}</td>
                                        <td class="p-1" >{{ $item['mobile']  }}</td>
                                        <td>
                                            <a href="{{url('studentDetail')}}/{{ $item->id ?? '' }}" class="btn btn-success  btn-xs" title="View Student"><i class="fa fa-bars"></i></a>
                                            <a href="{{url('admissionEdit',$item->id)}}" class="btn btn-primary  btn-xs ml-3" title="Edit Student"><i class="fa fa-edit"></i></a>
                                        </td>                                             
                                    </tr>
                                   @endforeach
                                </tbody>
                            </table>
                            @endif
                            @endif

                            @if(!empty($data['Teacher']))
                            @if(count($data['Teacher']) > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary text-center">
                                        <th colspan="12">Teachers List</th>
                                    </tr>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Teacher Name </th>
                                        <th>F. Name</th>
                                        <th>Mob. No.</th>
                                        <th>Email</th>                                  
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                       $i=1;
                                    @endphp
                                    @foreach ($data['Teacher'] as $teacher)
                                    <tr style="cursor:pointer; ">
                                        <td class="p-1" >{{ $i++ }}</td>
                                        <td class="p-1" >{{ $teacher['first_name'] ?? '' }} {{ $teacher['last_name'] ?? '' }} </td>
                                        <td class="p-1" >{{ $teacher['father_name']  }}</td>
                                        <td class="p-1" >{{ $teacher['mobile']  }}</td>
                                        <td class="p-1" >{{ $teacher['email']  }}</td>
                                        <td>
                                            <a href="{{url('teachers/edit',$teacher->id)}}" class="btn btn-primary  btn-xs" title="Edit Teacher"><i class="fa fa-edit"></i></a>
                                        </td>                                             
                                    </tr>
                                   @endforeach
                                </tbody>
                            </table>
                            @endif
                            @endif

                            @if(!empty($data['User']))
                            @if(count($data['User']) > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary text-center">
                                        <th colspan="12">Users List</th>
                                    </tr>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Users Name </th>
                                        <th>F. Name</th>
                                        <th>Mob. No.</th>
                                        <th>Email</th>                                  
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                       $i=1;
                                    @endphp
                                    @foreach ($data['User'] as $user)
                                    <tr style="cursor:pointer; ">
                                        <td class="p-1" >{{ $i++ }}</td>
                                        <td class="p-1" >{{ $user['first_name'] ?? '' }} {{ $user['last_name'] ?? '' }} </td>
                                        <td class="p-1" >{{ $user['father_name']  }}</td>
                                        <td class="p-1" >{{ $user['mobile']  }}</td>
                                        <td class="p-1" >{{ $user['email']  }}</td>
                                        <td>
                                            <a href="{{url('editUser',$user->id)}}" class="btn btn-primary  btn-xs" title="Edit Teacher"><i class="fa fa-edit"></i></a>
                                        </td>                                             
                                    </tr>
                                   @endforeach
                                </tbody>
                            </table>
                            @endif
                            @endif
                            
                            @if(!empty($data['SidebarSub']))
                            @if(count($data['SidebarSub']) > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary text-center">
                                        <th colspan="12">Sidebar & Modules</th>
                                    </tr>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Sidebar</th>
                                        <th>Module</th>                                 
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                       $i=1;
                                    @endphp
                                    @foreach ($data['SidebarSub'] as $sidebar)
                                    <tr style="cursor:pointer; ">
                                        <td class="p-1" >{{ $i++ }}</td>
                                        <td class="p-1" >{{ $sidebar['sidebar_name'] ?? '' }}</td>
                                        <td class="p-1" >{{ $sidebar['name'] ?? '' }} </td>
                                        <td>
                                            <a href="{{ $sidebar->url ?? '' }}" class="btn btn-success btn-xs" title="Follow Link"><i class="fa fa-external-link"></i></a>
                                        </td>                                             
                                    </tr>
                                   @endforeach
                                </tbody>
                            </table>
                            @endif
                            @endif                            
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
