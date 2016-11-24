@extends('layouts.app')

@section('content')
{{ Html::style('css/jquery-ui.min.css') }}

<div class="container">
<div class="row">
{!! Form::open([
  'route' => 'stories.index',
  'method' => 'GET',
  'class' => '']) !!}
  {!! Form::text('date', '', ['class' => 'btn-small', 'id' => 'datepicker']) !!}
  {!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
{!! Form::close() !!}
</div>
<div class="row">
  <ul class="navbar-right btn btn-group">
    <a id="create-child-tag-rev" href="{{ route('revisions.create') }}" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Create Child Tag Revisions
    </a>
    <a id="create-daily-mail" href="{{ route('mails.create') }}" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Create Daily Mail
    </a>
  </ul>
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
        @include('stories._data', [
            'projectName' => $projectName,
            'tag' => is_null($tag->get($projectName)) ? collect() : $tag->get($projectName)->values(),
            'projectIds' => $projectIds
        ])
        </div>
        @php
        $active = '';
        @endphp
    @endforeach
  </div>
</div>
</div>

</div>
@endsection
