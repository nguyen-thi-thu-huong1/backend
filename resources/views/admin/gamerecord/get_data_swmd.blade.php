@extends('layouts.baseframe')

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <tbody>
                        @if (!empty($viewData))
                            @foreach ($viewData as $key => $data)
                                <tr>
                                    <th>{!! $key !!}</th>
                                    <td>{!! $data !!}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
