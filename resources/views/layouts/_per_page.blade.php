<div class="row">
    <div class="col-sm-9">
        @if(isset($export_excel))
        <form action="{{ route('admin.'.$route, $id) }}">
            @csrf
            <input type="hidden" name="created_at" value="{{ $created_at }}">
            <button type="submit" class="btn"><span class="label label-primary" style="font-size: 16px; cursor: pointer">Xuất Excel</span></button>
        </form>
        @endif
        &nbsp;
    </div>
    <div class="col-sm-3">
        <div class="form-group row">
            <div class="col-sm-7">
                <label class="form-control col-form-label no-border">@lang('res.common.per_page')</label>
            </div>
            <div class="col-sm-5">
                <select class="form-control form-control-sm per_page" name="per_page">
                    @foreach (getConfig('custom_per_page') as $perPage => $perPageTitle)
                        <option value="{{ $perPage }}" {{ $perPage == request('per_page') ? 'selected' : '' }}>
                            {!! $perPageTitle !!}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
@section('footer-js')
    <script>
        $(document).ready(function() {
            $('.per_page').on('change', function() {
                var perPage = this.value;
                var currentUrl = document.URL;

                currentUrl += currentUrl.search(/\?/) !== -1 ? '&' : '?';
                currentUrl += 'per_page=' + perPage;

                window.location.href = currentUrl;
            });
        });
    </script>
@endsection
