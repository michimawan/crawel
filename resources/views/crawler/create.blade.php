{!! Form::open([
	'route' => 'crawler.store',
	'method' => 'POST',
	'class' => '']) !!}
<div class="form-group">
  {!! Form::label('project', 'Project', ['class' => '']) !!}
  <div class="col-sm-10">
    {!! Form::select('project', $options, ['class' => 'form-control']) !!}
  </div>
</div>
<div class="form-group">
  {!! Form::label('stories', 'Stories', ['class' => '']) !!}
  <div class="col-sm-10">
    {!! Form::textarea('stories', '', ['class' => '']) !!}
  </div>
</div>
<div class="form-group">
  <div class="col-sm-10 col-sm-offset-2">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
  </div>
</div>
{!! Form::close() !!}