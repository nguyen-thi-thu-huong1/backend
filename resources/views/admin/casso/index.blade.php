@extends('layouts.baseframe')

@section('title', $_title)

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#searchContent" aria-expanded="false" aria-controls="searchContent">
                            <i class="mdi mdi-chevron-double-down"></i> @lang('res.btn.collapse')
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="javascript:window.location.reload()"><i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')</button>
                    </li>
                </ul>
            </div>
            <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                <iframe src="https://public.casso.vn/faaa97a0-4b7a-11ec-a2d4-0bfc29d9f72f" allowfullscreen="allowfullscreen" style="width: 100%; height: 100vh; border: none;"></iframe>
            </div>
        </div>
    </div>
@endsection
