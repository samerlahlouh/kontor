@if(count($errors)>0)
  @foreach($errors->all() as $error)
  <div class="alert alert-dismissible alert-danger">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>{{__('messages_lng.oh')}}</strong> {{$error}} {{__('messages_lng.submitting_again')}}
  </div>
  @endforeach
@endif

@if(session('success'))
<div class="alert alert-dismissible alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{__('messages_lng.done')}}</strong>   {{session('success')}}.
</div>
@endif


@if(session('error'))
  <div class="alert alert-dismissible alert-danger">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>{{__('messages_lng.oh')}}</strong>  {{session('error')}} {{__('messages_lng.submitting_again')}}
  </div>
@endif