@extends('layouts.app')

@section('content')
<div class="container">

{!! Form::open([
  'route' => 'stories.index',
  'method' => 'GET',
  'class' => '']) !!}
{!! Form::date('date', \Carbon\Carbon::now(), ['class' => 'btn-small']) !!}
{!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
{!! Form::close() !!}
<div>
<div role="presentation" class="divider"><div>

<div class="dropdown">
  <button class="btn btn-success dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Greentag Timing
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Greentag list</a></li>
      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Greentag list</a></li>
      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Greentag list</a></li>
      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Greentag list</a></li>
  </ul>
</div>
<div role="presentation" class="divider"><div>
<ul class="nav navbar-right">
    <a href="{{ url('/create') }}">
        <button type="button" class="btn btn-warning btn-sm">Send to google spreadsheets</button>
        <button type="button" class="btn btn-danger btn-sm">Create daily email</button>
    </a>
</ul>  
<div role="presentation" class="divider"><div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    @php
    $active = 'class="active"';
    @endphp
    @foreach($projects as $projectName => $projectIds)
      <li role="presentation"{!! $active !!}>
        <a href="#{{ $projectName }}" aria-controls="{{ $projectName }}" role="tab" data-toggle="tab">{{ $projectName }}</a>
      </li>
      @php
      $active = '';
      @endphp
    @endforeach
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    @php
    $active = ' active';
    @endphp
    @foreach($projects as $projectName => $projectIds)
        <div role="tabpanel" class="tab-pane{!! $active !!}" id="{{ $projectName }}">
        @include('stories._data', [
            'projectName' => $projectName,
            'stories' => is_null($stories->get($projectName)) ? collect() : $stories->get($projectName)->values(),
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
@endsection
