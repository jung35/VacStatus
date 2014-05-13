@extends('admin.adminBase')

@section('breadcrumb')
<li class="active">News</li>
@stop

@section('adminContent')
<div class="col-md-12">
  <h2 class="text-center">News</h2>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Created Date</th>
        <th>Updated Date</th>
        <th>Action</th>
        <th></th>
      </tr>
    </thead>
    <tbody>

    @foreach($siteNewses as $siteNews)
      <tr>
        <td>{{{ $siteNews->id }}}</td>
        <td>{{{ $siteNews->title }}}</td>
        <td>{{{ date('m/d/Y', strtotime($siteNews->created_at))}}}</td>
        <td>{{{ date('m/d/Y', strtotime($siteNews->updated_at))}}}</td>
        <td>
          <button type="button" class="btn btn-warning">Edit</button>
        </td>
        <td>
          <button type="button" class="btn btn-danger">Delete</button>
        </td>
      </tr>
    @endforeach

    </tbody>
  </table>

  {{ $siteNewses->links() }}
  <h3>New News</h3>
  {{ Form::open(array('route' => 'admin.news.new', 'class' => 'form-horizontal col-md-12')) }}
    {{ Form::text('form-title', null, array('class' => 'form-control input-lg', 'placeholder' => 'Title')) }}
    <br>
    {{ Form::textArea('form-news', null, array('class' => 'form-control')) }}
    <br>
    <input type="submit" value="Create New News" class="btn btn-default btn-lg btn-block">
  {{ Form::close() }}

</div>

@stop

@section('script')
  <script type="text/javascript" src="{{{ URL::route('home') }}}/js/admin/CKEditor/ckeditor.js"></script>
  <script>CKEDITOR.replace( 'form-news');</script>
@stop
