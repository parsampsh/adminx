<?php

namespace Adminx\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

/**
 * The Adminx Controller
 */
class AdminxController extends BaseController
{
    public function index()
    {
        return 'welcome to adminx';
    }
}
