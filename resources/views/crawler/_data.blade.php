       	<div class="container">
        <h1>Today {{ $projectName }} Story</h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Story Id</th>
                        <th>Title</th>
                        <th>Project Name</th>
                        <th>Story type & Points</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($stories as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ "[#" . $item->pivotal_id . "]" }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ isset($projectIds[$item->project_id]) ? $projectIds[$item->project_id] : "" }}</td>
                        <td>{{ "(". $item->story_type . ", " . $item->point . ")"}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
      </div>