@extends('layout')
@section('content')
    @include('profile/profileSkeleton', array('profile' => $profile, 'old_check' => $old_check))
@stop


@section('javascript')
    @if(isset($update) && $update)
    <script>
        $.ajax({
            url: '{{{ URL::route('profile.update.single') }}}',
            type: "POST",
            data: {
                'steam3Id': '{{{ $steam3Id }}}',
                '_token': _token
            },
            beforeSend: fadeInLoader('Updating Profile')
        }).done(function(data) {
            if(data.type == 'error') {
                fadeOutLoader(function() {
                    fadInOutAlert("<strong>Error</strong> "+data.message, 2);
                });
            } else {
                fadeOutLoader();
                $('.content-start > .row > .column').html(data);
            }
        }).error(function() {
            fadeOutLoader(function() {
                fadInOutAlert("<strong>Error</strong> Steam API error. Please try refreshing again in few minutes.", 2);
            });
        });
    </script>
    @endif
    <script type="text/javascript" src="{{ asset('js/profile.js') }}"></script>
@stop
