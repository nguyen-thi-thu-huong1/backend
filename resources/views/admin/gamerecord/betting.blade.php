@extends('layouts.baseframe')

@section('title', $_title)

@section('css')
    <link rel="stylesheet" href="{{ asset('css/vendor/daterangepicker.css') }}">
    <style>
        .table-hover>tbody>tr:hover {
            background-color: #f8eb95;
        }
    </style>
@endsection

@php
    $queryParams = request()->all();
    $reportPage = data_get($queryParams, 'page');
    $queryParams['report_page'] = $reportPage;
    unset($queryParams['page']);
@endphp

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#searchContent" aria-expanded="false"
                            aria-controls="searchContent"><i class="mdi mdi-chevron-double-down"></i>
                            @lang('res.btn.collapse')</button>
                    </li>
                    <li>
                        <button type="button" onclick="window.location.reload()"><i class="mdi mdi-refresh"></i>
                            @lang('res.btn.refresh')</button>
                    </li>
                </ul>
            </div>
            <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                <form action="" method="get" id="searchForm" name="searchForm">
                    <div class="row">
                        @include('layouts._search_field', [
                            'list' => [
                                'name' => [
                                    'name' => trans('res.member.field.name'),
                                    'type' => 'text',
                                    'col' => 'col-sm-3',
                                ],
                                'created_at' => [
                                    'name' => trans('res.transaction_history.field.created_at'),
                                    'type' => 'predefined-date-range',
                                    'col' => 'col-sm-3',
                                ],
                                'product_type' => [
                                    'name' => trans('res.transaction_history.field.product_type'),
                                    'type' => 'select',
                                    'data' => \App\Models\TransactionHistory::getProductType(),
                                    'col' => 'col-sm-3',
                                ],
                            ],
                        ])

                        <div class="col-sm-3">
                            <div class="input-group">
                                <button type="submit" class="btn btn-primary">@lang('res.btn.search')</button>&nbsp;
                                <button type="reset" class="btn btn-warning"
                                    onclick="window.location.href='{{ route('admin.gamerecords.betting') }}'">@lang('res.btn.reset')</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @include('layouts._per_page')

                @include('layouts._paging')

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle bg-primary">
                                    {{ trans('res.transaction_history.field.member_id') }}</th>
                                <th class="text-center bg-primary">Số lượng cược</th>
                                <th class="text-center bg-primary">Tổng tiền cược</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td><a
                                            href="{{ route('admin.gamerecords.betting_detail', array_merge($queryParams, ['member_id' => $item->id])) }}">{{ $item->name }}</a>
                                    </td>
                                    <td class="text-right">{{ number_format($item->total_records) }}</td>
                                    <td class="text-right">{{ moneyFormat($item->total_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @include('layouts._paging')
            </div>
        </div>
    </div>
    @yield('footer-js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('js/daterangepicker/setting-daterangepicker.js') }}"></script>
    <script src="{{ asset('web/js/report/index.js') }}"></script>
@endsection
