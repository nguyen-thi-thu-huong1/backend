@extends('layouts.baseframe')

@section('title', $_title)

@section('css')
    <link rel="stylesheet" href="{{asset('/js/bootstrap-table/bootstrap-table.min.css')}}">
    <link href="{{asset('/js/bootstrap-table/jquery.treegrid.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" onclick="javascript:window.location.reload()">
                            <i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-toolbar clearfix">
                <div class="toolbar-btn-action">
                    <a class="btn btn-primary m-r-5" href="{{ route("admin.permissions.create") }}"><i class="mdi mdi-plus"></i> @lang('res.btn.add')</a>
                    <a class="btn btn-danger" id="batchDelete" data-operate="delete" data-url="/admin/permissions/ids"><i class="mdi mdi-window-close"></i> @lang('res.btn.delete')</a>
                </div>
            </div>

            <div class="card-body">
                @include('layouts._per_page')

                @include('layouts._paging')

                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead>
                        <tr>
                            <th>
                                <label class="lyear-checkbox checkbox-primary"><input type="checkbox" id="check-all"><span></span></label>
                            </th>
                            <th>@lang('res.permission.field.name')</th>
                            <th>@lang('res.permission.field.icon')</th>
                            <th>@lang('res.permission.field.pid')</th>
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
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->icon }}</td>
                                <td>{{ $item->pid }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-xs btn-default"
                                           href="{{ route('admin.permissions.edit', $item->id) }}" title=""
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

                @include('layouts._paging')
            </div>
        </div>
    </div>
@endsection

@section("footer-js")

    <script src="{{ public_url('/js/bootstrap-table/bootstrap-table.min.js?v=1.12.1')}}"></script>
    <script src="{{ public_url('/js/bootstrap-table/bootstrap-table-treegrid.js?v=1.0')}}"></script>
    <script src="{{ public_url('/js/bootstrap-table/jquery.treegrid.min.js')}}"></script>


    <script>
        var $table = $('.table');

        $(function () {
            var data = JSON.parse('{!! $data !!}');
            $table.bootstrapTable({
                data: data,
                idField: 'id',
                dataType: 'jsonp',
                columns: [{
                    field: 'id',
                    title: 'ID'
                },
                    {
                        field: 'name',
                        title: '{{ trans("res.permission.field.name") }}'
                    },
                    {
                        field: 'route_name',
                        title: '{{ trans("res.permission.field.route_name") }}'
                    },
                    {
                        field: 'icon',
                        title: '图标',
                        align: 'center',
                        formatter: function (value, row, index) {
                            return '<i class="' + value + '"></i>';
                        }
                    },
                    {
                        field: 'is_show',
                        title: '{{ trans("res.permission.field.is_show") }}',
                        sortable: false,
                        align: 'center',
                        formatter: 'statusFormatter'
                    },
                    {
                        field: 'weight',
                        title: '{{ trans("res.permission.field.weight") }}',
                        align: 'center'
                    },
                    {
                        field: 'created_at',
                        title: '{{ trans("res.common.created_at") }}',
                        align: 'center'
                    },
                    {
                        field: 'operate',
                        title: '{{ trans("res.common.operate") }}',
                        align: 'center',
                        formatter: 'operateFormatter'
                    },
                ],
                treeShowField: 'name',
                parentIdField: 'pid',
                onResetView: function (data) {
                    $table.treegrid({
                        initialState: 'collapsed', // 所有节点都折叠
                        initialState: 'expanded', // 所有节点都展开，默认展开
                        expanderExpandedClass: 'mdi mdi-folder-open', // 可自定义图标样式
                        expanderCollapsedClass: 'mdi mdi-folder',
                        treeColumn: 1,
                        onChange: function () {
                            $table.bootstrapTable('resetWidth');
                        },

                    });
                    $table.treegrid('getRootNodes').treegrid('expand');
                },
                onCheck: function (row) {
                    var datas = $table.bootstrapTable('getData');
                    selectChilds(datas, row, "id", "pid", true);
                    selectParentChecked(datas, row, "id", "pid")
                    $table.bootstrapTable('load', datas);
                },

                onUncheck: function (row) {
                    var datas = $table.bootstrapTable('getData');
                    selectChilds(datas, row, "id", "pid", false);
                    $table.bootstrapTable('load', datas);
                },

                // 表格渲染完成后，手动渲染bootstrap控件
                onPostBody:function(row){
                    $("[data-toggle='tooltip']").tooltip();
                }
            });
        });



        // 格式化按钮
        function operateFormatter(value, row, index) {
            return [
                '<a class="btn btn-xs btn-default" href="/admin/permissions/create/' + row.id +
                '" data-toggle="tooltip" data-original-title="{{ trans('res.permission.index.btn_child') }}"><i class="mdi mdi-plus"></i></a>',
                '<a class="btn btn-xs btn-default" href="/admin/permissions/' + row.id +
                '/edit" title="" data-toggle="tooltip" data-original-title="{{ trans('res.btn.edit') }}"><i class="mdi mdi-pencil"></i></a>',
                '<a class="btn btn-xs btn-default" href="javascript:;" data-operate="show-page" data-toggle="tooltip" data-original-title="{{ trans('res.btn.detail') }}" data-url="/admin/permissions/'+
                row.id+'"><i class="mdi mdi-file-document-box"></i></a>',
                '<a class="btn btn-xs btn-default" href="javascript:;" data-operate="delete" data-toggle="tooltip" data-original-title="{{ trans('res.btn.delete') }}" data-url="/admin/permissions/' +
                row.id + '"> <i class="mdi mdi-window-close"></i></a>'
            ].join('');
        }

        // 格式化状态
        function statusFormatter(value, row, index) {
            if (value === 1) {
                return '<span title="{{ trans('res.permission.is_show.1') }}" class="label label-success">{{ trans('res.permission.is_show.1') }}</span>';
            } else if (value === 0) {
                return '<span title="{{ trans('res.permission.is_show.0') }}" class="label label-danger">{{ trans('res.permission.is_show.0') }}</span>';
            }
        }

    </script>
@endsection
