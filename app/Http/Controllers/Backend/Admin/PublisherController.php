<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Api;
use App\Models\Publisher;
use App\Models\Base;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublisherController extends AdminBaseController
{
    protected $create_field = ['title','web_pic', 'mobile_pic','params','lang_json','lang','is_open'];
    protected $update_field = ['title','web_pic', 'mobile_pic','params','lang_json','lang','is_open'];

    public function __construct(Publisher $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $data = $this->model->latest()->paginate(request('per_page', apiPaginate()));
        $params = $request->all();
        return view("{$this->view_folder}.index", compact('data', 'params'));
    }

    public function edit(Publisher $publisher)
    {
        return view($this->getEditViewName(), ["model" => $publisher]);
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
        $data = $this->setLanguage($data);
        return $data;
    }

    public function updateHandle($data)
    {
        $data = $this->setLanguage($data);
        return $data;
    }

    public function setLanguage($data)
    {
        if (!is_array($data['lang_json']) || !count($data['lang_json'])) $data['lang_json'] = [Publisher::LANG_CN => $data['title']];
        $data['lang_json'] = json_encode($data['lang_json'], JSON_UNESCAPED_UNICODE);
        return $data;
    }

    protected function validRule()
    {
        return [
            "title"     => "required",
            "lang"      => "required",
            "is_open"   => ["required", Rule::in(array_keys(config('platform.is_open')))],
        ];
    }
}
