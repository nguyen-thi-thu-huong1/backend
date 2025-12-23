@extends('layouts.baseframe')

@section('title', $_title)

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

            <div class="card-toolbar clearfix">
                <div class="toolbar-btn-action">
                    <a class="btn btn-primary m-r-5" href="{{ route("admin.apis.create") }}"><i class="mdi mdi-plus"></i>@lang('res.btn.add')</a>
                    <a class="btn btn-danger" id="batchDelete" data-operate="delete" data-url="/admin/apis/ids"><i class="mdi mdi-window-close"></i> @lang('res.btn.delete')</a>
                </div>
            </div>

            <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>
                                <label class="lyear-checkbox checkbox-primary"><input type="checkbox" id="check-all"><span></span></label>
                            </th>
                            @include('layouts._table_header',['data' => \App\Models\Api::$list_field,'model' => 'apis'])
                            <th>@lang('res.apis.field.lang_list')</th>
                            <th>@lang('res.apis.field.api_money')</th>
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
                                @include('layouts._table_body',['data' => \App\Models\Api::$list_field,'item' => $item])
                                <td>
                                    @if($item->lang_list)
                                        @foreach (json_decode($item->lang_list,1) as $k => $v)
                                            <span class="label {{ config('platform.style_label')[$k+1] }}">{{ config('platform.lang_select')[$v] }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if(\App\Models\Api::isCommonWallet() && !in_array($item->api_name,[\App\Models\Api::COMMON_WALLET_API,\App\Models\Api::LY_LOTTERY]))
                                        <a href="javascript:;" class="btn btn-danger btn-xs">
                                            <span>{{ $item->api_money }}</span>
                                        </a>
                                    @else
                                        <a href="javascript:;" class="btn btn-danger btn-xs fresh-money"
                                           data-url="{{ route('admin.api.refresh',['api_code' => $item->api_name]) }}"
                                           data-toggle="tooltip" data-original-title="@lang('res.apis.index.btn_refresh')">
                                            <i class="mdi mdi-refresh"></i>
                                            <span>{{ $item->api_money }}</span>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-xs btn-default"
                                           href="{{ route('admin.apis.edit',['api' => $item->id,'is_super' => request('is_super')]) }}" title=""
                                           data-toggle="tooltip" data-original-title="@lang('res.btn.edit')"><i
                                                    class="mdi mdi-pencil"></i></a>

                                        <a class="btn btn-xs btn-default" href="javascript:;" data-operate="delete"
                                           data-toggle="tooltip" data-original-title="@lang('res.btn.delete')"
                                           data-url="{{ route('admin.apis.destroy', ['api' => $item->id]) }}">
                                            <i class="mdi mdi-window-close"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="clearfix">
                    <div class="pull-left">
                        <p>@lang('res.common.total') <strong style="color: red">{{ count($data) }}</strong> @lang('res.common.count')</p>
                    </div>
                </div>
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
