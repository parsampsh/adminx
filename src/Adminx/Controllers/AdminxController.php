<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under GPL-v3.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

/**
 * The Adminx Controller
 */
class AdminxController extends BaseController
{
    private $core;

    public function __construct()
    {
        $this->core = \Adminx\Core::$core;
    }

    public function run_middleware()
    {
        $result = $this->core->run_middleware();
        if ($result === false) {
            abort(403);
        }
    }

    public function index()
    {
        $this->run_middleware();
        return view('adminx.index', ['core' => $this->core]);
    }

    public function show_page(Request $request, string $slug)
    {
        $this->run_middleware();
        // check page exists
        $action = null;
        $page_title = null;
        foreach ($this->core->get_menu() as $item) {
            if ($item['type'] === 'page') {
                if ($item['slug'] === $slug) {
                    $action = $item['action'];
                    $page_title = $item['title'];
                }
            }
        }

        if (!is_callable($action)) {
            abort(404);
        }

        // run the page action
        $output = call_user_func_array($action, [$request]);

        // return the view
        return view('adminx.page', ['output' => $output, 'core' => $this->core, 'page_title' => $page_title]);
    }

    public function model_index(Request $request, string $slug)
    {
        $this->run_middleware();
        $model_config = null;
        foreach($this->core->get_menu() as $item)
        {
            if($item['type'] === 'model'){
                if($item['config']['slug'] === $slug){
                    $model_config = $item['config'];
                }
            }
        }

        if($model_config === null){
            abort(404);
        }

        $middleware_result = call_user_func_array($model_config['middleware'], [auth()->user()]);
        if($middleware_result !== true){
            abort(403);
        }

        // load model table rows
        $rows = $model_config['model']::query();
        $rows = $model_config['filter_data']($rows);

        // handle search filter
        if($request->get('search')){
            if(is_callable($model_config['search'])){
                $rows = call_user_func_array($model_config['search'], [$rows, $request->get('search')]);
            }
        }

        // paginate and get the final data
        $rows = $rows->paginate($model_config['per_page']);

        return view('adminx.model', ['core' => $this->core, 'model_config' => $model_config, 'rows' => $rows]);
    }
}
