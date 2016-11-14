@extends('layouts.app')

@section('content')
{{ Html::style('css/jquery-ui.min.css') }}

<div class="container">
{!! Form::open([
  'route' => 'stories.index',
  'method' => 'GET',
  'class' => '']) !!}
<!-- {!! Form::date('date', \Carbon\Carbon::now(), ['class' => 'btn-small', 'id' => 'datepicker']) !!}
 -->{!! Form::text('date', '', ['class' => 'btn-small', 'id' => 'datepicker']) !!}

{!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
{!! Form::close() !!}
<div>
<ul class="nav navbar-right">
    <a href="{{ url('/greentag') }}">
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
@endsection
