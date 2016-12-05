<div class="container">
<h1>Today {{ $projectName }} Story</h1>
<div class="table">
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-default">
            <tr>
              <th>No</th>
              <th>Date</th>
              <th>Revision</th>
              <th>Get Greentag Timing</th>
              <th>Stories</th>
              <th>End Time For Check Story</th>
              <th>End Time For Check Automation</th>
              <th>Time To Get Canary</th>
              <th>Time To Finish Test In Canary</th>
              <th>Time To Finish ELB</th>
              <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tag as $greenTag)
            <tr>
       
              <?php $number = 1; ?>
              <td> {{ $number++ . "." }} </td>
              <td>date</td>
              @foreach($greenTag->revisions as $i => $item)
              <td>
                <p>{{ $item->child_tag_revisions }}</p>
              </td>
              @foreach($greenTag->stories as $i => $item)
              <td>{{ $item->created_at }}</td>
              <td>
                <p>
                    {{ ($i+1) . "." }}
                    {{ "[#" . $item->pivotal_id . "]" }}
                    {{ isset($projectIds[$item->project_id]) ? $projectIds[$item->project_id] : "" }}
                    <span class="box ellipsis">{{ $item->title }}</span>
                    <?php
                        $type = $item->story_type == 'feature' ? $item->point . ' points' : $item->story_type;
                    ?>
                    {{ "(". $type .", " . $item->status . ")"}}
                </p>
              @endforeach
              </td>
              <td><p>{{ $item->end_time_check_story }}</p></td>
              <td><p>{{ $item->end_time_run_automate_test }}</p></td>
              <td><p>{{ $item->time_get_canary }}</p></td>
              <td><p>{{ $item->time_to_elb }}</p></td>
              <td><p>{{ $item->description }}</p></td>
              @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
