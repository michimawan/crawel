@extends('layouts.app')


@section('content')
<div class="container">
  <li class="list-group-item">
      <h4>  #Greentag ID &nbsp; &nbsp;
          <br>
              <span> 
                1. Story List                 
              </span>
          </br>
          <div class="row">
          <div class='col-sm-6'>
              <div class="form-group">
                  <br>
                  <div class='input-group date' id='datetimepicker3'>
                      <input type='text' class="form-control" placeholder="Child Tag Revisions"/>
                          <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
                      </span>
                  </div>
                  <br>
                    <span>End Time To Check Story</span>
                  </br>
                  <div class='input-group date' id='datetimepicker3'>
                      <input class="form-control" type="time" value="" id="example-time-input"/>
                          <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
                      </span>
                  </div>
                  <br>
                    <span>End Time To Run Automated Test</span>
                  </br>
                  <div class='input-group date' id='datetimepicker3'>
                      <input class="form-control" type="time" value="" id="example-time-input"/>
                          <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
                      </span>
                  </div>
                   <br>
                    <span>Time Get Canary</span>
                  </br>
                  <div class='input-group date' id='datetimepicker3'>
                      <input class="form-control" type="time" value="" id="example-time-input"/>
                          <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
                      </span>
                  </div>
                  <br>
                    <span>Time To Elb</span>
                  </br>
                  <div class='input-group date' id='datetimepicker3'>
                      <input class="form-control" type="time" value="" id="example-time-input"/>
                          <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
                      </span>
                  </div>
                  <br>
                  <div class='input-group date' id='datetimepicker3'>
                      <input type='text' class="form-control" placeholder="Descriptions"/>
                          <span class="input-group-addon">
                          <span class="glyphicon glyphicon-pencil"></span>
                      </span>
                  </div>
              </div>
          </div>
          </div>
          <br>
          <a href="{{ url('/greentag') }}">
              <button type="button" class="btn btn-success pull-left">
                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Submit
              </button>
          </a>
          </br>
          <br>
          </br>
      <h5>
  </li>
</div>

@endsection
