<tbody>
    @foreach ($data as $item)
        <tr>
            <td>
                @include("layouts._member_dropmenus",['member' => $item])
            </td>
            <td>{{ $item->realname }}</td>
            <td>{{ trans('res.option.boolean')[$item->agent_id > 0 ? 1 : 0] }} / {{ $item->top->member->name ?? '-' }}</td>
            <td>{{ $item->rechargeCount }}</td>
            <td>{{ $item->drawingCount }}</td>
            <td>{{ $item->rechargeSum ?? 0 }}</td>
            <td>{{ $item->drawingSum ?? 0 }}</td>
            <td>{{ $item->moneylogSumFanshui ?? 0 }}</td>
            <td>{{ $item->moneylogSumHongli ?? 0 }}</td>
            <td>
                @if ($item->moneylogSumOther - $item->moneylogSumDebit >= 0)
                    <div class="text-dark">
                        {{ $item->moneylogSumOther - $item->moneylogSumDebit }}
                    </div>
                @else
                    <div class="text-danger">
                        {{ $item->moneylogSumOther - $item->moneylogSumDebit }}
                    </div>
                @endif
            </td>
            <td>
                @if ($item->rechargeSum - $item->drawingSum > 0)
                    <div class="text-success">
                        {{ $item->rechargeSum - $item->drawingSum }}
                    </div>
                @else
                    <div class="text-danger">
                        {{ $item->rechargeSum - $item->drawingSum }}
                    </div>
                @endif
            </td>
            <td>
                {!! isset($histories[$item->id]) && isset($histories[$item->id][0]) ? $item->getWinLossDiff($histories[$item->id][0]['total_amount_win'], $histories[$item->id][0]['total_amount_lost']) : 0 !!}
            </td>
        </tr>
    @endforeach
</tbody>
<tfoot>
<tr style="color: #D57D11">
    <td colspan="6"></td>
    <td colspan="2"><strong>@lang('res.member.money_report.profit_formula_notice')</strong></td>
    <td colspan="3"><strong >@lang('res.member.money_report.profit_formula')</strong></td>
</tr>
</tfoot>
<tfoot>
    <tr>
        <td><strong style="color: red">@lang('res.common.sum')</strong></td>
        <td colspan="4"></td>
        <td><strong @if ($total_recharges > 0)
            style="color: green"
        @else
            style="color: red"
        @endif>{{ $total_recharges }}</strong></td>
        <td><strong @if ($total_drawings > 0)
            style="color: green"
        @else
            style="color: red"
        @endif>{{ $total_drawings }}</strong></td>
        <td><strong @if ($total_fs > 0)
            style="color: green"
        @else
            style="color: red"
        @endif>{{ $total_fs }}</strong></td>
        <td><strong @if ($total_dividend > 0)
            style="color: green"
        @else
            style="color: red"
        @endif>{{ $total_dividend }}</strong></td>
        <td><strong @if ($total_other > 0)
            style="color: green"
        @else
            style="color: red"
        @endif>{{ $total_other }}</strong></td>
        <td>
            @if($total_yinli > 0)
                <span class="text-dark">@lang('res.member.money_report.yinli')</span><strong class="text-success">{{ $total_yinli }}</strong>
            @else
                <span class="text-dark">@lang('res.member.money_report.kuisun')</span><strong class="text-danger">{{ $total_yinli }}</strong>
            @endif
        </td>
        <td>
            @if (isset($total_sum_win) && isset($total_sum_lost))
            <strong @if ($total_sum_win - $total_sum_lost > 0)
                style="color: green"
            @else
                style="color: red"
            @endif>{{ $total_sum_win - $total_sum_lost }}</strong>
            @else
                0
            @endif
        </td>
        <td>
            <strong style="color: red">{{ $total_sum_win - $total_sum_lost }}</strong>
        </td>
    </tr>
</tfoot>
