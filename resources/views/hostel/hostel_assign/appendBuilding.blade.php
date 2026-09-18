
@if(!empty($building)) 
    @foreach($building as $build)
        <div class="col-2 col-md-1 btn btn-secondary btn-xs m-1 buildings" style="width: 88px; height: 88px; border-radius: 60px;" data-id="{{ $build->id ?? '' }}">
                    <div class="mt-1"><i style="font-size: 40px;margin-top: 2px;" class="fa fa-building-o"></i><p>{{ $build->name ?? '' }}</p></div>
                </div>
    @endforeach
@endif