@php $projectName = strtolower($projectName) @endphp
<div class="container">
<h3>Choose Daily Green Tag</h3>
  @foreach($tag as $idx => $greenTag)
    <div class="input-group">
    @php $name = "{$projectName}_tags[{$idx}]"; @endphp
    {!! Form::checkbox($name, $greenTag->id ) !!}
    {{ $greenTag->code . ' (' . $greenTag->timing .")" }}
    </div>
  @endforeach
  @if ($tag->count() == 0)
  <h4>Sorry No Green Tag Today</h4>
  @endif
</div>
