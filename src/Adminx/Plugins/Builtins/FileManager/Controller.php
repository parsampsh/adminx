<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2022 Parsa Shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Plugins\Builtins\FileManager;

/**
 * HTTP Controller for file manager
 */
class Controller
{
    function __construct(protected FileManagerPlugin $plugin)
    {
    }

    public function handle()
    {
        if (!call_user_func_array($this->plugin->accessMiddleware, [auth()->user()])) {
            abort(403);
            return;
        }

        return 'welcome to file manager';
    }
}
