@extends('layouts.baseframe')
@php
    $title = "Tải ảnh lên";
@endphp
@section('title', $title ?? '')

@section('content')
    <div class="col-sm-12 p-t-15">

        <div class="card">
            <div class="card-header">
                <h4>{{ $title }}</h4>
            </div>
            <div class="card-body">

                <input type="hidden" id="iframe_id" value="">
                <input type='hidden' id="url_field" value="">

                <form method="post" class="form-horizontal" id="form">

                    <div class="form-group">
                        <label class="col-sm-2 control-label ">Ảnh logo</label>
                        <div class="col-sm-4">
                            <input type="text" name="uploadurl" class="form-control" placeholder="Đường dẫn ảnh" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tải ảnh lên</label>
                        <div class="col-sm-8">
                            <ul class="list-inline clearfix lyear-uploads-pic" id="web_pic"
                                data-upload-url="{{ route('attachment.upload',['file_type' => 'pic','category' => 'bank']) }}"
                                data-delete-url="{{ route('attachment.delete') }}">
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="button" id="btn-yes">OK</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection


@section('footer-js')
    <script>
        $("#btn-yes").on("click",function () {
            // 获取页面的元素值
            //var str = $(this).find("span").text();
            var url = $('input[name=uploadurl]').val();

            // 获取iframe id
            var iframe_id = $("#iframe_id").val();
            var sibling = $.utils.getSiblingFrame(iframe_id);

            // 赋值并移除id
            sibling.$('#picture-url').val(url).removeAttr('id');
            $.utils.closeIframeLayer();
        });

        // 单图上传
        $('#web_pic').imageUpload({
            $callback_input: $('.form-control[name="uploadurl"]'),
            showErrorDialog: $.utils.layerError,
            showSuccessDialog: $.utils.layerSuccess
        });

    </script>
@endsection
