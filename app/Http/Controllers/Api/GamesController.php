<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiGame;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GamesController extends MemberBaseController
{
    public function index(Request $request)
    {
        $games = DB::table('api_games')->select('api_games.*')
            ->join('apis', function ($join) {
                $join->on('apis.api_name', '=', 'api_games.api_name')
                    ->where('apis.is_open', 1);
            })
            ->whereIn('api_games.game_type', $request->get('game_type'))
            ->whereIn('api_games.client_type', [0, 2])
            ->where('api_games.is_open', 1)
            ->whereIn('api_games.lang', [ApiGame::LANG_COMMON, $request->get('lang')])
            ->orderBy('api_games.weight', 'desc')
            ->latest()
            ->get()
            ->transform(function ($item) use ($request) {
                $item->title = Str::contains($item->lang_json, $request->get('lang')) ? Arr::get(json_decode($item->lang_json, 1), $request->get('lang'), $item->title) : $item->title;
                return $item;
            });

        return $this->success(['data' => $games]);
    }

    public function categories()
    {
        $config = app(SystemConfig::class)
            ->where('name', request()->get('category_name', request('is_mobile') ? 'mobile_category_json' : 'web_category_json'))
            ->where('lang', ApiGame::LANG_COMMON)
            ->first();

        $categories = collect(json_decode($config->value, true))
            ->where('is_open', true)
            ->sortByDesc('weight');

        return $this->success(['data' => $categories]);
    }
}
