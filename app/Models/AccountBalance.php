<?php

namespace App\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    protected $fillable = [
        'member_id',
        'date',
        'balance_start_day',
        'balance_middle_day',
        'bet_amount',
        'canceled_amount',
        'pending_amount',
        'win_loss',
        'commission'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
