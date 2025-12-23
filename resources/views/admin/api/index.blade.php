@extends('layouts.baseframe')

@section('title', $_title)

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>@lang('res.apis.index.top_title')</h4>
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
                <form action="{{ route('admin.systemconfig.checkagentsync') }}" method="post" id="searchForm" name="searchForm" class="form-horizontal">
                    <div class="row p-15">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <td width="5%">Agent Credit</td>
                                <td width="5%">@lang('res.common.operate')</td>
                            </tr>
                            </thead>
                            <tr>
                                <td>{{ $config['remote_check_agent'] ?? '' }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-operate="ajax-submit"><i class="mdi mdi-refresh"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>

                <form action="{{ route('admin.systemconfig.sync') }}" method="post" id="searchForm" name="searchForm" class="form-horizontal">
                    <div class="row p-15">
                        <div class="card">
                            <div class="card-header">
                                <h4>@lang('res.apis.index.old_api_title')</h4>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered table-hover text-left">
                                    <thead>
                                    <tr>
                                        <th width="550">@lang('res.apis.index.setting_name')</th>
                                        <th>@lang('res.apis.index.setting_value')</th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td>@lang('res.apis.index.api_domain')</td>
                                        <td>
                                            <input type="text" class="form-control" name="remote_api_domain-{{ \App\Models\Base::LANG_COMMON }}" placeholder="?:api.888.com" value="{{ $config['remote_api_domain'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.api_prefix')</td>
                                        <td>
                                            <input type="text" class="form-control" name="remote_api_prefix-{{ \App\Models\Base::LANG_COMMON }}" placeholder="?:9k" value="{{ $config['remote_api_prefix'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.api_id')</td>
                                        <td>
                                            <input type="text" class="form-control" name="remote_api_id-{{ \App\Models\Base::LANG_COMMON }}" placeholder="" value="{{ $config['remote_api_id'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.api_key')</td>
                                        <td>
                                            <input type="text" class="form-control" name="remote_api_key-{{ \App\Models\Base::LANG_COMMON }}" placeholder="" value="{{ $config['remote_api_key'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.sub_account')</td>
                                        <td>
                                            <input type="text" class="form-control" name="remote_api_sub_account-{{ \App\Models\Base::LANG_COMMON }}" placeholder="" value="{{ $config['remote_api_sub_account'] ?? '' }}" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>@lang('res.apis.index.sbo_api_title')</h4>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered table-hover text-left">
                                    <thead>
                                    <tr>
                                        <th width="550">@lang('res.apis.index.setting_name')</th>
                                        <th>@lang('res.apis.index.setting_value')</th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td colspan="2"><b>Cài đặt chung</b></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.company_key')</td>
                                        <td>
                                            <input type="text" class="form-control" name="company_key-{{ \App\Models\Base::LANG_COMMON }}" placeholder="" value="{{ $config['company_key'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.enable_sbo_log')</td>
                                        <td>
                                            @php $enable = (bool)data_get($config, 'enable_sbo_log') @endphp
                                            <label for="enable">
                                                Bật&nbsp;<input type="radio" class="form-control" id="enable" name="enable_sbo_log-{{ \App\Models\Base::LANG_COMMON }}" placeholder="" value="1" @if($enable) checked @endif/>
                                            </label>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <label for="disable">
                                                Tắt&nbsp;<input type="radio" class="form-control" name="enable_sbo_log-{{ \App\Models\Base::LANG_COMMON }}" placeholder="" value="0" @if(!$enable) checked @endif/>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.casino_table_limit')</td>
                                        <td>
                                            <input type="number" min="1" max="4" pattern="[0-9]" class="form-control"  oninput="validity.valid||(value='');" name="casino_table_limit-{{ \App\Models\Base::LANG_COMMON }}" value="{{ $config['casino_table_limit'] ?? '' }}" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"><b>Lệnh cược đại lý</b></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.agent_min_bet_setting')</td>
                                        <td>
                                            <input type="number" min="1" pattern="[0-9]" class="form-control"  oninput="validity.valid||(value='');" name="agent_min_bet_setting-{{ \App\Models\Base::LANG_COMMON }}" value="{{ $config['agent_min_bet_setting'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.agent_max_bet_setting')</td>
                                        <td>
                                            <input type="number" min="1" max="1000000" pattern="[0-9]" class="form-control"  oninput="validity.valid||(value='');" name="agent_max_bet_setting-{{ \App\Models\Base::LANG_COMMON }}" value="{{ $config['agent_max_bet_setting'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.agent_max_per_match')</td>
                                        <td>
                                            <input type="number" min="1" max="1000000" pattern="[0-9]" class="form-control"  oninput="validity.valid||(value='');" name="agent_max_per_match-{{ \App\Models\Base::LANG_COMMON }}" value="{{ $config['agent_max_per_match'] ?? '' }}" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"><b>Lệnh cược người chơi</b></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.user_min_bet_setting')</td>
                                        <td>
                                            <input type="number" min="1" pattern="[0-9]" class="form-control"  oninput="validity.valid||(value='');" name="user_min_bet_setting-{{ \App\Models\Base::LANG_COMMON }}" value="{{ $config['user_min_bet_setting'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.user_max_bet_setting')</td>
                                        <td>
                                            <input type="number" min="1" max="1000000" pattern="[0-9]" class="form-control"  oninput="validity.valid||(value='');" name="user_max_bet_setting-{{ \App\Models\Base::LANG_COMMON }}" value="{{ $config['user_max_bet_setting'] ?? '' }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('res.apis.index.user_max_per_match')</td>
                                        <td>
                                            <input type="number" min="1" max="1000000" pattern="[0-9]" class="form-control"  oninput="validity.valid||(value='');" name="user_max_per_match-{{ \App\Models\Base::LANG_COMMON }}" value="{{ $config['user_max_per_match'] ?? '' }}" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row p-15">
                        <button type="button" class="btn btn-primary" data-operate="ajax-submit">@lang('res.btn.save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section("footer-js")
    <script>
        //??????
        laydate.render({
            elem: '#created_at',
            type: 'datetime',
            theme: "#33cabb",
            range: "~"
        });

        $('.fresh-money').click(function(){
            var _this = $(this);
            _this.attr("disabled", true);

            if(!_this.data('url')) return;

            $.ajax({
                url: _this.data('url'),
                method:'get',
                success:function(res){
                    _this.attr("disabled", false);

                    if(res.code == 200 && res.data) _this.find('span').html(res.data)
                    else $.utils.layerError(res.message)
                },
                error:function(err){
                    _this.attr("disabled", false);

                }
            });
        })


    </script>
@endsection
