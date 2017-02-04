<div class="container">
<h1>Today {{ $projectName }} Story</h1>
<div class="table">
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-default">
            <tr>
              <th>Date</th>
              <th>Revision</th>
              <th>Stories</th>
              <th>Time To Check Story</th>
              <th>End Time For Check Story</th>
              <th>End Time For Check Automation</th>
              <th>Time To Get Canary</th>
              <th>Time To Finish Test In Canary</th>
              <th>Time To Finish ELB</th>
              <th>Description</th>
              <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rev as $revision)
            <tr class="revision--item" data-id={{$revision->id}}>
              <td class="token hidden" data-id={{$revision->id}}><input type="hidden" name="_token" value="{{{ csrf_token() }}}" /></td>
              <?php $number = 1; ?>
              <td class="date col-md-1">{{ $revision->created_at->toDateTimeString() }}</td>
              <td class="child-tag-revisions col-md-1">
                <p>{{ $revision->child_tag_revisions }}</p>
              </td>
              <td class="stories col-md-3" data-id={{$revision->id}}>
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
              <td class="time-to-check-story col-md-1" data-id={{$revision->id}}><p>{{ $revision->time_to_check_story }}</p></td>
              <td class="end-time-check-story col-md-1" data-id={{$revision->id}}><p>{{ $revision->end_time_check_story }}</p></td>
              <td class="end-time-run-automate-test col-md-1" data-id={{$revision->id}}><p>{{ $revision->end_time_run_automate_test }}</p></td>
              <td class="time-get-canary col-md-1" data-id={{$revision->id}}><p>{{ $revision->time_get_canary }}</p></td>
              <td class="time-to-finish-test-canary col-md-1" data-id={{$revision->id}}><p>{{ $revision->time_to_finish_test_canary }}</p></td>
              <td class="time-to-elb col-md-1" data-id={{$revision->id}}><p>{{ $revision->time_to_elb }}</p></td>
              <td class="description col-md-2" data-id={{$revision->id}}><p>{{ $revision->description }}</p></td>
              <td>
                <button type="button" class="edit-btn btn btn-info btn-sm" data-id={{$revision->id}}>Edit</button>
              </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
