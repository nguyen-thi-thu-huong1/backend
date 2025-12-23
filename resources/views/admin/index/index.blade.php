@extends('layouts.baseframe')
@section('title', $_title)
<link rel="stylesheet" href="{{ asset('/js/bootstrap-multitabs/multitabs.min.css') }}">
<style>
    .d-flex {
        display: flex
    }

    .card,
    .card-body {
        height: 125px;
    }

    .align-items-center {
        align-items: center;
    }

    .card-body {
        height: auto;
    }

    .card .card-body div.table-responsive:after {
        min-height: auto
    }

    .justify-between {
        justify-content: space-between;
    }

    .card .pull-right {
        margin-right: auto
    }
</style>

@section('content')

    {{-- http://lyear.itshubao.com/iframe/lyear_main.html --}}

    {{-- 今日注册，今日营销，今日投注，今日游戏总营收，本月 --}}

    @if (auth()->user()->hasPermissionTo('欢迎界面'))
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMemberRegisterToday() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.today_register')",
                                url: "{{ route('admin.members.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-primary" onclick="createTabMemberRegisterToday()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.today_register')</p>
                                <p class="h3 text-white m-b-0">{{ number_format($today_register) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-account fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMemberLoginToday() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.today_login')",
                                url: "{{ route('admin.members.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-primary" onclick="createTabMemberLoginToday()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.today_login')</p>
                                <p class="h3 text-white m-b-0">{{ number_format($today_login) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-account fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="col-sm-6 col-md-3">
                    <div class="card bg-danger">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.today_free')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($today_free) }}&nbsp;{{ getCurrentCurrency() }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card bg-success">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.today_bet')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($today_bet) }}&nbsp;{{ getCurrentCurrency() }}</p>
                            </div>
                            <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i
                                        class="mdi mdi-cards-playing-outline fa-1-5x"></i></span> </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabTodayGameProfit() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.today_game_profit')",
                                url: "{{ route('admin.gamerecords.report', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-purple" onclick="createTabTodayGameProfit()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.today_game_profit')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($today_game_profit) }}&nbsp;{{ getCurrentCurrency() }}</p>
                            </div>
                            <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i
                                        class="mdi mdi-cash-multiple fa-1-5x"></i></span> </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMemberRegisterMonth() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.month_register')",
                                url: "{{ route('admin.members.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->startOfMonth()->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-primary" onclick="createTabMemberRegisterMonth()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.month_register')</p>
                                <p class="h3 text-white m-b-0">{{ number_format($month_register) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-account fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card bg-danger">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.month_free')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($month_free) }}&nbsp;{{ getCurrentCurrency() }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card bg-success">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.month_bet')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($month_bet) }}&nbsp;{{ getCurrentCurrency() }}</p>
                            </div>
                            <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i
                                        class="mdi mdi-cards-playing-outline fa-1-5x"></i></span> </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMonthGameProfit() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.month_game_profit')",
                                url: "{{ route('admin.gamerecords.report', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->startOfMonth()->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-purple" onclick="createTabMonthGameProfit()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.month_game_profit')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($month_game_profit) }}&nbsp;{{ getCurrentCurrency() }}</p>
                            </div>
                            <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i
                                        class="mdi mdi-cash-multiple fa-1-5x"></i></span> </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMemberTotal() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.notice.members_title')",
                                url: "{{ route('admin.members.index') }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-success" onclick="createTabMemberTotal()">
                        <div class="card-body clearfix d-flex align-items-center justify-between sidebar-main active"
                            style="padding: 16px;">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.member_total')
                                </p>
                                <p class="h3 text-white m-b-0">{{ $member_total }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-account fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabOnlineMember() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.online_member')",
                                url: "{{ route('admin.members.index', ['is_online' => \App\Models\Member::IS_ONLINE]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-warning" onclick="createTabOnlineMember()">
                        <div class="card-body clearfix d-flex align-items-center justify-between" style="padding: 16px;">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.online_member')
                                </p>
                                <p class="h3 text-white m-b-0">{{ $online_member }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-account fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabLoginDayMember() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.member_login_today')",
                                url: "{{ route('admin.memberlogs.index', ['type' => \App\Models\MemberLog::LOG_TYPE_API_LOGIN , 'created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-pink" style="background-color: #d63384" onclick="createTabLoginDayMember()">
                        <div class="card-body clearfix d-flex align-items-center justify-between" style="padding: 16px;">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.member_login_today')
                                </p>
                                <p class="h3 text-white m-b-0">{{ $member_login_today }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-account fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMoneyDepositTotay() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.money_deposit_today')",
                                url: "{{ route('admin.recharges.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-info" onclick="createTabMoneyDepositTotay()">
                        <div class="card-body clearfix d-flex align-items-center justify-between" style="padding: 16px;">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.money_deposit_today')</p>
                                <p class="h3 text-white m-b-0">{{ number_format($money_deposit_today) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMoneyWithdrawToday() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.money_withdraw_today')",
                                url: "{{ route('admin.recharges.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-dark" onclick="createTabMoneyWithdrawToday()">
                        <div class="card-body clearfix d-flex align-items-center justify-between" style="padding: 16px;">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.money_withdraw_today')</p>
                                <p class="h3 text-white m-b-0">{{ number_format($money_withdraw_today) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMoneyTotalToday() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.money_total_today')",
                                url: "{{ route('admin.drawings.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s')]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-purple" onclick="createTabMoneyTotalToday()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.money_total_today')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($money_total_today) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMoneyTotalUnprocessedToday() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.money_total_unprocessed_today')",
                                url: "{{ route('admin.recharges.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s'), 'status' => \App\Models\Recharge::STATUS_UNDEAL]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-brown" onclick="createTabMoneyTotalUnprocessedToday()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.money_total_unprocessed_today')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($money_total_unprocessed_today) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabMoneyTotalUnprocessedWithdrawToday() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.money_total_unprocessed_withdraw_today')",
                                url: "{{ route('admin.drawings.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s'), 'status' => \App\Models\Drawing::STATUS_UNDEAL]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-cyan" onclick="createTabMoneyTotalUnprocessedWithdrawToday()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.money_total_unprocessed_withdraw_today')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($money_total_unprocessed_withdraw_today) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <script>
                        function createTabDailyTotalReturnToday() {
                            parent.$(parent.document).data('multitabs').create({
                                iframe: true,
                                title: "@lang('res.index.daily_total_return_today')",
                                url: "{{ route('admin.membermoneylogs.index', ['created_at' => \Carbon\Carbon::now()->setTime(0, 0, 0)->format('d/m/Y H:i:s') . ' - ' . \Carbon\Carbon::now()->setTime(23, 59, 59)->format('d/m/Y H:i:s'), 'status' => \App\Models\Drawing::STATUS_UNDEAL]) }}"
                            }, true);
                        }
                    </script>
                    <div class="card bg-yellow" onclick="createTabDailyTotalReturnToday()">
                        <div class="card-body clearfix d-flex align-items-center justify-between">
                            <div class="pull-right">
                                <p class="h6 text-white m-t-0">@lang('res.index.daily_total_return_today')</p>
                                <p class="h3 text-white m-b-0">
                                    {{ number_format($daily_total_return_today) }}</p>
                            </div>
                            <div class="pull-left">
                                <span class="img-avatar img-avatar-48 bg-translucent">
                                    <i class="mdi mdi-currency-cny fa-1-5x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bieu do --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="">
                        <div class="card-header">
                            <h4>@lang('res.index.10_days_recharge')</h4>
                        </div>
                        <div class="bg-white">
                            <canvas class="js-chartjs-lines" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="">
                        <div class="card-header">
                            <h4>@lang('res.index.10_days_drawing')</h4>
                        </div>
                        <div class="bg-white">
                            <canvas class="js-chartjs-drawing-lines" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="">
                        <div class="card-header">
                            <h4>@lang('res.index.win_lose')</h4>
                        </div>
                        <div class="bg-white">
                            <canvas class="js-chartjs-vertical-bar" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="">
                        <div class="card-header">
                            <h4>Biểu đồ giao dịch nạp - rút - hoàn trả 30 ngày qua</h4>
                        </div>
                        <div class="bg-white">
                            <canvas class="js-chartjs-stacked-bar" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="">
                        <div class="card-header">
                            <h4>Biểu đồ báo cáo thành viên truy cập theo thiết bị hoặc hệ điều hành</h4>
                        </div>
                        <div class="bg-white">
                            <canvas class="js-chartjs-device-pie" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="">
                        <div class="card-header">
                            <h4>Biểu đồ báo cáo thành viên truy cập theo vị trí Quốc gia</h4>
                        </div>
                        <div class="bg-white">
                            <canvas class="js-chartjs-country-bar" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-sm-12">
            @lang('res.index.welcome'){{ auth()->user()->name }}
        </div>
    @endif
    <script></script>
@endsection

@section('footer-js')
    <script type="text/javascript" src="{{ asset('js/Chart.js') }}"></script>
    <script>
        $(document).ready(function(e) {
            const arrColors = ["#FF0000", "#0000FF", "#00FF00", "#FFA500", "#800080", "#FFC0CB", "#A52A2A",
                "#808080", "#444555", "#00FFFF", "#FF00FF", "#800000", "#808000", "#008000", "#008080",
                "#000080", "#C0C0C0", "#FFD700", "#ADFF2F", "#4B0082", "#7FFF00", "#FF6347", "#6A5ACD",
                "#FF4500", "#DC143C", "#8A2BE2", "#7CFC00", "#D2691E", "#5F9EA0", "#FF69B4", "#B0E0E6",
                "#8B008B", "#9400D3", "#FF1493", "#7B68EE", "#00FA9A", "#48D1CC", "#4682B4", "#D2B48C",
                "#DA70D6", "#CD5C5C", "#FF8C00", "#32CD32", "#FA8072", "#20B2AA", "#778899", "#BDB76B",
                "#FFDAB9", "#00CED1", "#FFFF00"
            ];
            // $dashChartBarsCnt  = jQuery( '.js-chartjs-bars' )[0].getContext( '2d' )
            var $dashChartLinesCnt = jQuery('.js-chartjs-lines')[0].getContext('2d'),
                $dashChartDrawingLineCnt = jQuery('.js-chartjs-drawing-lines')[0].getContext('2d'),
                $dashChartVerticalBarCnt = jQuery('.js-chartjs-vertical-bar')[0].getContext('2d'),
                $dashChartStackedBarCnt = jQuery('.js-chartjs-stacked-bar')[0].getContext('2d'),
                $dashChartCountryBarCnt = jQuery('.js-chartjs-country-bar')[0].getContext('2d'),
                $dashChartDevicePieCnt = jQuery('.js-chartjs-device-pie')[0].getContext('2d');

            var $dashChartLinesData = {
                // labels: ['2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014'],
                labels: {!! json_encode(array_keys($last_10days)) !!},
                datasets: [{
                    label: '@lang('res.index.recharge_title')',
                    // data: [20, 25, 40, 30, 45, 40, 55, 40, 48, 40, 42, 50],
                    data: {!! json_encode(array_values($last_10days)) !!},
                    borderColor: '#358ed7',
                    backgroundColor: 'rgba(53, 142, 215, 0.175)',
                    borderWidth: 1,
                    fill: false,
                    lineTension: 0.5
                }]
            };

            var $dashDrawingData = {
                labels: {!! json_encode(array_keys($last_10days_drawing)) !!},
                datasets: [{
                    label: '@lang('res.index.drawing_title')',
                    data: {!! json_encode(array_values($last_10days_drawing)) !!},
                    borderColor: '#358ed7',
                    backgroundColor: 'rgba(53, 142, 215, 0.175)',
                    borderWidth: 1,
                    fill: false,
                    lineTension: 0.5
                }]
            };

            var $verticalBarData = {
                labels: {!! json_encode(array_values($lose_win_30days['dataLabels'])) !!},
                datasets: [{
                        label: "Thắng",
                        data: {!! json_encode(array_values($lose_win_30days['dataWin'])) !!},
                        borderColor: '#33cabb',
                        backgroundColor: '#33cabb',
                        borderWidth: 1,
                    },
                    {
                        label: "Thua",
                        data: {!! json_encode(array_values($lose_win_30days['dataLoss'])) !!},
                        borderColor: '#f96868',
                        backgroundColor: '#f96868',
                        borderWidth: 1,
                    }
                ]
            };

            var $stackedBarData = {
                labels: {!! json_encode(array_values($dpst_wtda_rtr_30days['dataLabels'])) !!},
                datasets: [{
                        label: "Hoàn trả",
                        data: {!! json_encode(array_values($dpst_wtda_rtr_30days['dataTotal'])) !!},
                        borderColor: '#33cabb',
                        backgroundColor: '#33cabb',
                        borderWidth: 1,
                    },
                    {
                        label: "Nạp tiền",
                        data: {!! json_encode(array_values($dpst_wtda_rtr_30days['dataRecharge'])) !!},
                        borderColor: '#15c377',
                        backgroundColor: '#15c377',
                        borderWidth: 1,
                    },
                    {
                        label: "Rút tiền",
                        data: {!! json_encode(array_values($dpst_wtda_rtr_30days['dataDrawing'])) !!},
                        borderColor: '#f96868',
                        backgroundColor: '#f96868',
                        borderWidth: 1,
                    }
                ]
            };
            var $countryBarData = {
                labels: {!! json_encode($traffic_location['dataLabels']) !!},
                datasets: [{
                    label: '',
                    data: {!! json_encode($traffic_location['dataCountry']) !!},
                    borderColor: "#33cabb",
                    backgroundColor: "#33cabb",
                }]
            };

            var $devicePieData = {
                labels: {!! json_encode($traffic_device['dataLabels']) !!},
                datasets: [{
                    data: {!! json_encode($traffic_device['dataDevice']) !!},
                    borderColor: arrColors,
                    backgroundColor: arrColors,
                    borderWidth: 1,
                }, ]
            };

            /**
             *
             new Chart($dashChartBarsCnt, {
                type: 'bar',
                data: $dashChartBarsData
            });
             **/

            new Chart($dashChartLinesCnt, {
                type: 'line',
                data: $dashChartLinesData,
            });

            new Chart($dashChartDrawingLineCnt, {
                type: 'line',
                data: $dashDrawingData,
            });

            new Chart($dashChartVerticalBarCnt, {
                type: 'bar',
                data: $verticalBarData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 10,
                            bottom: 10
                        }
                    }
                }
            });

            new Chart($dashChartStackedBarCnt, {
                type: 'bar',
                data: $stackedBarData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true
                        }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 10,
                            bottom: 10
                        }
                    }
                },
            });

            new Chart($dashChartCountryBarCnt, {
                type: 'bar',
                data: $countryBarData,
                options: {
                    plugins: {
                        legend: {
                            display: false // Ẩn legend
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45,
                                display: true,
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            new Chart($dashChartDevicePieCnt, {
                type: 'pie',
                data: $devicePieData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 10,
                            bottom: 10
                        }
                    }
                },
            });
        });
    </script>
@endsection
