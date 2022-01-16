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

use Adminx\Plugins\IPlugin;
use Adminx\Core;

class FileManagerPlugin implements IPlugin
{
    /**
     * The closure which determines which users can access the file manager
     * 
     * @var \Closure
     */
    protected \Closure $accessMiddleware;

    /**
     * The slug of the page that plugin creates
     * 
     * @var string
     */
    protected string $pageSlug = 'file-manager';

    /**
     * Receives the passed $options to the run method and processes them
     * 
     * @param array $options
     */
    protected function loadConfiguration(array $options)
    {
        if (!(isset($options['access_middleware']) && $options['access_middleware'] instanceof \Closure))
        {
            $options['access_middleware'] = function () { return true; };
        }

        if (isset($options['page_slug']) && is_string($options['page_slug']))
        {
            $this->pageSlug = $options['page_slug'];
        }

        $this->accessMiddleware = $options['access_middleware'];
    }

    /**
     * Main method of the plugin
     * 
     * @param Core $admin
     * @param array $options
     */
    public function run(Core $admin, array $options = [])
    {
        $this->loadConfiguration($options);

        $admin->addPage($admin->getWord('file-manager.page-title', 'File Manager'), $this->pageSlug, function() {
            if (!call_user_func_array($this->accessMiddleware, [auth()->user()])) {
                abort(403);
                return;
            }

            return 'welcome to file manager';
        });
    }
}
