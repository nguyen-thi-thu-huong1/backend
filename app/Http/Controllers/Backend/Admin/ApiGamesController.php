<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Api;
use App\Models\ApiGame;
use App\Models\Base;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiGamesController extends AdminBaseController
{
    protected $create_field = ['title', 'subtitle', 'web_pic', 'mobile_pic', 'api_name', 'class_name', 'game_type', 'params', 'is_open', 'weight', 'tags', 'remark', 'client_type', 'lang_json', 'logo_url', 'lang'];
    protected $update_field = ['title', 'subtitle', 'web_pic', 'mobile_pic', 'api_name', 'class_name', 'game_type', 'params', 'is_open', 'weight', 'tags', 'remark', 'client_type', 'lang_json', 'logo_url', 'lang'];

    public function __construct(ApiGame $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $params = $request->except('tags');
        $data = $this->model->whereTags($request->get('tags', []))->where($this->convertWhere($params))->latest()->paginate(request('per_page', apiPaginate()));
        $params = $request->all();
        return view("{$this->view_folder}.index", compact('data', 'params'));
    }

    public function edit(ApiGame $apigame)
    {
        return view($this->getEditViewName(), ["model" => $apigame]);
    }

    public function storeRule()
    {
        return $this->validRule();
    }

    public function updateRule($id)
    {
        return $this->validRule();
    }

    public function storeHandle($data)
    {
        if (array_key_exists('tags', $data)) $data['tags'] = implode(',', $data['tags']);
        else $data['tags'] = "";

        $data['api_name'] = strtoupper($data['api_name']);

        $data = $this->setLanguage($data);
        return $data;
    }

    public function updateHandle($data)
    {
        if (array_key_exists('tags', $data)) $data['tags'] = implode(',', $data['tags']);
        else $data['tags'] = "";

        $data['api_name'] = strtoupper($data['api_name']);
        $data = $this->setLanguage($data);
        return $data;
    }

    public function setLanguage($data)
    {
        if (!is_array($data['lang_json']) || !count($data['lang_json'])) $data['lang_json'] = [ApiGame::LANG_CN => $data['title']];

        if ($data['api_name']) {
            $api = Api::where('api_name', $data['api_name'])->first();
            if (!in_array($api->lang, [Base::LANG_CN])) $data['lang'] = $api->lang;
        }

        $data['lang_json'] = json_encode($data['lang_json'], JSON_UNESCAPED_UNICODE);
        return $data;
    }

    // 首页分类管理
    public function category()
    {
        $data = SystemConfig::query()->getConfigValue('mobile_category_json', Base::LANG_COMMON);
        if ($data) $data = json_decode($data, 1);
        else $data = [];

        $web = SystemConfig::query()->getConfigValue('web_category_json', Base::LANG_COMMON);
        if ($web) $web = json_decode($web, 1);
        else $web = [];

        return view('admin.apigame.category', compact('data', 'web'));
    }

    // 保存
    public function mobile_category_save(Request $request)
    {
        $data = $request->all();

        $arr = [];

        foreach ($data['title'] as $key => $val) {
            if ($val) {
                array_push($arr, [
                    'title' => $val,
                    'game_type' => isset_and_not_empty($data['game_type'], $key, ''),
                    'icon_before' => isset_and_not_empty($data['icon_before'], $key, ''),
                    'icon_after' => isset_and_not_empty($data['icon_after'], $key, ''),
                    'weight' => isset_and_not_empty($data['weight'], $key, '10'),
                    'is_open' => $data['is_open'][$key] ?? false
                ]);
            }
        }

        $mod = SystemConfig::query()->getConfig('mobile_category_json', Base::LANG_COMMON);

        if ($mod->update([
            'value' => json_encode($arr, 320)
        ])) {
            return $this->success(['reload' => true], trans('res.base.save_success'));
        } else {
            return $this->failed(trans('res.base.save_fail'));
        }
    }

    public function web_category_save(Request $request)
    {
        $data = $request->all();

        $arr = [];

        foreach (data_get($data, 'title') as $key => $value) {
            if (blank($value) || is_null($value)) {
                continue;
            }

            array_push($arr, [
                'title' => $value,
                'game_type' => isset_and_not_empty($data['game_type'], $key, ''),
                'icon_before' => isset_and_not_empty($data['icon_before'], $key, ''),
                'icon_after' => isset_and_not_empty($data['icon_after'], $key, ''),
                'weight' => isset_and_not_empty($data['weight'], $key, '10'),
                'is_open' => $data['is_open'][$key] ?? false
            ]);
        }

        $mod = SystemConfig::query()->getConfig('web_category_json', Base::LANG_COMMON);

        if ($mod->update(['value' => json_encode($arr, 320)])) {
            return $this->success(['reload' => true], trans('res.base.save_success'));
        } else {
            return $this->failed(trans('res.base.save_fail'));
        }
    }

    protected function validRule()
    {
        return [
            "title" => "required",
            "game_type" => Rule::in(array_keys(trans('res.option.game_type'))),
            "is_open" => ["required", Rule::in(array_keys(config('platform.is_open')))],
            "api_name" => "required"
        ];
    }
}
