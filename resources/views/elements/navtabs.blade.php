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

