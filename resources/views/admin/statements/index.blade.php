@extends('layouts.baseframe')

@section('title', $_title)
@section('css')
    <link rel="stylesheet" href="{{ asset('css/vendor/daterangepicker.css') }}">
@endsection
@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#searchContent" aria-expanded="false"
                            aria-controls="searchContent">
                            <i class="mdi mdi-chevron-double-down"></i> @lang('res.btn.collapse')
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="javascript:window.location.reload()">
                            <i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                <form action="" method="get" id="searchForm" name="searchForm">
                    <div class="row">
                        @include('layouts._search_field', [
                            'list' => [
                                'created_at' => [
                                    'name' => trans('res.common.created_at'),
                                    'type' => 'predefined-date-range',
                                ],
                            ],
                        ])

                        <input type="hidden" name="member_id" value="{{ $params['member_id'] ?? '' }}">

                        <div class="col-lg-3 col-sm-3">
                            <div class="input-group">
                                <button type="submit" class="btn btn-primary">@lang('res.btn.search')</button>&nbsp;
                                <button type="reset" class="btn btn-warning"
                                    onclick="document.searchForm.reset()">@lang('res.btn.reset')</button>&nbsp;
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
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width=100 style="min-width: 110px; text-transform: capitalize">@lang('validation.attributes.day')</th>
                                <th width=100 style="min-width: 150px">@lang('Số dư đầu ngày')</th>
                                <th width=100 style="min-width: 150px">@lang('Số dư giữa ngày')</th>
                                <th width=100 style="min-width: 110px">@lang('Tiền cược')</th>
                                <th width=100 style="min-width: 110px">@lang('Hủy / Từ chối')</th>
                                <th width=100 style="min-width: 110px">@lang('Tiền chưa xử lý')</th>
                                <th width=100 style="min-width: 110px">@lang('Thắng / thua')</th>
                                <th width=100 style="min-width: 110px">@lang('Hoa hồng')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item->date}}</td>
                                    <td>
                                        {{ $item->balance_start_day }}
                                    </td>
                                    <td>
                                        {{ $item->balance_middle_day }}
                                    </td>
                                    <td>
                                        {{ $item->bet_amount }}
                                    </td>
                                    <td>{{ $item->cancel_amount }}</td>
                                    <td>{{ $item->pending_amount }}</td>
                                    <td>
                                        @if($item->win_loss > 0)
                                            <strong class="text-success">{{ $item->win_loss }}</strong>

                                        @elseif ($item->win_loss == 0)
                                        <strong>{{ $item->win_loss }}</strong>
                                        @else
                                            <strong class="text-danger">{{ $item->win_loss }}</strong>
                                        @endif
                                    </td>
                                    <td>{{ $item->commission }}</td>
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
