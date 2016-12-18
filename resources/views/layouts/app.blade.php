<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pivotal Crawler</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    {{ Html::style('css/bootstrap.min.css') }}
    {{ Html::style('css/bootstrap-tour.min.css') }}
    <!-- Styles -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter&#45;bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384&#45;1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous"> -->
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <!-- Branding Image -->
                <a id="brand" class="navbar-brand" href="{{ url('/') }}">
                    Pivotal Crawler
                </a>
            </div>
            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <ul id="create-story-list" class="nav navbar-brand navbar-right">
                    <a href="{{ route('revisions.create') }}">
                        <button type="button" class="btn btn-success btn-sm">Create Story List</button>
                    </a>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    {{ Html::script('js/jquery.min.js') }}
    {{ Html::script('js/bootstrap.min.js') }}
    {{ Html::script('js/bootstrap-tour.min.js') }}
    {{ Html::script('js/jquery-ui.min.js') }}
    {{ Html::script('js/tour.js') }}
    <script type="text/javascript">
      $(function() {
        $( "#datepicker" ).datepicker({
          dateFormat: 'yy-mm-dd'
        });
      });
    </script>
</body>
</html>
