@extends('layouts.app')

@section('content')
<div class="container">

<div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
      <a href="#publishing" aria-controls="publishing" role="tab" data-toggle="tab">Publishing</a>
    </li>
    <li role="presentation">
      <a href="#vidio" aria-controls="vidio" role="tab" data-toggle="tab">Vidio</a>
    </li>
    <li role="presentation">
      <a href="#playback" aria-controls="playback" role="tab" data-toggle="tab">Playback</a>
    </li>
    <li role="presentation">
      <a href="#apps" aria-controls="apps" role="tab" data-toggle="tab">Apps</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="publishing">
        <div class="container">
        <h1>Get new story</h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th> Story Id </th>
                        <th> Title </th>
                        <th> Project Name </th>
                        <th> Story type & Points </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($stories as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ "[#" . $item->pivotal_id . "]" }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->project_id }}</td>
                        <td>{{ "(". $item->story_type . ", " . $item->point . ")"}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="vidio">
      <div class="container">
        <h1>Get new story</h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th> Story Id </th>
                        <th> Title </th>
                        <th> Project Name </th>
                        <th> Story type & Points </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($stories as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ "[#" . $item->pivotal_id . "]" }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->project_id }}</td>
                        <td>{{ "(". $item->story_type . ", " . $item->point . ")"}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="playback">
      <div class="container">
        <h1>Get new story</h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th> Story Id </th>
                        <th> Title </th>
                        <th> Project Name </th>
                        <th> Story type & Points </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($stories as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ "[#" . $item->pivotal_id . "]" }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->project_id }}</td>
                        <td>{{ "(". $item->story_type . ", " . $item->point . ")"}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="apps">
      <div class="container">
        <h1>Get new story</h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th> Story Id </th>
                        <th> Title </th>
                        <th> Project Name </th>
                        <th> Story type & Points </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($stories as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ "[#" . $item->pivotal_id . "]" }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->project_id }}</td>
                        <td>{{ "(". $item->story_type . ", " . $item->point . ")"}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>

</div>
@endsection
