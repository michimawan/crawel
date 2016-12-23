<div class="container">
<h1>Today {{ $projectName }} Story</h1>
<div class="table">
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-default">
            <tr>
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
            @foreach($rev as $revision)
            <tr>

              <?php $number = 1; ?>
              <td>{{ $revision->created_at->toDateTimeString() }}</td>
              <td>
                <p>{{ $revision->child_tag_revisions }}</p>
              </td>
              <td>
                <p>{{ $revision->child_tag_revisions }}</p>
              </td>
              <td>
              @php $storiesString = ""; @endphp
              @foreach($revision->tags as $tag)
                  @php $tmp = ""; @endphp
                  @foreach($tag->stories as $i => $item)
                      @php
                      $str = "[#" . $item->pivotal_id . "]";
                      $str .= isset($projectIds[$item->project_id]) ? "[" . $projectIds[$item->project_id] . "] " : "";

                      $type = $item->story_type == 'feature' ? $item->point . ' point(s)' : $item->story_type;

                      $str .= "<span class='box ellipsis'>{$item->title}</span>";
                      $str .= " (". $type .", " . $item->status . ")";

                      $tmp .= $str . "<br>";
                      @endphp
                  @endforeach
                  @php $storiesString .= $tmp; @endphp
              @endforeach
              @php echo $storiesString @endphp
              </td>
              <td><p>{{ $revision->end_time_check_story }}</p></td>
              <td><p>{{ $revision->end_time_run_automate_test }}</p></td>
              <td><p>{{ $revision->time_get_canary }}</p></td>
              <td><p></p></td>
              <td><p>{{ $revision->time_to_elb }}</p></td>
              <td><p>{{ $revision->description }}</p></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
