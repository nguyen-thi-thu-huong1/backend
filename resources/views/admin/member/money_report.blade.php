@extends('layouts.baseframe')

@section('title', $_title)
@section('css')
    <link rel="stylesheet" href="{{ asset('css/vendor/daterangepicker.css') }}">
@endsection
@section('content')

    <div class="col-sm-12">

        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            {!! trans('res.member.money_report.notice') !!} <br>
            {{ trans('res.common.lang_notice') }}
        </div>

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
                        <button type="button">
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
                                'lang' => ['name' => trans('res.member.field.lang'),'type' => 'select','data' => config('platform.lang_select')],
                                'name' => ['name' => trans('res.member.field.name'),'type' => 'text'],
                                // 'status' => ['name' => '状态','type' => 'select','data' => config('platform.member_status')],
                                'created_at' => ['name' => trans('res.common.created_at'),'type' => 'predefined-date-range']
                            ]
                        ])

                        <div class="col-lg-3 col-sm-3">
                            <div class="input-group">
                                <button type="submit" class="btn btn-primary">@lang('res.btn.search')</button>&nbsp;
                                <button type="reset" class="btn btn-warning"
                                        onclick="document.searchForm.reset()">@lang('res.btn.reset')</button>&nbsp;
                                <button type="button" class="btn btn-info" id="export">@lang('res.btn.export')</button>&nbsp;
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="money_report_table">
                        <thead>
                        <tr>
                            <th style="width: 14%;min-width: 70px;">@lang('res.member.field.name')</th>
                            <th style="width: 8%;min-width: 80px;">@lang('res.member.field.realname')</th>
                            <th style="width: 10%;min-width: 95px;">@lang('res.member.money_report.is_agent_and_top_agent')</th>
                            <th style="width: 5%;min-width: 80px;">@lang('res.member.money_report.recharge_count')</th>
                            <th style="width: 5%;min-width: 80px;">@lang('res.member.money_report.drawing_count')</th>
                            <th style="width: 10%;min-width: 95px;">@lang('res.member.money_report.recharge_sum')</th>
                            <th style="width: 10%;min-width: 95px;">@lang('res.member.money_report.drawing_sum')</th>
                            <th style="width: 10%;min-width: 70px;">@lang('res.member.money_report.total_fs')</th>
                            <th style="width: 10%;min-width: 70px;">@lang('res.member.money_report.total_dividend')</th>
                            <th style="width: 10%;min-width: 70px;">@lang('res.member.money_report.total_other')</th>
                            <th style="width: 18%;min-width: 80px;">@lang('res.agent_page.field.total_profit')</th>
                            <th style="width: 18%;min-width: 80px;">Tổng thắng/thua</th>
                        </tr>
                        </thead>

                        @include('admin.member.money_report_table')
                    </table>
                </div>

                @if($data)
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
    </div>
    @yield('footer-js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('/web/js') }}/layer.js"></script>
    <script src="{{ asset('js/daterangepicker/setting-daterangepicker.js') }}"></script>
    <script src="{{ asset('web/js/report/index.js') }}"></script>
@endsection

@section("footer-js")
    <script src="{{ asset('/js/bootstrap-table/tableExport.min.js') }}"></script>
    <script>
        //日期时间范围
        // laydate.render({
        //     elem: '#created_at',
        //     type: 'datetime',
        //     theme: "#33cabb",
        //     @if(!isCnLanguage())
        //     lang: 'en',
        //     @endif
        //     range: "~"
        // });
        var loading = true;
        var close_reload = @json($close_reload);
        $($('#export').click(function(){
            // $.utils.layerSuccess('测试');
            $('.table-bordered').tableExport({
                type: "excel",
                fileName: "export"
            });
        }));

        // Add loading form search
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            var loadingIndex = layer.load(1, {
                shade: [0.5,'#fff'] //0.1透明度的白色背景
            });

            $.ajax({
                url: $(this).attr('action'),
                type: 'GET',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.html && res.close_reload) {
                        $('#money_report_table tbody').remove();
                        $('#money_report_table tfoot').remove();
                        $('#money_report_table').append(res.html);
                        layer.close(loadingIndex);
                    }
                },
                error: function() {
                    layer.close(loadingIndex);
                    layer.msg('Có lỗi xảy ra', {icon: 2});
                }
            });

        });

    </script>
@endsection
