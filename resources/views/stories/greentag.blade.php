@extends('layouts.app')
@section('content')
{{ Html::style('css/jquery-ui.min.css') }}
<div class="container">
<a href="{{ url('/greentag') }}">
<button type="button" class="btn btn-danger btn-sm" >Sent to draft email</button>
</a>
<div class="row">
  <div class="col-md-4 col-sm-8">
    <div class="tabs-left">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#a" data-toggle="tab">
            <span>
              <h4>Project1</h4>
            </span>
          </a>
        </li>
        <li>
          <a href="#b" data-toggle="tab">
            <span>
              <h4>Project2</h4>
            </span>
          </a>
        </li>
        <li>
          <a href="#c" data-toggle="tab">
            <span>
              <h4>Project3</h4>
            </span>
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="a">
          <h3>Choose daily greentag</h3>
          {!! Form::open([
          'route' => 'stories.index',
          'method' => 'GET',
          'class' => '']) !!}
          {!! Form::text('date', '', ['class' => 'btn-small', 'id' => 'datepicker']) !!}
          {!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
          {!! Form::close() !!}
          <ul class="list-group pull-left">
            <li class="list-group-item">
              <h4>Greentag 1 &nbsp; &nbsp;
                <span>
                <input type="checkbox">
                </span>
              </h4>
            </li>
            <li class="list-group-item">
              <h4>Greentag 2 &nbsp; &nbsp;
                <span>
                <input type="checkbox">
                </span>
              </h4>
            </li>
          </ul>
        </div>
        <div class="tab-pane" id="b">
          <h3>Choose daily greentag b</h3>
          {!! Form::open([
          'route' => 'stories.index',
          'method' => 'GET',
          'class' => '']) !!}
          {!! Form::text('date', '', ['class' => 'btn-small', 'id' => 'datepicker']) !!}
          {!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
          {!! Form::close() !!}
          <ul class="list-group pull-left">
            <li class="list-group-item">
              <h4>Greentag 1 &nbsp; &nbsp;
                <span>
                <input type="checkbox">
                </span>
              </h4>
            </li>
            <li class="list-group-item">
              <h4>Greentag 2 &nbsp; &nbsp;
                <span>
                <input type="checkbox">
                </span>
              </h4>
            </li>
          </ul>
        </div>
        <div class="tab-pane" id="c">
          <h3>Choose daily greentag c</h3>
          {!! Form::open([
          'route' => 'stories.index',
          'method' => 'GET',
          'class' => '']) !!}
          {!! Form::text('date', '', ['class' => 'btn-small', 'id' => 'datepicker']) !!}
          {!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
          {!! Form::close() !!}
          <ul class="list-group pull-left">
            <li class="list-group-item">
              <h4>Greentag 1 &nbsp; &nbsp;
                <span>
                <input type="checkbox">
                </span>
              </h4>
            </li>
            <li class="list-group-item">
              <h4>Greentag 2 &nbsp; &nbsp;
                <span>
                <input type="checkbox">
                </span>
              </h4>
            </li>
          </ul>
        </div>
      </div>
      <!-- /tab-content -->
    </div>
    <!-- /tabbable -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->
@endsection