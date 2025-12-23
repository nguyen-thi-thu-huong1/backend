<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Casso;
use Illuminate\Http\Request;

class CassoController extends AdminBaseController
{
    public function __construct(Casso $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request)
    {
        return view("{$this->view_folder}.index");
    }
}
