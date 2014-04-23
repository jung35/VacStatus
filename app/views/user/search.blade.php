@section('search')
{{ Form::open(array('route' => 'search', 'class' => 'form-horizontal col-md-6 col-md-offset-3')) }}
  <div class="form-group">
  @if (Session::get('error'))
    <p class="bg-danger search-error"><span class="text-danger">Error : </span>{{ Session::get('error') }}</p>
  @elseif (Session::get('success'))
    <p class="bg-success search-error"><span class="text-success">Success : </span>{{ Session::get('success') }}</p>
  @endif
    {{ Form::label('doSearch', 'Search', array('class' => 'col-md-2 control-label')) }}
    <div class="col-md-10">
      <div class="input-group search-regular">
        {{ Form::text('doSearch', null, array('class' => 'form-control', 'placeholder' => 'SteamID or Steam Community ID or URL')) }}
        <div class="input-group-btn">
          <a onClick="searchMany();" class="btn btn-default" type="button">Multi-Search</a>
        </div>
      </div>
      <div class="search-multi hidden">
        {{ Form::textArea('doManySearch', null, array('class' => 'form-control', 'value' => "", 'placeholder' => 'SteamID or Steam Community ID or URL (new line)')) }}
        <input type="submit" value="Search" class="btn btn-default col-md-12">
      </div>
    </div>
  </div>
{{ Form::close() }}
@stop
