<div class="container">
<h1>Today {{ $projectName }} Story</h1>
<div class="table">
    <table class="table table-bordered table-striped table-hover">
        @foreach($tag as $greenTag)
        @foreach($greenTag->stories as $i => $item)
        <div class="tab-content">
            <li class="list-group-item">
                <h4>Greentag &nbsp; &nbsp;
                    <span>
                        {{ $greenTag->code }}
                    </span>
                </h4>
            </li>
            <li class="list-group-item">
                <h5> Story List &nbsp; &nbsp;
                    <br>
                    <?php $counter = 1; ?>
                        <span>
                            {{ $counter . "." }}
                        </span>
                    <?php $counter++; ?>
                    {{ "[#" . $item->pivotal_id . "]" }}
                    {{ isset($projectIds[$item->project_id]) ? $projectIds[$item->project_id] : "" }}
                    <span class="box ellipsis">
                        {{ $item->title }}
                    </span>
                    <?php
                        $type = $item->story_type == 'feature' ? $item->point . ' points' : $item->story_type;
                    ?>
                    {{ "(". $type .", " . $item->status . ")"}}
                    </br>
                    <br>
                    <a href="{{ url('/greentag') }}">
                        <button type="button" class="btn btn-default pull-right">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                        </button>
                    </a>
                    </br>
                <h5>
            </li>
        </div>
        <tbody>
        @endforeach
        @endforeach
        </tbody>
    </table>
</div>
</div>
