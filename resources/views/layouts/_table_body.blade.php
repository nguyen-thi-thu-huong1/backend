@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Str;
@endphp

@foreach ($data as $key => $value)
    @if(isset_and_not_empty($value,'is_show'))
        @switch(Arr::get($value, 'type', 'text'))
            @case('picture')
            <td>@include("layouts._table_image", ['url' => $item->$key])</td>
            @break

            @case('radio')
            @case('select')
            <td>
                @if(array_key_exists('style',$value))
                <span class="label {{ Arr::get(config($value['style']), $item->$key) }}">{{ Arr::get(trans('res.option')[substr($value['data'], strlen('platform.'))] ?? config($value['data']), $item->$key) }}</span>
                @else
                    @if(!is_null($item->$key))
                        @php $label = Arr::get(trans('res.option')[substr($value['data'], strlen('platform.'))] ?? config($value['data']), $item->$key, ''); @endphp
                    @else
                        @php $label = ''; @endphp
                    @endif

                    @php $labelStyle = config('platform.style_label')[current(array_keys(array_keys(config($value['data'])), $item->$key))%13] @endphp

                    @if(data_get($value, 'label_style'))
                        @php $labelStyle = data_get($value, 'label_style.' . $item->$key) @endphp
                    @endif
                <span class="label {{ $labelStyle }}">{{ $label }}</span>
                @endif
            </td>
            @break

            @case('money')
            <td>
                @if(data_get($value, 'class'))
                    <h4 class="{{ data_get($value, 'class') }}">{{ number_format($item->$key) }}</h4>
                @else
                    <span>{{ number_format($item->$key) }}</span>
                @endif
            </td>
            @break

            @case('function')
            @php $function = Arr::get($value, 'data'); @endphp
            <td>{!! $item->$function() !!}</td>
            @break

            @default
            @switch($key)
                @case('member_id')
                    @if(Str::startsWith(\Route::currentRouteName(), 'admin.'))
                    <td title="{{ $item->member->name ?? '' }}">@include("layouts._member_dropmenus", ['member' => $item->member])</td>
                    @else
                    <td title="{{ $item->member->name ?? '' }}">{{ $item->member->name ?? '已删除' }}</td>
                    @endif
                @break

                @case('user_id')
                <td title="{{ $item->user->name ?? '-' }}">{{ string_limit($item->user->name ?? '-', 20) }}</td>
                @break

                @case('agent_id')
                <td title="{{ $item->member->name ?? '' }}">{{ string_limit($item->member->name ?? '已删除', 20) }}</td>
                @break

                @case('register_ip')
                <td title="{{ $item->register_ip ?? '' }}" style="cursor: pointer">
                    <script>
                        function createTabIp{{$item->id}}(){
                            parent.$(parent.document).data('multitabs').create({
                                iframe : true,
                                title : "Kiểm tra và truy vấn thành viên",
                                url : "{{ route('admin.quick.member_arbitrage_query', ['member_id' => $item->id, 'type' => 'ip']) }}"
                            }, true);
                        }
                    </script>
                    <div onclick="createTabIp{{$item->id}}()">
                        {{ $item->register_ip ?? '' }}
                    </div>
                </td>
                @break

                @default
                <td title="{{ $item->$key }}">{{ string_limit($item->$key, Str::contains($key, 'url') ? 50 : 20) }}</td>
                @endswitch
        @endswitch
    @endif
@endforeach
