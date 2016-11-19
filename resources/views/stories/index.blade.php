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
  <ul class="nav navbar-right">
    <a href="{{ route('mails.create') }}">
      <button type="button" class="btn btn-danger btn-sm">Create daily email</button>
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
