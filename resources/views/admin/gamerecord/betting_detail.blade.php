@extends('layouts.baseframe')

@section('title', $_title)

@section('css')
    <style>
        .table-hover > tbody > tr:hover {
            background-color: #f8eb95;
        }
    </style>
@endsection

@php
    $backParams = request()->all();
    $reportPage = data_get($backParams, 'report_page');
    $backParams['page'] = $reportPage;
    unset($backParams['report_page']);
@endphp

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" onclick="window.location.href='{{ route('admin.gamerecords.betting', $backParams) }}'">
                            <i class="mdi mdi-skip-backward"></i> @lang('res.btn.back')
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @include('layouts._per_page')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.time') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.game') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.amount') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.status') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                        @if($data->total())
                        @foreach ($data as $key => $item)
                            <tr>
                                <td>
                                    <p>Ref Code: {{ $item->transfer_code }}</p>
                                    <p>{{ $item->transaction_time }}</p>
                                </td>
                                <td>
                                    <p style="font-weight: bold;color: #33cabb;">{!! $item->getGameProviderText() !!}</p>
                                    <p style="font-weight: bold;color: #4d5259;">{!! $item->getProductTypeText() !!}</p>
                                </td>
                                <td class="text-right">{{ moneyFormat($item->amount) }}</td>
                                <td class="text-center">{!! $item->getStatusText() !!}</td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="4">Không có kết quả nào</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                @if($data->total())
                    <div class="clearfix">
                        <div class="pull-left">
                            <p>@lang('res.common.total') <strong style="color: red">{{ $data->total() }}</strong> @lang('res.common.count')</p>
                        </div>
                        <div class="pull-right">
                            {!! $data->appends($params)->render() !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" onclick="window.location.href='{{ route('admin.gamerecords.betting', $backParams) }}'">
                            <i class="mdi mdi-skip-backward"></i> @lang('res.btn.back')
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
