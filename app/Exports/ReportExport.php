<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Models\TransactionHistory;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithChunkReading
{
    use Exportable;
    protected $request;
    private $memberService;
    protected $memberId;
    public function __construct(Request $request, $memberId)
    {
        $this->request = $request;
        $this->memberService = app(MemberService::class);
        $this->memberId = $memberId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $params = $this->request->all();
        $params['member_id'] = $this->memberId;
        // $from = data_get($params, 'created_at_from');
        // $to = data_get($params, 'created_at_to');
        $from = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[0]))->format('Y-m-d H:i:s') : null;
        $to = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[1]))->format('Y-m-d H:i:s') : null;
        $productType = data_get($params, 'product_type');
        $perPage = data_get($params, 'per_page', apiPaginate());

        $transactionHistoryModel = TransactionHistory::with('member', 'apiGame')
            ->where('member_id', $this->memberId)
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST, TransactionHistory::STATUS_TIE])
            // filter by transaction_time
            ->when($from, function ($query) use ($from) {
                $query->where('transaction_time', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('transaction_time', '<=', $to);
            })

            // filter by product_type
            ->when($productType, function ($query) use ($productType) {
                $query->where('product_type', $productType);
            });

        $viewData = [
            'data' => $transactionHistoryModel->orderBy(TransactionHistory::getTableName() . '.id', 'desc')->get(),
            'params' => $this->request->all(),
        ];
        return $viewData['data'];
    }

    public function headings(): array
    {
        return [
            'Thời gian',
            'Trò chơi',
            'Tiền cược',
            'Thắng/thua',
            'Trạng thái',
            'Hoàn trả',
            'Số dư trước',
            'Số dư sau'
        ];
    }

    /**
     * @param $item
     * @return array
     */
    public function map($item): array
    {
        return [
            $item->transfer_code . ' - ' . $item->transaction_time,
            $item->getGameProviderText() . ' - ' . $item->getProductTypeText() . ' - ' . $item->getMemberPhoneAttribute(),
            $item->amount,
            $item->win_loss - $item->amount,
            $item->getStatusText(true),
            $item->getFsDetailByMoneyText($item->amount, true),
            moneyFormat($item->balance_before),
            moneyFormat($item->balance_after)
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $member = Member::where('id', $this->memberId)->first() ? Member::where('id', $this->memberId)->first()->name : null;
        return 'Thống kê tài khoản ' . $member;
    }

    public function chunkSize(): int
    {
        return 1000; // Chia nhỏ dữ liệu thành các phần 1000 dòng
    }
}
