@if(Session::has('message') || Session::has('error') || Session::has('info') || Session::has('warning'))
<script>
document.addEventListener('DOMContentLoaded',function(){
    @if(Session::has('message'))
        StudentModal.open({type:'success',title:'Success',message:@json(session('message')),timer:2000,showConfirm:false});
    @elseif(Session::has('error'))
        StudentModal.open({type:'error',title:'Unable to continue',message:@json(session('error'))});
    @elseif(Session::has('warning'))
        StudentModal.open({type:'warning',title:'Please check',message:@json(session('warning'))});
    @elseif(Session::has('info'))
        StudentModal.open({type:'info',title:'Information',message:@json(session('info'))});
    @endif
});
</script>
@endif
