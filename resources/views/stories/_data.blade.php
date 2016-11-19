<div class="container">
<h3>Today {{ $projectName }} Story</h3>
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
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
        <?php $counter = 1; ?>
        @foreach($tag as $greenTag)
        @foreach($greenTag->stories as $i => $item)
            <tr>
                <td>{{ $counter }}</td>
                <td>{{ $greenTag->code }}</td>
                <td>{{ "[#" . $item->pivotal_id . "]" }}</td>
                <td>{{ isset($projectIds[$item->project_id]) ? $projectIds[$item->project_id] : "" }}</td>
                <td>{{ $item->title }}</td>
                <?php
                $type = $item->story_type == 'feature' ? $item->point . ' point(s)' : $item->story_type;
                ?>
                <td>{{ "(". $type .", " . $item->status . ")"}}</td>
                <td> 
                    <a href="{{ url('/greentag') }}">
                        <button type="button" class="btn btn-default btn-lg">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                        </button>
                    </a>
                </td>
            </tr>
        <?php $counter++; ?>
        @endforeach
        @endforeach
        </tbody>
    </table>
</div>
</div>
