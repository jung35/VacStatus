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
  <script>
    $.ajax({
      url: '/u/update/single',
      type: "POST",
      data: {
        'steam3Id': '{{{ $steam3Id }}}',
        '_token': _token
      },
      beforeSend: fadeInLoader('Loading Profile')
    }).done(function(data) {
      fadeOutLoader();
      $('.content-start').html(data);
    }).error(function() {
      fadeOutLoader(function() {
        fadInOutAlert("<strong>Error</strong> Steam API error. Please try refreshing again in few minutes.", 2);
      });
    });
  </script>
  <script type="text/javascript" src="{{ asset('js/profile.js') }}"></script>
@stop
