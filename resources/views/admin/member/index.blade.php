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
                                'name' => ['name' => trans('res.member.field.name'), 'type' => 'text'],
                                'phone' => ['name' => trans('res.member.field.phone'), 'type' => 'text'],
                                'lang' => [
                                    'name' => trans('res.member.field.lang'),
                                    'type' => 'select',
                                    'data' => config('platform.lang_select'),
                                ],
                                'status' => [
                                    'name' => trans('res.member.field.status'),
                                    'type' => 'select',
                                    'data' => trans('res.option.member_status'),
                                ],
                                'is_online' => [
                                    'name' => trans('res.member.field.is_online'),
                                    'type' => 'select',
                                    'data' => trans('res.option.is_online'),
                                ],

                                'checkbox' => [
                                    'name' => trans('res.member.field.is_payment'),
                                    'type' => 'select',
                                    'data' => trans('res.option.is_payment'),
                                ],
                                'created_at' => [
                                    'name' => trans('res.common.created_at'),
                                    'type' => 'predefined-date-range',
                                ],
                                'is_in_on' => [
                                    'name' => trans('res.member.field.is_in_on'),
                                    'type' => 'select',
                                    'data' => trans('res.option.boolean'),
                                ],
                            ],
                        ])

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
            <div class="card-toolbar clearfix">
                <div class="toolbar-btn-action">
                    <a class="btn btn-primary m-r-5" href="{{ route('admin.members.create') }}"><i class="mdi mdi-plus"></i>
                        @lang('res.btn.add')</a>
                    {{-- <a class="btn btn-success m-r-5" href="#!"><i class="mdi mdi-check"></i> ??</a>
                    <a class="btn btn-warning m-r-5" href="#!"><i class="mdi mdi-block-helper"></i> ??</a> --}}
                    <a class="btn btn-danger m-r-5" id="batchDelete" data-operate="delete" data-url="/admin/members/ids">
                        <i class="mdi mdi-window-close"></i> @lang('res.btn.batch_delete')
                    </a>


                </div>
            </div>

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
                                <th style="min-width: 250px;">@lang('res.member.field.name')</th>
                                <th>@lang('res.member.field.phone')</th>
                                @include('layouts._table_header', [
                                    'data' => \App\Models\Member::$list_field,
                                    'model' => 'member',
                                ])
                                {{-- <th>????</th> --}}
                                <th width="10%" style="min-width: 140px">@lang('res.member.index.is_agent_and_top_agent')</th>
                                <th style="min-width: 80px;">@lang('res.member.field.is_online')</th>
                                {{-- <th style="min-width: 160px">????IP/????</th> --}}
                                <th style="min-width: 160px">@lang('res.member.index.last_ip')</th>
                                <th>@lang('res.common.created_at')</th>
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
                                    <td>
                                        @include('layouts._member_dropmenus', ['member' => $item])
                                    </td>
                                    <td>
                                        {{$item->phone}}
                                    </td>
                                    @include('layouts._table_body', [
                                        'data' => \App\Models\Member::$list_field,
                                        'item' => $item,
                                    ])
                                    {{-- <td>{{ $item->updated_at }}</td> --}}
                                    <td>{{ trans('res.option.boolean')[$item->agent_id > 0 ? 1 : 0] }} /
                                        {{ $item->top->member->name ?? '-' }}</td>
                                    <td>
                                        @if (in_array($item->id, $online_list))
                                            <span style="color:green">{{ trans('res.option.is_online')[1] }}</span>
                                        @else
                                            <span style="color:red">{{ trans('res.option.is_online')[0] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->logs->first())
                                            {{ $item->logs->first()->ip }} / {{ $item->logs->first()->address }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="btn btn-xs btn-default"
                                                href="{{ route('admin.members.edit', ['member' => $item->id]) }}"
                                                title="" data-toggle="tooltip"
                                                data-original-title="@lang('res.btn.edit')"><i class="mdi mdi-pencil"></i></a>

                                            <a class="btn btn-xs btn-default" href="javascript:;" data-operate="iframe-page"
                                                data-toggle="tooltip" data-original-title="@lang('res.member.index.title_modify_money')"
                                                data-title="@lang('res.member.index.title_modify_money')"
                                                data-url="{{ route('admin.member.modify_money', ['member' => $item->id]) }}">
                                                <i class="mdi mdi-coin"></i>
                                            </a>

                                            {{-- ?????????,??????????????? || ??????,??????????? --}}
                                            @if (
                                                (!$item->isAgent() && app(\App\Services\AgentService::class)->isTraditionalMode() && !$item->top_id) ||
                                                    (!app(\App\Services\AgentService::class)->isTraditionalMode() && !$item->isAgent()))
                                                {{--
                                            <a class="btn btn-xs btn-default" data-url href="{{ route('admin.agents.assign',['member' => $item->id]) }}" data-toggle="tooltip" data-original-title="????">
                                                <i class="mdi mdi-account-check"></i>
                                            </a>
                                            --}}
                                                <a class="btn btn-xs btn-default" data-operate="iframe-page"
                                                    data-url="{{ route('admin.agents.assign', ['member' => $item->id]) }}"
                                                    data-toggle="tooltip" data-original-title="@lang('res.member.index.title_assign_agent')"
                                                    data-title="@lang('res.member.index.title_assign_member_agent', ['name' => $item->name])">
                                                    <i class="mdi mdi-arrow-collapse-up"></i>
                                                </a>
                                            @endif

                                            {{-- ?????????,???????????? || ??????,??????????? --}}
                                            @if (
                                                (app(\App\Services\AgentService::class)->isTraditionalMode() && !$item->isAgent()) ||
                                                    !app(\App\Services\AgentService::class)->isTraditionalMode())
                                                <a class="btn btn-xs btn-default" href="javascript:;"
                                                    data-operate="iframe-page" {{-- ?????? --}} data-toggle="tooltip"
                                                    data-original-title="@lang('res.member.index.title_modify_top')" data-title="@lang('res.member.index.title_modify_member_top', ['name' => $item->name])"
                                                    data-url="{{ route('admin.member.modify_top', ['member' => $item->id]) }}">
                                                    <i class="mdi mdi-arrow-collapse-up"></i>
                                                </a>
                                            @endif

                                            <a class="btn btn-xs btn-default" href="javascript:;" data-operate="delete"
                                                data-toggle="tooltip" data-original-title="@lang('res.btn.delete')"
                                                data-url="{{ route('admin.members.destroy', ['member' => $item->id]) }}">
                                                <i class="mdi mdi-window-close"></i>
                                            </a>
                                        </div>
                                    </td>
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
