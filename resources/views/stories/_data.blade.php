<div class="container">
<h1>Today {{ $projectName }} Story</h1>
<div class="table">
    <table class="table table-bordered table-striped table-hover">
        @foreach($tag as $greenTag)
        <li class="list-group-item">
            <h4>Greentag &nbsp; &nbsp;<span>{{ $greenTag->code }}</span></h4>
            <h5> Story List </h5>
        @foreach($greenTag->stories as $i => $item)
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
        </li>
        @endforeach
        </tbody>
    </table>
</div>
</div>
