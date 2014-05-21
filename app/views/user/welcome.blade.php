@extends('base')
@include('user.search')

@section('head')
  {{ HTML::style('css/user/user.css') }}
  <script>
    @if(isset($displayAdded))
    var dated = true;
    @endif
    var userLoad = [];
  </script>
@stop

@section('title')
@if(isset($searching))
&mdash; Search
@endif
@stop

@section('content')
@section('search')
@show
<div class="col-md-8 col-md-offset-2">
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th></th>
          <th><span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Alias of user added">User</span></th>
          @if (!isset($searching))
          <th>
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Date added to list (mm/dd/yy)">Date</span>
          </th>
          @endif
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Valve Anti-Cheat / Overwatch Status (mm/dd/yy)">VAC / Overwatch</span>
          </th>
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="# of others who also has this person on list">Others</span>
            </th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @if ((isset($vBanCount) && $vBanCount > 0) || (method_exists($vBanList, 'count') && $vBanList->count() > 0))
        @foreach ($vBanList as $vBanUser)
        <tr>
          @if(!is_object($vBanUser))
            <td colspan='7' id="user-{{{ bcsub($vBanUser, '76561197960265728') }}}" style="height: 49px" class="text-muted text-center"><script>userLoad.push({{{ bcsub($vBanUser, '76561197960265728') }}});</script><span class="icon-spin glyphicon glyphicon-refresh"></span> This user is currently loading</td>
          @else
            @include('user.userSlide')
          @endif
        </tr>
        @endforeach
      @else
        <tr>
          <td colspan='7' class="text-muted text-center">No one is on your list :(</td>
        </tr>
      @endif
      </tbody>
    </table>
  </div>
  @if (!isset($searching))
    {{ $vBanList->links() }}
  @endif
</div>
@stop

@section('script')
  {{ HTML::script('js/user/userLoad.js') }}
@stop
