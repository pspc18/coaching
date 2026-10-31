@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();;
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
							<h3 class="card-title"><i class="fa fa-check-square-o"></i> &nbsp;{{ __('examination.View Questions') }}</h3>
							<div class="card-tools"> 
							    <a href="{{url('add/question')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(8)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a> 
                                <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            </div>
						</div>
						<div class="card-body">
						    
                    <form id="quickForm" action="{{ url('view/question') }}" method="post" >
                        @csrf 
                    <div class="row">

                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>Class</label>
                    			<select class="select2 form-control" id="class_type_id" name="class_type_id">
                    			    <option value="">{{ __('common.Select') }}</option>
                                    @if(!empty($classType)) 
                                        @foreach($classType as $type)
                                            <option value="{{ $type->id }}" {{ ($type->id == ($search['class_type_id'] ?? '')) ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                    	    </div>
                    	</div>
                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('examination.Subject') }}</label>
                    			<select class="select2 form-control" id="subject_id" name="subject_id">
                    			<option value="">{{ __('common.Select') }}</option>
                                 @if(!empty($getsubject)) 
                                      @foreach($getsubject as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['subject_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                    	    </div>
                    	</div>
                    	<div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('examination.Question Type') }}</label>
                    				<select class="select2 form-control" id="question_type_id" name="question_type_id">
                    			   <option value="">{{ __('common.Select') }}</option>
                    			   <option value="1" {{ (1== $search['question_type_id']) ? 'selected' : '' }}>Objective</option>
                    			   <option value="2" {{ (2 == $search['question_type_id']) ? 'selected' : '' }}>Descriptive</option>
                                </select>
                    	    </div>
                    	</div>

            		<div class="col-md-4">
            			<div class="form-group">
            				<label>{{ __('common.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label for="" class="text-white">{{ __('common.Search') }}</label>
                    	    <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                    	</div>
                    			
                    </div>
                </form> 						    
						    
							<table id="example1" class="table table-bordered table-striped  dataTable">
								<thead>
									<tr role="row">
										<th>{{ __('examination.Q. Id') }}</th>
										<th>Class</th>
										<th>{{ __('common.Subject') }}</th>
										<th>{{ __('examination.Question Type') }} </th>
										<th>{{ __('examination.Question') }}</th>
										<th>{{ __('common.Action') }}</th>
								</thead>
								<tbody> 
								@if(!empty($data)) 
    								@php 
    								    $i=1 
    								@endphp 
								@foreach ($data as $item)
									<tr>
										<td>{{ $i++ }}</td>
										<td><span class="badge badge-info" style="font-size: 11px;">{{ $item['class_name'] ?? '—' }}</span></td>
										<td>{{ $item['subject_name'] ?? ''}}</td>
										<td>
										    @if($item['question_type_id'] == 1)
										        Objective
										    @else
										        Descriptive
										    @endif
										</td>
										<td>
										    @if(!empty($item->group_id))
										        <span class="badge badge-info mr-1" title="Direction Group: {{ $item->group_id }}"><i class="fa fa-sitemap mr-1"></i> {{ $item->sub_question_no ? 'Q'.$item->sub_question_no : 'Group' }}</span>
										    @endif
										    {!! Str::limit(strip_tags($item['name'] ?? ''), 100) !!}
										</td>
							            <td class="d-flex align-items-center">
                                                <a data-question="{{ htmlspecialchars($item->name ?? '', ENT_QUOTES) }}"  
                                                    data-subject_id="{{ $item['Subject']->name ?? '' }}" data-class_type_id="{{ $item['ClassType']->name ?? '' }}" 
                                                    data-opt1="{{ htmlspecialchars($item->ans1 ?? '', ENT_QUOTES) }}"
                                                    data-opt2="{{ htmlspecialchars($item->ans2 ?? '', ENT_QUOTES) }}" data-opt3="{{ htmlspecialchars($item->ans3 ?? '', ENT_QUOTES) }}"
                                                    data-opt4="{{ htmlspecialchars($item->ans4 ?? '', ENT_QUOTES) }}"
                                                    data-opt5="{{ htmlspecialchars($item->ans5 ?? '', ENT_QUOTES) }}"
                                                    data-correct_ans="{{ $item->correct_ans ?? '' }}"
                                                    data-group_id="{{ $item->group_id ?? '' }}"
                                                    data-direction="{{ htmlspecialchars($item->direction ?? '', ENT_QUOTES) }}"
                                                    data-direction_table="{{ htmlspecialchars($item->direction_table ?? '', ENT_QUOTES) }}"
                                                    data-image="{{ $item->image ? asset($item->image) : '' }}"
                                                    data-explanation="{{ htmlspecialchars($item->explanation ?? '', ENT_QUOTES) }}"
                                                    class="btn btn-success questionDetail btn-xs {{ Helper::permissioncheck(8)->view ? '' : 'd-none' }}" title="View Question"><i class="fa fa-eye"></i></a> 
                                                <a href="{{url('edit/question') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3 {{ Helper::permissioncheck(8)->edit ? '' : 'd-none' }}" title="Edit Question"><i class="fa fa-edit"></i></a> 
											    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 {{ Helper::permissioncheck(8)->delete ? '' : 'd-none' }}" title="Delete Question"><i class="fa fa-trash-o"></i></a> 
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
	</section>
</div>



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>

<div class="modal" id="myModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content card-outline card-orange">
        <div class="modal-header">
            <h4 class="text-center font-weight-bold" style="width:100%; color: #002C54;">{{ __('examination.Question Details') }}</h4>   
            <button type="button" id="closeModal" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
        </div>

        <div class="modal-body p-4">
            <div class="row mb-3 pb-2 border-bottom">
                <div class="col-md-6">
                    <p class="mb-1"><b>Class:</b> <span id="subject_id1" class="badge badge-info"></span></p>
                </div>
                <div class="col-md-6 text-md-right">
                    <p class="mb-1"><b>{{ __('examination.Question Type') }}:</b> <span class="badge badge-secondary">{{ __('examination.Objective') }}</span></p>
                </div>
            </div>

            {{-- Direction / Context Passage Alert (for Grouped Questions) --}}
            <div id="modal_direction_box" class="alert alert-info d-none p-3 mb-3" style="background: #f0f7ff; border-left: 4px solid #0284c7; color: #1e293b;">
                <div class="d-flex align-items-center mb-1">
                    <span class="badge badge-primary mr-2" style="background-color: #0284c7;"><i class="fa fa-sitemap mr-1"></i> Direction / Passage</span>
                    <strong id="modal_group_badge" class="text-dark small"></strong>
                </div>
                <div id="modal_direction_text" class="font-weight-bold my-2" style="font-size: 13px; line-height: 1.6;"></div>
                <div id="modal_direction_table" class="table-responsive bg-white p-2 rounded border mt-2 d-none"></div>
            </div>

            {{-- Question Diagram / Chart --}}
            <div id="modal_image_box" class="text-center my-3 d-none">
                <img id="modal_image" src="" alt="Question Diagram" class="img-fluid rounded border p-1 bg-white shadow-sm" style="max-height: 250px;">
            </div>

            {{-- Question Statement --}}
            <div class="col-md-12 p-3 mb-3 rounded" id="question1" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <p class="mb-0 font-weight-bold text-dark" style="font-size: 14px; line-height: 1.6;">
                    <span class="text-primary mr-1"><i class="fa fa-question-circle"></i></span>
                    <span id="question"></span>
                </p>
            </div>

            {{-- Options A to E --}}
            <div class="row">
                <div class="col-md-6 mb-2" id="option1">
                    <div class="p-2 border rounded opt-box" id="opt_box_0">
                        <strong class="badge badge-light border mr-1">A</strong> <span id="ans1"></span>
                    </div>
                </div>       
                <div class="col-md-6 mb-2" id="option2">
                    <div class="p-2 border rounded opt-box" id="opt_box_1">
                        <strong class="badge badge-light border mr-1">B</strong> <span id="ans2"></span>
                    </div>
                </div>      
                <div class="col-md-6 mb-2" id="option3">
                    <div class="p-2 border rounded opt-box" id="opt_box_2">
                        <strong class="badge badge-light border mr-1">C</strong> <span id="ans3"></span>
                    </div>
                </div>       
                <div class="col-md-6 mb-2" id="option4">
                    <div class="p-2 border rounded opt-box" id="opt_box_3">
                        <strong class="badge badge-light border mr-1">D</strong> <span id="ans4"></span>
                    </div>
                </div>
                <div class="col-md-6 mb-2 d-none" id="option5">
                    <div class="p-2 border rounded opt-box" id="opt_box_4">
                        <strong class="badge badge-light border mr-1">E</strong> <span id="ans5"></span>
                    </div>
                </div>
            </div>

            {{-- Explanation / Solution --}}
            <div id="modal_explanation_box" class="mt-3 p-3 rounded bg-light border d-none">
                <strong class="text-dark"><i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution / Explanation:</strong>
                <div id="modal_explanation_text" class="mt-1 small text-muted"></div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('delete/question') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('messages.Are you sure you want to delete') }} ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
</div> 

<script>
$('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});

function renderKaTeXInModal(container) {
    if (!container || !window.katex) return;
    const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);
    const nodes = [];
    let node;
    while (node = walker.nextNode()) {
        if (node.nodeValue && node.nodeValue.includes('$')) {
            nodes.push(node);
        }
    }
    nodes.forEach(textNode => {
        const text = textNode.nodeValue;
        const displayRegex = /\$\$([\s\S]+?)\$\$/g;
        const inlineRegex = /\$([^\$\n]+?)\$/g;
        if (!displayRegex.test(text) && !inlineRegex.test(text)) return;

        const span = document.createElement('span');
        let html = text.replace(/\$\$([\s\S]+?)\$\$/g, (m, f) => {
            try { return katex.renderToString(f.trim(), { displayMode: true, throwOnError: false }); } catch(e) { return m; }
        }).replace(/\$([^\$\n]+?)\$/g, (m, f) => {
            try { return katex.renderToString(f.trim(), { displayMode: false, throwOnError: false }); } catch(e) { return m; }
        });
        span.innerHTML = html;
        if (textNode.parentNode) {
            textNode.parentNode.replaceChild(span, textNode);
        }
    });
}

function formatViewHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&lt;(\/?(?:br|b|strong|i|em|u|sub|sup|table|thead|tbody|tr|th|td|p|div|span|ul|ol|li|hr|h[1-6])(?:\s+[^&>]*)?)&gt;/gi, '<$1>')
        .replace(/<br\s*\/?>\s*\r?\n?/gi, '<br>')
        .replace(/([^>])\r?\n/g, '$1<br>');
}

