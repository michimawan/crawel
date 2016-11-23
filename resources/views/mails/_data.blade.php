@php $projectName = strtolower($projectName) @endphp
<div class="container">
<h3>Choose Daily Green Tag</h3>
  @foreach($revisions as $idx => $tagRev)
    <div class="input-group">
    @php $name = "{$projectName}_revisions[{$idx}]"; @endphp
    {!! Form::checkbox($name, $tagRev->id ) !!}
    {{ $tagRev->child_tag_revisions }}
    </div>
  @endforeach
  @if ($revisions->count() == 0)
  <h4>Sorry Child Tag Revision Today</h4>
  @endif
</div>
