@extends('layouts.app')

@section('content')
<div class="container">

<div>
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
        @include('crawler._data', [
            'stories' => is_null($stories->get($projectName)) ? collect() : $stories->get($projectName),
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
