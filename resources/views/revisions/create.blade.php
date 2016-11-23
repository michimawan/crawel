@extends('layouts.app')

@section('content')
<div class="container">
<div class="row">
  <ul class="navbar-right btn btn-group">
    <a href="{{ route('stories.edit') }}" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
    </a>
    <a href="{{ route('mails.create') }}" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Create Daily Mail
    </a>
  </ul>
  {!! Form::open([
    'route' => 'revisions.store',
    'method' => 'POST',
    'class' => '']) !!}
  <!-- Nav tabs -->
  @include('elements.navtabs', [
    'projects' => $projects,
  ])
  <!-- Tab panes -->
  <div class="tab-content">
  @php
  $active = ' active';
  @endphp
  @foreach($projects as $projectName => $projectIds)
    <div role="tabpanel" class="tab-pane{!! $active !!}" id="{{ $projectName }}">
        <div class="row">
        <div class="col-xs-6">
        @include('revisions._properties', [
            'projectName' => $projectName,
        ])
        </div><!-- /col-xs-6 -->
        <div class="col-xs-6">
        @include('revisions._data', [
            'projectName' => $projectName,
            'tag' => is_null($greenTags->get($projectName)) ? collect() : $greenTags->get($projectName)->values(),
            'projectIds' => $projectIds
        ])
        </div><!-- /col-xs-6 -->
        </div><!-- /row -->
    </div><!-- /tab-panel -->
  @php $active = ''; @endphp
  @endforeach
  </div>
  {!! Form::submit('Submit', ['class' => 'btn btn-success btn-sm']) !!}
  {!! Form::close() !!}
</div>
</div>

</div>
@endsection
