@extends('layouts.baseframe')

@section('title', $_title)

@php 
    $banks = getConfig('eeziepay.bank_code') + getConfig('eeziepay.bank_qr_code'); 
    $total_request_amount = 0;
    $total_receive_amount = 0;
    $total_fee = 0;
@endphp

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#searchContent" aria-expanded="false" aria-controls="searchContent"><i class="mdi mdi-chevron-double-down"></i> @lang('res.btn.collapse')</button>
                    </li>
                    <li>
                        <button type="button" onclick="window.location.reload()"><i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')</button>
                    </li>
                </ul>
            </div>

            <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                <form action="" method="get" id="searchForm" name="searchForm">
                    <div class="row">
                        @include('layouts._search_field',
                        [
                        'list' => [
                            'member_name' => ['name' => trans('res.member.field.player_name'), 'type' => 'text'],
                            'bank_code' => ['name' => trans('res.eeziepay_histories.field.bank_code'), 'type' => 'select', 'data' => getConfig('eeziepay.bank_code')],
                            'status' => ['name' => trans('res.eeziepay_histories.field.status'), 'type' => 'select', 'data' => \App\Models\EeziepayHistory::statusConfig()],
                            'transaction_at' => ['name' => trans('res.eeziepay_histories.field.transaction_at'), 'type' => 'datetime'],
                            ]
                        ])

                        <div class="col-lg-3 col-sm-3">
                            <div class="input-group">
                                <button type="submit" class="btn btn-primary">@lang('res.btn.search')</button>&nbsp;
                                <button type="reset" class="btn btn-warning" onclick="document.searchForm.reset()">@lang('res.btn.reset')</button>&nbsp;
                            </div>
                        </div>
                    </div>
                    <div  class="row">
                        <div class="col-md-12">
                            <hr/>
                            <h5>Tổng tiền nạp : {{ number_format($totalAmount) }} VND</h5>
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
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.member_id') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.billno') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.partner_orderid') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.bank_code') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.currency') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.request_amount') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.receive_amount') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.fee') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.transaction_at') }}</th>
                                <th class="text-center align-middle">{{ trans('res.eeziepay_histories.field.status') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                        @if(!empty($data) && $data->total())
                            @foreach ($data as $key => $item)
                            @php
                                $total_request_amount += $item->request_amount;
                                $total_receive_amount += $item->receive_amount;
                                $total_fee += $item->fee;
                            @endphp
                            <tr>
                                <td>{{ $item->member ? $item->member->name : '' }}</td>
                                <td>{{ $item->billno }}</td>
                                <td>{{ $item->partner_orderid }}</td>
                                <td>{{ isset($banks[$item->bank_code]) ? $banks[$item->bank_code] : '' }}</td>
                                <td>{{ $item->currency }}</td>
                                <td>{{ number_format($item->request_amount) }}</td>
                                <td>{{ number_format($item->receive_amount) }}</td>
                                <td>{{ number_format($item->fee) }}</td>
                                <td>{{ $item->transaction_at }}</td>
                                <td>{{ $item->getStatusText() }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr><td colspan="10" class="text-center align-middle">Không tìm thấy kết quả nào.</td></tr>
                        @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ number_format($total_request_amount) }}</td>
                                <td>{{ number_format($total_receive_amount) }}</td>
                                <td>{{ number_format($total_fee) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                          </tfoot>
                    </table>
                </div>

                @include('layouts._paging')
            </div>
            <div class="card-footer">
                <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                    <form action="" method="get" id="searchForm" name="searchForm">
                        <div class="row">
                            @include('layouts._search_field',
                            [
                            'list' => [
                                'member_name' => ['name' => trans('res.member.field.player_name'), 'type' => 'text'],
                                'bank_code' => ['name' => trans('res.eeziepay_histories.field.bank_code'), 'type' => 'select', 'data' => getConfig('eeziepay.bank_code')],
                                'status' => ['name' => trans('res.eeziepay_histories.field.status'), 'type' => 'select', 'data' => \App\Models\EeziepayHistory::statusConfig()],
                                'transaction_at' => ['name' => trans('res.eeziepay_histories.field.transaction_at'), 'type' => 'datetime'],
                                ]
                            ])
    
                            <div class="col-lg-3 col-sm-3">
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary">@lang('res.btn.search')</button>&nbsp;
                                    <button type="reset" class="btn btn-warning" onclick="document.searchForm.reset()">@lang('res.btn.reset')</button>&nbsp;
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>   
        </div>
    </div>
@endsection
