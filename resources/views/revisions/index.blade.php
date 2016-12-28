@extends('layouts.app')

@section('content')
{{ Html::style('css/jquery-ui.min.css') }}

<div class="container">
<div class="row">
{!! Form::open([
  'route' => 'revisions.index',
  'method' => 'GET',
  'class' => '']) !!}
  {!! Form::text('date', '', ['class' => 'btn-small', 'id' => 'datepicker']) !!}
  {!! Form::submit('Submit', ['class' => 'btn btn-info btn-sm']) !!}
{!! Form::close() !!}
</div>
<div class="row">
  <ul class="navbar-right btn btn-group">
    <a id="create-daily-mail" href="{{ route('mails.create') }}" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Create Daily Mail
    </a>
  </ul>
  <!-- Nav tabs -->
  @include('elements.navtabs', [
    'projects' => $projects,
  ])
  <!-- Tab panes -->
  <div class="tab-content">
    @php
    $active = ' active';
    @endphp
    @foreach($projects as $projectName => $projectIds)
        <div role="tabpanel" class="tab-pane{!! $active !!}" id="{{ $projectName }}">
        @include('revisions._data', [
            'projectName' => $projectName,
            'rev' => is_null($rev->get($projectName)) ? collect() : $rev->get($projectName)->values(),
            'projectIds' => $projectIds
        ])
        </div>
        @php
        $active = '';
        @endphp
    @endforeach
  </div>
</div>
</div>

</div>

<script type="text/javascript">
$(document).ready(function() {
  $('.edit-btn').click(function() {
    var id = $(this).data('id');
    if (isEdit(this)) {
      changeToForm(id);
    } else {
      var datas = submitForm(id);
      returnToNormal(id, datas);
    }
  });

  function isEdit(element) {
    return $(element).text() === 'Edit';
  }

  function changeToForm(id) {
    var endTimeCheckStoryForm = '<input name="end-time-check-story"/>';
    var endTimeRunAutomateTest = '<input name="end-time-run-automate-test"/>';
    var timeGetCanary = '<input name="time-get-canary"/>';
    var timeToElb = '<input name="time-to-elb"/>';
    var description = '<input name="description"/>';

    $('.end-time-check-story[data-id=' + id + ']').html(endTimeCheckStoryForm);
    $('.end-time-run-automate-test[data-id=' + id + ']').html(endTimeRunAutomateTest);
    $('.time-get-canary[data-id=' + id + ']').html(timeGetCanary);
    $('.time-to-elb[data-id=' + id + ']').html(timeToElb);
    $('.description[data-id=' + id + ']').html(description);
    $('.edit-btn[data-id=' + id + ']').text('Submit');
    $('.edit-btn[data-id=' + id + ']').removeClass('btn-info');
    $('.edit-btn[data-id=' + id + ']').addClass('btn-success');
  }

  function submitForm(id) {
    var data = {};

    data.end_time_check_story = $('.end-time-check-story[data-id=' + id + '] input').val();
    data.end_time_run_automate_test = $('.end-time-run-automate-test[data-id=' + id + '] input').val();
    data.time_get_canary = $('.time-get-canary[data-id=' + id + '] input').val();
    data.time_to_elb = $('.time-to-elb[data-id=' + id + '] input').val();
    data.description = $('.description[data-id=' + id + '] input').val();
    data._token = $('.token[data-id=' + id + '] input').val();

    $.ajax({
      type: "POST",
      url: '/revisions/update/' + id,
      data: data,
    }).done(function(responses) {
      data = responses.datas;
    }).fail(function(responses) {
      alert('fail to save data, an error occured.');
    });

    return data;
  }

  function returnToNormal(id, data) {
    $('.end-time-check-story[data-id=' + id + ']').html(data.end_time_check_story);
    $('.end-time-run-automate-test[data-id=' + id + ']').html(data.end_time_run_automate_test);
    $('.time-get-canary[data-id=' + id + ']').html(data.time_get_canary);
    $('.time-to-elb[data-id=' + id + ']').html(data.time_to_elb);
    $('.description[data-id=' + id + ']').html(data.description);
    $('.edit-btn[data-id=' + id + ']').addClass('btn-info');
    $('.edit-btn[data-id=' + id + ']').removeClass('btn-success');
    $('.edit-btn[data-id=' + id + ']').text('Edit');
  }
});
</script>
@endsection