       	<div class="container">
        <h1>Today {{ $projectName }} Story</h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>GreenTag Id</th>
                        <th>Story Id</th>
                        <th>Project Name</th>
                        <th>Title</th>
                        <th>Story type & Points</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tag as $greenTag)
                @foreach($greenTag->stories as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $greenTag->code }}</td>
                        <td>{{ "[#" . $item->pivotal_id . "]" }}</td>
                        <td>{{ isset($projectIds[$item->project_id]) ? $projectIds[$item->project_id] : "" }}</td>
                        <td>{{ $item->title }}</td>
                        <?php
                        $type = $item->story_type == 'feature' ? $item->point . ' point(s)' : $item->story_type;
                        ?>
                        <td>{{ "(". $type .", " . $item->status . ")"}}</td>
                    </tr>
                @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
      </div>