$('.questionDetail').click(function() {
    $('#myModal').modal('toggle');
    
    var question = $(this).data('question');
    var class_type_id = $(this).data('class_type_id');
    var subject_id1 = $(this).data('subject_id');
    var answer1 = $(this).data('opt1') || '';
    var answer2 = $(this).data('opt2') || '';
    var answer3 = $(this).data('opt3') || '';
    var answer4 = $(this).data('opt4') || '';
    var answer5 = $(this).data('opt5') || '';
    var correct_ans = String($(this).data('correct_ans') ?? '0');
    var groupId = $(this).data('group_id') || '';
    var direction = $(this).data('direction') || '';
    var directionTable = $(this).data('direction_table') || '';
    var image = $(this).data('image') || '';
    var explanation = $(this).data('explanation') || '';

    $('#question').html(formatViewHtml(question));
    $('#subject_id1').html(subject_id1 || class_type_id);
    $('#ans1').html(formatViewHtml(answer1));
    $('#ans2').html(formatViewHtml(answer2));
    $('#ans3').html(formatViewHtml(answer3));
    $('#ans4').html(formatViewHtml(answer4));

    // Option E
    if (answer5) {
        $('#ans5').html(formatViewHtml(answer5));
        $('#option5').removeClass('d-none');
    } else {
        $('#option5').addClass('d-none');
    }

    // Direction box
    if (direction || directionTable) {
        $('#modal_direction_text').html(formatViewHtml(direction));
        $('#modal_group_badge').text(groupId ? '(' + groupId + ')' : '');
        if (directionTable) {
            $('#modal_direction_table').html(directionTable).removeClass('d-none');
        } else {
            $('#modal_direction_table').addClass('d-none');
        }
        $('#modal_direction_box').removeClass('d-none');
    } else {
        $('#modal_direction_box').addClass('d-none');
    }

    // Diagram image
    if (image) {
        $('#modal_image').attr('src', image);
        $('#modal_image_box').removeClass('d-none');
    } else {
        $('#modal_image_box').addClass('d-none');
    }

    // Explanation
    if (explanation) {
        $('#modal_explanation_text').html(formatViewHtml(explanation));
        $('#modal_explanation_box').removeClass('d-none');
    } else {
        $('#modal_explanation_box').addClass('d-none');
    }

    // Highlight correct answer
    $('.opt-box').removeClass('bg-success text-white').css({
        'background-color': '#ffffff',
        'border-color': '#e2e8f0',
        'color': '#1e293b'
    });
    const correctBox = $('#opt_box_' + correct_ans);
    if (correctBox.length) {
        correctBox.css({
            'background-color': '#dcfce7',
            'border-color': '#86efac',
            'color': '#166534',
            'font-weight': 'bold'
        });
    }

    setTimeout(function() {
        renderKaTeXInModal(document.getElementById('myModal'));
    }, 100);
});

</script>
@endsection