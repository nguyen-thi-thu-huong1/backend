@extends('layouts.baseframe')

@section('title', $_title)

@section('css')
    <style>
        .table-hover>tbody>tr:hover {
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
                        <button type="button"
                            onclick="window.location.href='{{ route('admin.gamerecords.report', $backParams) }}'">
                            <i class="mdi mdi-skip-backward"></i> @lang('res.btn.back')
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @include('layouts._per_page', [
                    'export_excel' => true,
                    'route' => 'gamerecord.export-sbo',
                    'id' => isset($id) ? $id : null,
                    'created_at' => isset($params['created_at']) ? $params['created_at'] : null,
                ])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center align-middle bg-primary">STT</th>
                                <th class="text-center align-middle bg-primary" style="min-width: 315px">
                                    {{ trans('res.transaction_history.field.time') }}</th>
                                <th class="text-center align-middle bg-primary" style="min-width: 200px" colspan="2">
                                    {{ trans('res.transaction_history.field.game') }}</th>
                                <th class="text-center align-middle bg-primary" colspan="2">
                                    {{ trans('res.transaction_history.field.amount') }}</th>
                                <th class="text-center align-middle bg-primary" colspan="2">
                                    {{ trans('res.transaction_history.field.win_loss') }}</th>
                                <th class="text-center align-middle bg-primary" style="min-width: 100px;">
                                    {{ trans('res.transaction_history.field.status') }}</th>
                                <th class="text-center align-middle bg-primary" style="min-width: 100px;" colspan="2">
                                    {{ trans('res.transaction_history.field.fs_detail') }}</th>
                                <th class="text-center align-middle bg-primary" style="min-width: 100px;">
                                    {{ trans('res.transaction_history.field.balance_before') }}</th>
                                <th class="text-center align-middle bg-primary">
                                    {{ trans('res.transaction_history.field.balance_after') }}</th>
                                <th class="text-center align-middle bg-primary" style="min-width: 180px">
                                    {{ trans('res.banner.field.created_at') }} (GMT+7)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $key + 1 }}</td>
                                    <td>
                                        <p>Ref Code: {{ $item->transfer_code }}</p>
                                        <p>Thời gian nhà sản xuất: {{ $item->transaction_time }}</p>
                                    </td>
                                    <td>
                                        {{--                                    <p style="font-weight: bold;color: #b50000;">{!! $item->getGameTypeText() !!}</p> --}}
                                        <p style="font-weight: bold;color: #33cabb;">{!! $item->getGameProviderText() !!}</p>
                                        <p style="font-weight: bold;color: #4d5259;">{!! $item->getProductTypeText() !!}</p>
                                        <p style="font-weight: bold;color: #4d5259;">{!! $item->getMemberPhoneAttribute() !!}</p>
                                        <a href="javascript:void(0);" data-operate="iframe-page"
                                            data-url="{{ route('admin.gamerecords.getBetDetail', ['id' => $item->id]) }}"
                                            class="btn btn-warning btn-xs">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                    <td>
                                        @if(isset($item->wager_id))
                                            <div class="" style="display: flex;">
                                                <p style="font-weight: bold;color: #4d5259; white-space: nowrap">{!! $item->wager ? $item->wager->gameName->en_name : '' !!}</p>
                                                [{{ $item->wager_id }}]
                                            </div>
                                            <a href="javascript:void(0);" data-operate="iframe-page"
                                                data-url="{{ route('admin.gamerecords.getBetDetailSwmd', ['id' => $item->wager_id]) }}"
                                                class="btn btn-warning btn-xs">
                                                Xem chi tiết
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ moneyFormat($item->amount) }}</td>
                                    <td class="text-right">{{ $item->wager ? $item->wager->valid_bet_amount : '' }}</td>
                                    <td class="text-right">{!! $item->getWinLossText() !!}</td>
                                    <td class="text-right">{{ $item->wager ? $item->wager->jackpot_amount : '' }}</td>
                                    <td class="text-center">{!! $item->getStatusText() !!}</td>
                                    <td class="text-left">{!! $item->getFsDetailByMoneyText($item->amount) !!}</td>
                                    <td class="text-right">{{ $item->wager ? $item->wager->commision_amount : '' }}</td>
                                    <td class="text-right">{{ moneyFormat($item->balance_before) }}</td>
                                    <td class="text-right">{{ moneyFormat($item->balance_after) }}</td>
                                    <td class="text-center"> {{ $item->created_at }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($data)
                    <div class="clearfix">
                        <div class="pull-left">
                            <p>@lang('res.common.total') <strong style="color: red">{{ $data->total() }}</strong> @lang('res.common.count')
                            </p>
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
                        <button type="button" onclick="window.location.href='{{ route('admin.gamerecords.report') }}'">
                            <i class="mdi mdi-skip-backward"></i> @lang('res.btn.back')
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
