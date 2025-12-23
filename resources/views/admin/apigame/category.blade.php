@extends('layouts.baseframe')

@section('title', $_title)

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>@lang('res.api_game.category.web_category_title')</h4>

                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#webContent" aria-expanded="false" aria-controls="searchContent">
                            <i class="mdi mdi-chevron-double-down"></i> @lang('res.btn.collapse')
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="window.location.reload()">
                            <i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body collapse in" id="webContent" aria-expanded="true">
                <form action="{{ route('admin.apigames.web_category_save') }}" method="post" id="webForm" name="webForm" class="form-horizontal">
                    <div class="card-toolbar clearfix">
                        <div class="toolbar-btn-action">
                            <a id="add-btn" class="btn btn-label btn-primary m-r-5" href="javascript:void(0);"><label><i class="mdi mdi-plus"></i></label>&nbsp;@lang('res.btn.add')</a>
                            <a class="btn btn-label btn-info" data-operate="ajax-submit"><label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label>&nbsp;@lang('res.btn.save')</a>
                        </div>
                    </div>

                    <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                        <div class="row p-15">
                            <table id="table-icon" class="table table-bordered table-hover text-center">
                                <thead>
                                <tr>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.game_category_name') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.game_category_type') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.icon_before_category') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.image_preview') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.icon_after_category') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.image_preview') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.weight') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.status') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.common.operate') }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($web as $item)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" name="title[]" placeholder="Vui lòng nhập tên danh mục" value="{{ $item['title'] ?? '' }}" />
                                        </td>
                                        <td>
                                            <select name="game_type[]" class="form-control js_select2" data-value="{{ $item['game_type'] ?? '' }}"></select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control icon-url-before" name="icon_before[]"
                                                   placeholder="Vui lòng nhập địa chỉ của biểu tượng trước khi nhấp vào" value="{{ $item['icon_before'] ?? '' }}" />
                                        </td>
                                        <td>
                                            <img src="{{ $item['icon_before'] ?? ''}}" data-src="{{ $item['icon_before'] ?? ''}}"
                                                 data-operate="show-image" style="max-width:60px;cursor: pointer;">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control icon-url-after" name="icon_after[]"
                                                   placeholder="Vui lòng nhập địa chỉ biểu tượng đã nhấp" value="{{ $item['icon_after'] ?? '' }}" />
                                        </td>
                                        <td>
                                            <img src="{{ $item['icon_after'] ?? ''}}" data-src="{{ $item['icon_after'] ?? ''}}"
                                                 data-operate="show-image" style="max-width:60px;cursor: pointer;">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="weight[]" placeholder="Vui lòng nhập trọng lượng"
                                                   value="{{ $item['weight'] ?? '' }}" />
                                        </td>
                                        <td>

                                            <label class="lyear-switch switch-solid switch-primary switch-col">
                                                <input type="checkbox" name="is_open[]" value="{{ $item['is_open'] }}" data-true="true" data-false="false" @if($item['is_open']) checked @endif>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="delete-btn btn btn-danger btn-xs">Xóa</a>

                                            <a href="javascript:void(0);" data-operate="show-uploader"
                                               data-url="/admin/picture/upload"
                                               class="btn btn-warning btn-xs btn-uploader-before">
                                                {{ __('res.api_game.category.icon_before_upload') }}
                                            </a>

                                            <a href="javascript:void(0);" data-operate="show-uploader"
                                               data-url="/admin/picture/upload"
                                               class="btn btn-warning btn-xs btn-uploader-after">
                                                {{ __('res.api_game.category.icon_after_upload') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>@lang('res.api_game.category.mobile_category_title')</h4>

                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#mobileContent" aria-expanded="false"
                                aria-controls="searchContent">
                            <i class="mdi mdi-chevron-double-down"></i> @lang('res.btn.collapse')
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="window.location.reload()">
                            <i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body collapse in" id="mobileContent" aria-expanded="true">
                <form action="{{ route('admin.apigames.mobile_category_save') }}" method="post" id="searchForm" name="searchForm" class="form-horizontal">
                    <div class="card-toolbar clearfix">
                        <div class="toolbar-btn-action">
                            <a id="add-btn" class="btn btn-label btn-primary m-r-5" href="javascript:void(0);"><label><i class="mdi mdi-plus"></i></label>&nbsp;@lang('res.btn.add')</a>
                            <a class="btn btn-label btn-info" data-operate="ajax-submit"><label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label>&nbsp;@lang('res.btn.save')</a>
                        </div>
                    </div>

                    <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                        <div class="row p-15">
                            <table id="table-icon" class="table table-bordered table-hover text-center">
                                <thead>
                                <tr>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.game_category_name') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.game_category_type') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.icon_before_category') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.image_preview') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.icon_after_category') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.image_preview') }}</th>
                                    <th class="text-center" width="15%">{{ __('res.api_game.category.weight') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.api_game.category.status') }}</th>
                                    <th class="text-center" width="10%">{{ __('res.common.operate') }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($data as $item)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" name="title[]" placeholder="Vui lòng nhập tên danh mục" value="{{ $item['title'] ?? '' }}" />
                                        </td>
                                        <td>
                                            <select name="game_type[]" class="form-control js_select2" data-value="{{ $item['game_type'] ?? '' }}"></select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control icon-url-before" name="icon_before[]"
                                                   placeholder="Vui lòng nhập địa chỉ của biểu tượng trước khi nhấp vào" value="{{ $item['icon_before'] ?? '' }}" />
                                        </td>
                                        <td>
                                            <img src="{{ $item['icon_before'] ?? ''}}" data-src="{{ $item['icon_before'] ?? ''}}"
                                                 data-operate="show-image" style="max-width:60px;cursor: pointer;">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control icon-url-after" name="icon_after[]"
                                                   placeholder="Vui lòng nhập địa chỉ biểu tượng đã nhấp" value="{{ $item['icon_after'] ?? '' }}" />
                                        </td>
                                        <td>
                                            <img src="{{ $item['icon_after'] ?? ''}}" data-src="{{ $item['icon_after'] ?? ''}}"
                                                 data-operate="show-image" style="max-width:60px;cursor: pointer;">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="weight[]" placeholder="Vui lòng nhập trọng lượng"
                                                   value="{{ $item['weight'] ?? '' }}" />
                                        </td>
                                        <td>

                                            <label class="lyear-switch switch-solid switch-primary switch-col">
                                                <input type="checkbox" name="is_open[]" value="{{ $item['is_open'] }}" data-true="true" data-false="false" @if($item['is_open']) checked @endif>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="delete-btn btn btn-danger btn-xs">Xóa</a>

                                            <a href="javascript:void(0);" data-operate="show-uploader"
                                               data-url="/admin/picture/upload"
                                               class="btn btn-warning btn-xs btn-uploader-before">
                                                {{ __('res.api_game.category.icon_before_upload') }}
                                            </a>

                                            <a href="javascript:void(0);" data-operate="show-uploader"
                                               data-url="/admin/picture/upload"
                                               class="btn btn-warning btn-xs btn-uploader-after">
                                                {{ __('res.api_game.category.icon_after_upload') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @php
        $config = [];
        foreach(trans('res.option.game_type') as $k => $v){
            array_push($config, ['id' => $k,'text' => $v]);
        }
    @endphp
@endsection

@section("footer-js")
    <script>
        $(function () {
            $(document).on('click', '.delete-btn', function () {
                $(this).parents('tr').remove();
            });

            $(document).on('click', '.btn-uploader-before', function () {
                $('.icon-url-before').removeAttr('id');
                $(this).parents('tr').find('.icon-url-before').attr('id', 'picture-url');
            });

            $(document).on('click', '.btn-uploader-after', function () {
                $('.icon-url-after').removeAttr('id');
                $(this).parents('tr').find('.icon-url-after').attr('id', 'picture-url');
            });

            initSelect();

            // add new web category
            $('#web-add-btn').click(function () {
                var tbody = $('#table').find('tbody');
                tbody.append(
                    '<tr><td><input type="text" class="form-control" name="title[]" placeholder="Tên danh mục" value="" /></td>' +
                    '<td><select name="game_type[]" class="form-control js_select2"></select></td>' +
                    '<td><input type="text" class="form-control icon-url-before" name="icon_before[]" placeholder="Biểu tượng phía trước" value=""></td>' +
                    '<td> - </td>' +
                    '<td><input type="text" class="form-control icon-url-after" name="icon_after[]" placeholder="Biểu tượng phía sau" value=""></td>' +
                    '<td> - </td>' +
                    '<td><input type="text" class="form-control" name="weight[]" placeholder="Thứ tự" value="" /></td>' +
                    '<td><label class="lyear-switch switch-solid switch-primary"><input type="checkbox" name="is_open[]" value="0"><input type="hidden" value="0"><span></span></label></td>' +
                    '<td><a href="javascript:void(0);" class="delete-btn btn btn-danger btn-xs">Xóa</a>' +
                    '<a href="javascript:void(0);" class="btn btn-warning btn-xs btn-uploader" data-title="Tải lên biểu tượng (phía trước)" data-operate="show-uploader" data-url="/admin/picture/upload" >Tải lên biểu tượng (phía trước)</a>' +
                    '</td></tr>')

                initSelect();

            });

            // add new mobile category
            $('#add-btn').click(function () {
                var tbody = $('#table-icon').find('tbody');
                tbody.append(
                    '<tr><td><input type="text" class="form-control" name="title[]" placeholder="Tên danh mục" value="" /></td>' +
                    '<td><select name="game_type[]" class="form-control js_select2"></select></td>' +
                    '<td><input type="text" class="form-control icon-url-before" name="icon_before[]" placeholder="Biểu tượng phía trước" value=""></td>' +
                    '<td> - </td>' +
                    '<td><input type="text" class="form-control icon-url-after" name="icon_after[]" placeholder="Biểu tượng phía sau" value=""></td>' +
                    '<td> - </td>' +
                    '<td><input type="text" class="form-control" name="weight[]" placeholder="Thứ tự" value="" /></td>' +
                    '<td><label class="lyear-switch switch-solid switch-primary"><input type="checkbox" name="is_open[]" value="0"><input type="hidden" value="0"><span></span></label></td>' +
                    '<td><a href="javascript:void(0);" class="delete-btn btn btn-danger btn-xs">Xóa</a>' +
                    '<a href="javascript:void(0);" class="btn btn-warning btn-xs btn-uploader" data-title="Tải lên biểu tượng (phía trước)" data-operate="show-uploader" data-url="/admin/picture/upload" >Tải lên biểu tượng (phía trước)</a>' +
                    '</td></tr>');

                initSelect();

            });

            $('.lyear-checkbox input[type=checkbox]').change(function () {
                $(this).siblings('input[type=hidden]').val($(this).is(":checked"));
            });

            function initSelect() {
                var data = {!! json_encode($config) !!};

                data.unshift({id: '-1', text: 'Vui lòng chọn'});

                // 渲染select
                $('[name="game_type[]"]').select2({
                    data: data
                }).each(function (index, ele) {
                    var value = $(this).data('value');

                    if (!value) return;

                    $(this).val([value]).trigger('change');

                });
            }
        });
    </script>
@endsection
