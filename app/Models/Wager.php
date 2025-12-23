<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wager extends Model
{
    public const WAGER_ID = 'wager_id';
    public const MEMBER_NAME =         'member_name';
    public const PRODUCT_ID =         'product_id';
    public const GAME_TYPE =  'game_type';
    public const CURRENCY_ID =  'currency_id';
    public const GAME_ID =  'game_id';
    public const GAME_ROUND_ID =  'game_round_id';
    public const VALID_BET_AMOUNT =  'valid_bet_amount';
    public const BET_AMOUNT =  'bet_amount';
    public const JP_BET =  'jp_bet';
    public const PAYOUT_AMOUNT =  'payout_amount';
    public const COMMISION_AMOUNT =  'commision_amount';
    public const JACKPOT_AMOUNT =  'jackpot_amount';
    public const SETTLEMENT_DATE =  'settlement_date';
    public const STATUS = 'status';
    protected $fillable = [
        self::MEMBER_NAME,
        self::PRODUCT_ID,
        self::GAME_TYPE,
        self::CURRENCY_ID,
        self::GAME_ID,
        self::GAME_ROUND_ID,
        self::VALID_BET_AMOUNT,
        self::BET_AMOUNT,
        self::JP_BET,
        self::PAYOUT_AMOUNT,
        self::COMMISION_AMOUNT,
        self::JACKPOT_AMOUNT,
        self::SETTLEMENT_DATE,
        self::STATUS,
    ];

    public function gameName()
    {
        return $this->hasOne(GameList::class, 'game_code', 'product_id');
    }   
}