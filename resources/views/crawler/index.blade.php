@extends('layouts.app')

@section('content')
<div class="container">

    <h1>Crawler</h1>
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th> No </th>
                    <th> Project Name </th>
                    <th> Pivotal Id </th>
                    <th> Point </th>
                    <th> Type </th>
                    <th> Title </th>
                </tr>
            </thead>
            <tbody>
            @foreach($crawler as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->project_name }}</td>
                    <td>{{ $item->pivotal_id }}</td>
                    <td>{{ $item->point }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->title }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
