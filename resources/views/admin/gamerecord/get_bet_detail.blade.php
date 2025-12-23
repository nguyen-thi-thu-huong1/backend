@extends('layouts.baseframe')

@section('title', $_title)

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <iframe src="{{ $url }}" width="100%" height="600">Your browser isn't compatible</iframe>
            </div>
        </div>
    </div>
@endsection
