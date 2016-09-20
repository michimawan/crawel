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
            @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
