@extends('layouts.app')

@section('content')
<div class="container">

    <h1>Crawler</h1>
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th> No </th>
                    <th> Story Id </th>
                    <th> Project Name </th>
                    <th> Title </th>
                    <th> Story Type </th>
                    <th> Point </th>
                </tr>
            </thead>
            <tbody>
            @foreach($stories as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ "[#" . $item->id . "]"}}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->story_type == 'feature' ? $item->estimate : 0 }}</td>
                    <td>{{ $item->story_type }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
