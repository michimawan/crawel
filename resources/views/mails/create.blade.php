@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
{!! Form::open([
  'route' => 'revisions.index',
  'method' => 'GET',
  'class' => '']) !!}
{!! Form::text('date', '', ['class' => 'btn-small', 'id' => 'datepicker']) !!}

{!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
{!! Form::close() !!}
</div>
<div class="row">
  <!-- Nav tabs -->
  @include('elements.navtabs', [
    'projects' => $projects,
  ])
  {!! Form::open([
    'route' => 'mails.send',
    'method' => 'POST',
    'class' => '']) !!}
  <div class="tab-content">
  @php
  $active = ' active';
  @endphp
  @foreach($projects as $projectName => $projectIds)
    <div role="tabpanel" class="tab-pane{!! $active !!}" id="{{ $projectName }}">
        @include('mails._data', [
            'projectName' => $projectName,
            'revisions' => is_null($revisions->get($projectName)) ? collect() : $revisions->get($projectName)->values(),
            'projectIds' => $projectIds
        ])
    </div><!-- /tab-panel -->
  @php $active = ''; @endphp
  @endforeach
  </div><!-- /tab-content -->
  {!! Form::submit('Submit', ['class' => 'btn btn-success btn-sm']) !!}
  {!! Form::close() !!}
</div><!-- /row -->
</div> <!-- container -->
@endsection
