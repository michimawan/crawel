@extends('layouts.app')

@section('content')
{!! Form::open([
  'route' => 'revisions.store',
  'method' => 'POST',
  'class' => '']) !!}
<div class="container">
  <div class="form-group">
    <div class="col-sm-10">
      {!! Form::label('workspace', 'Choose Input Project', ['class' => '']) !!}
      {!! Form::select('workspace', $options, ['class' => 'form-control']) !!}
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-10">
      {!! Form::label('child_tag_rev', 'Tag Rev', ['class' => '']) !!}
      {!! Form::text('child_tag_rev', '', ['class' => '', 'required']) !!}
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-10">
      {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>
  </div>
{!! Form::close() !!}
@endsection
