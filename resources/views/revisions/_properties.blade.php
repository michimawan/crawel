@php $projectName = strtolower($projectName) @endphp
@php
$fields = [
    'child_tag_revisions',
    'end_time_check_story',
    'end_time_run_automate_test',
    'time_get_canary',
    'time_to_elb',
];
@endphp
<div class="container">
  <div class="form-group">
    @foreach ($fields as $field)
        @php $name = "{$projectName}_{$field}" @endphp
      <div class="form-group">
      <div class='input-group date' id='datetimepicker3'>
          {!! Form::label($name, ucwords(str_replace('_', ' ', $name))) !!}
          {!! Form::text($name, '', ['class' => 'form-control']) !!}
      </div>
      </div>
    @endforeach
        @php $name = "{$projectName}_description" @endphp
    <div class="form-group">
    <div class='input-group date' id='datetimepicker3'>
      {!! Form::label($name, 'Descriptions') !!}
      {!! Form::text($name, '', ['class' => 'form-control']) !!}
    </div>
    </div>
  </div>
</div>
