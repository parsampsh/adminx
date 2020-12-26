<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
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
}
