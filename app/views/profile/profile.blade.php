@extends('layout')
@section('content')
  @include('profile/profileSkeleton', array('profile' => $profile, 'old_check' => $old_check))
@stop


@section('javascript')
  @if(isset($update) && $update)
  <div id="loader">Updating Profile... <i class="fa fa-refresh fa-spin"></i></div>
  <script>
    $.ajax({
      url: '{{ url('') }}/u/update/single/{{{ $steam3Id }}}',
      type: "POST",
      data: {
        '_token': _token
      }
    }).done(function(data) {
      $('#loader').fadeOut();
      $('.content-start').html(data);
    }).error(function() {
      $('#loader').fadeOut(function() {
        $('.error-notification').html("Sorry, there is an error with Steam API.\nPlease try refreshing again in few minutes.").fadeIn('slow');
      });
    });
  </script>
  @endif
  <script type="text/javascript" src="{{ asset('js/profile.js') }}"></script>
@stop
