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
    public \Closure $accessMiddleware;

    /**
     * The slug of the page that plugin creates
     * 
     * @var string
     */
    public string $pageSlug = 'file-manager';

    /**
     * List of the directories which developer wants to load in file manager
     * 
     * @var array
     */
    public array $dirs = [];

    /**
     * Receives the passed $options to the run method and processes them
     * 
     * @param array $options
     */
    protected function loadConfiguration(array $options)
    {
        if (!(isset($options['access_middleware']) && is_callable($options['access_middleware'])))
        {
            $options['access_middleware'] = function () { return true; };
        }

        if (isset($options['page_slug']) && is_string($options['page_slug']))
        {
            $this->pageSlug = $options['page_slug'];
        }

        if (isset($options['dirs']) && is_array($options['dirs']))
        {
            $this->dirs = $options['dirs'];
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
            return (new Controller($this))->handle();
        });
    }
}
