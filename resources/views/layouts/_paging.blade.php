@if($data)
    @php
        $totalRecord  = $data->total();
        $currentPage = $data->currentPage();
        $perPage = $data->perPage();
        $fromRecord = (int)($currentPage - 1) * $perPage + 1;
        if ($fromRecord > $totalRecord) $fromRecord = $totalRecord;
        $toRecord = (($currentPage * $perPage) - $totalRecord) > 0 ? $totalRecord : ($currentPage * $perPage);
    @endphp
    <div class="clearfix">
        <div class="pull-left">
            <p>@lang('res.common.total') <strong style="color: red">{{ number_format($data->total()) }}</strong> @lang('res.common.count'), @lang('res.common.display') <strong style="color: red">{{ number_format($fromRecord) }}</strong> ~ <strong style="color: red">{{ number_format($toRecord) }}</strong></p>
        </div>
        <div class="pull-right">
            {!! $data->appends(request()->all())->render() !!}
        </div>
    </div>
@endif
@section('css')
    <style>
        .pagination {
            margin: 0;
        }
    </style>
@endsection
