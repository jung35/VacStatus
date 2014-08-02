@extends('layout')
@section('content')

  <div class="vacstatus-profile">
    <div class="medium-2 small-12 columns avatar">
      <img class="online" src="{{ url('') }}/img/mystery-man.jpg">
    </div>
    <div class="medium-10 small-12 columns basic small-only-text-center">
      <h3>Loading Profile...</h3>
      <div class="row">
        <div class="medium-2 columns">
          <span class="big-steam"><a><i class="fa fa-steam"></i></a></span>
        </div>
      </div>
    </div>

  </div>
@stop

@section('javascript')
  <div id="loader">Loading Profile... <i class="fa fa-refresh fa-spin"></i></div>
  <script>
    $.ajax({
      url: '{{ url('') }}/u/update/single/{{{ $steam3Id }}}',
      type: "POST",
      data: {
        '_token': '{{{ csrf_token() }}}'
      }
    }).done(function(data) {
      $('#loader').fadeOut();
      $('.content-start').html(data);
    });
  </script>
  <script type="text/javascript" src="{{ asset('js/profile.js') }}"></script>
@stop
