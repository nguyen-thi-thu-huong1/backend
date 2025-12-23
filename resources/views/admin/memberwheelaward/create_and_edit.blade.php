@extends('layouts.baseframe')
@php
    $isUpdated = isset($model->id);
@endphp

@section('title', $_title)

@section('content')
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" onclick="javascript:window.history.go(-1);">
                            <i class="mdi mdi-skip-backward"></i> @lang('res.btn.back')
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">

                <form method="post" class="form-horizontal"
                      action="{{ $isUpdated?route('admin.memberwheelawards.update',['memberwheelaward' => $model->id]):route('admin.memberwheelawards.store') }}"
                      id="form">

                    @csrf

                    @if($isUpdated)
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $model->id }}">
                    @endif

                    <div class="form-group">
                        <label class="col-sm-2 control-label required">@lang('res.memberwheelaward.field.title')</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="title"
                                   value="{{ $isUpdated?$model->title:"" }}" @if(!$isUpdated) required @endif>
                        </div>
                    </div>

                    @foreach(config('platform.language_type') as $k => $v)
                        <div class="form-group">
                            <label class="col-sm-2 control-label">@lang('res.memberwheelaward.field.title')[{{$v}}]</label>
                            <div class="col-sm-4">
                                <input type="text" required class="form-control" name="lang_json[{{ $k }}]" placeholder="Vui lòng nhập tên trò chơi[{{$v}}]"
                                       value="{{ $isUpdated ? $model->getLangTitle($k):"" }}">
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group">
                        <label class="col-sm-2 control-label ">@lang('res.memberwheelaward.field.type')</label>
                        <div class="col-sm-4">
                            <select name="type" class="form-control js_select2">
                                <option value="">@lang('res.common.select_default')</option>
                                @foreach(trans('res.option.member_wheel_award_type') as $key =>$value)
                                    <option value="{{ $key }}" @if($isUpdated && $model->type == $key) selected
                                            @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="money">
                        <label class="col-sm-2 control-label required">@lang('res.memberwheelaward.field.money')</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" name="money"
                                   value="{{ $isUpdated?$model->money:"" }}" @if(!$isUpdated) required @endif>
                        </div>
                    </div>
                    <input type="hidden" name="redirect_url" value="{{ url()->previous() }}">

                    <div class="form-group">
                        <label class="col-sm-2 control-label ">@lang('res.memberwheelaward.field.web_pic')</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="web_pic"
                                   value="{{ $isUpdated?$model->web_pic:"" }}" @if(!$isUpdated) required @endif>
                        </div>
                        <div class="col-sm-4">
                            <span class="help-block">@lang('res.memberwheelaward.index.web_pic_notice')</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('res.common.upload_image')</label>
                        <div class="col-sm-8">
                            <ul class="list-inline clearfix lyear-uploads-pic" id="web_pic"
                                data-upload-url="{{ route('attachment.upload',['file_type' => 'pic','category' => 'memberwheelaward']) }}"
                                data-delete-url="{{ route('attachment.delete') }}" @if($isUpdated)
                                data-image-url="{{ $model->web_pic }}" @endif>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label ">@lang('res.memberwheelaward.field.mobile_pic')</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="mobile_pic"
                                   value="{{ $isUpdated?$model->mobile_pic:"" }}" @if(!$isUpdated) required @endif>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('res.common.upload_image')</label>
                        <div class="col-sm-8">
                            <ul class="list-inline clearfix lyear-uploads-pic" id="mobile_pic"
                                data-upload-url="{{ route('attachment.upload',['file_type' => 'pic','category' => 'memberwheelaward']) }}"
                                data-delete-url="{{ route('attachment.delete') }}" @if($isUpdated)
                                data-image-url="{{ $model->mobile_pic }}" @endif>
                            </ul>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label class="col-sm-2 control-label required">@lang('res.memberwheelaward.field.is_open')</label>
                        <div class="col-sm-4">
                            @foreach(config('platform.is_open') as $k => $v)
                                <label class="lyear-radio radio-inline radio-primary"><input type="radio" value="{{ $k }}"
                                                                                             name="is_open" @if($isUpdated && $model->is_open === $k) checked @endif >
                                    <span>{{ $v }}</span></label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label required">@lang('res.memberwheelaward.field.lang')</label>
                        <div class="col-sm-4">
                            <select name="lang" class="form-control js_select2">
                                <option value="">@lang('res.common.select_default')</option>
                                @foreach(config('platform.lang_fields') as $k => $v)
                                    <option value="{{ $k }}" @if($isUpdated && $model->lang == $k) selected
                                            @endif>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" data-operate="ajax-submit"
                                    type="button" data-select2="tags" >@lang('res.btn.save')</button>
                            <button class="btn btn-default" type="reset">@lang('res.btn.reset')</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection


@section('footer-js')
    <script>
        $(function () {
            initView();
            // 单图上传
            $('#web_pic').imageUpload({
                $callback_input: $('.form-control[name="web_pic"]'),
                showErrorDialog: $.utils.layerError,
                showSuccessDialog: $.utils.layerSuccess
            });

            // 单图上传
            $('#mobile_pic').imageUpload({
                $callback_input: $('.form-control[name="mobile_pic"]'),
                showErrorDialog: $.utils.layerError,
                showSuccessDialog: $.utils.layerSuccess
            });


            if($("[name=id]")){
                $("#tags").val({!! json_encode($model->tags_array ?? []) !!}).trigger('change');
            }

            function initView(){
                $('#money').hide().find('input[name]').attr("disabled", true);
                var typeSelect = $('[name=type]');
                var typeSelectValue = typeSelect.find("option:selected").attr("value");
                console.log('typeSelectValue', typeSelectValue);

                if(typeSelectValue == 1){
                    $('#money').hide().find('input[name]').attr("disabled", true);
                }else if(typeSelectValue == 2){
                    $('#money').show().find('input[name]').attr("disabled", false);
                }
            }

            $('[name=type]').change(function(){
                initView();
            });
        });

    </script>
@endsection
