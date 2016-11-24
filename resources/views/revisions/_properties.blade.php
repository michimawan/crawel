@php $projectName = strtolower($projectName) @endphp
@php
$fields = [
    'Child Tag Revisions' => 'child_tag_revisions',
    'End Time Check Story' => 'end_time_check_story',
    'End Time Run Automate Test' => 'end_time_run_automate_test',
    'Time Get Canary' => 'time_get_canary',
    'Time to Elb' => 'time_to_elb',
];
@endphp
<div class='col-sm-6'>
  <div class="form-group">
  @foreach ($fields as $title => $field)
        @php $name = "{$projectName}_{$field}" @endphp
      <!-- <div class="form&#45;group"> -->
      <!-- <div class='input&#45;group date' id='datetimepicker3'> -->
      <!--     {!! Form::label($name, ucwords(str_replace('_', ' ', $name))) !!} -->
      <!--     {!! Form::text($name, '', ['class' => 'form&#45;control']) !!} -->
      <!-- </div> -->
      <!-- </div> -->
    <div class="form-group">
      <div class='input-group date' id='datetimepicker3'>
      <input name="<?php echo $name ?>" type='text' class="form-control" placeholder="<?php echo $title ?>"/>
          <span class="input-group-addon"/>
          <span class="glyphicon glyphicon-time"/>
      </div>
    </div>
    @endforeach
    @php $name = "{$projectName}_description" @endphp
    <div class="form-group">
      <div class='input-group date' id='datetimepicker3'>
      <input name="<?php echo $name ?>" type='text' class="form-control" placeholder="Description"/>
          <span class="input-group-addon"/>
          <span class="glyphicon glyphicon-time"/>
      </div>
    </div>
  </div>
</div>
