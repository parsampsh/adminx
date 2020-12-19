<?php

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
        return view('adminx.default.index', ['core' => $this->core]);
    }
}
