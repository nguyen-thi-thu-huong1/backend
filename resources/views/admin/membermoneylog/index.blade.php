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
                        @include('layouts._search_field',
                        [
                        'list' => [
                            'member_name' => ['name' => trans('res.common.member_name'),'type' => 'text'],
                            'member_lang' => ['name' => trans('res.member.field.lang'),'type' => 'select','data' => config('platform.lang_select')],
                            'number_type' => ['name' => trans('res.member_money_log.field.number_type'),'type' => 'select','data' => trans('res.option.money_number_type')],
                            'operate_type' => ['name' => trans('res.member_money_log.field.operate_type'),'type' => 'select','data' => trans('res.option.member_money_operate_type')],
                            'money_type' => ['name' => trans('res.member_money_log.field.money_type'),'type' => 'select','data' => trans('res.option.member_money_type')],
                            'created_at' => ['name' => trans('res.common.created_at'),'type' => 'predefined-date-range']
                            ]
                        ])

                        <div class="col-lg-2 col-sm-2">
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
                            <th>
                                <label class="lyear-checkbox checkbox-primary">
                                    <input type="checkbox" id="check-all"><span></span>
                                </label>
                            </th>
                            @include('layouts._table_header',['data' => \App\Models\MemberMoneyLog::$list_field,'model' => 'member_money_log'])
                            {{-- <th width=100>修改时间</th> --}}
                            <th width=100>@lang('res.common.created_at')</th>
                            <th>@lang('res.common.operate')</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>
                                    <label class="lyear-checkbox checkbox-primary">
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}"><span></span>
                                    </label>
                                </td>
                                @include('layouts._table_body',['data' => \App\Models\MemberMoneyLog::$list_field,'item' => $item])
                                {{-- <td>{{ $item->updated_at }}</td> --}}
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <div class="btn-group">

                                        <a class="btn btn-xs btn-default" href="javascript:;" data-operate="show-page"
                                           data-toggle="tooltip" data-original-title="@lang('res.btn.detail')"
                                           data-url="{{ route('admin.membermoneylogs.show', ['membermoneylog' => $item->id]) }}">
                                            <i class="mdi mdi-file-document-box"></i>
                                        </a>

                                        <a class="btn btn-xs btn-default" href="javascript:;" data-operate="delete"
                                           data-toggle="tooltip" data-original-title="@lang('res.btn.delete')"
                                           data-url="{{ route('admin.membermoneylogs.destroy', ['membermoneylog' => $item->id]) }}">
                                            <i class="mdi mdi-window-close"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    Tổng số tiền hoạt động: {!! $data->sum('money') !!}
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
