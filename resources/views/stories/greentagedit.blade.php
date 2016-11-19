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
          <br>
            <div class="input-group">
              <input type="text" class="form-control" placeholder="End Time To Check Story" aria-describedby="basic-addon2">
              <input type="text" class="form-control" placeholder="End Time To Run Automated Test" aria-describedby="basic-addon2">
              <input type="text" class="form-control" placeholder="Time To Get Canary" aria-describedby="basic-addon2">
              <input type="text" class="form-control" placeholder="Time To ELB" aria-describedby="basic-addon2">
              <input type="text" class="form-control" placeholder="Description" aria-describedby="basic-addon2">
            </div>
          </br>
          <div class="row">
          <div class='col-sm-6'>
              <div class="form-group">
                  <div class='input-group date' id='datetimepicker3'>
                      <input type='text' class="form-control" />
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
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
      <!--       <a href="{{ url('/greentag') }}">
              <button type="button" class="btn btn-success pull-left">
                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Create Daily Email
              </button>
          </a>  -->
          </br>
      <h5>
  </li>
</div>

@endsection