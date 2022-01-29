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
     * Middleware to check can user see the file in files list
     * 
     * @var \Closure
     */
    public \Closure $canSee;

    /**
     * Middleware to check can user read the file
     * 
     * @var \Closure
     */
    public \Closure $canRead;

    /**
     * Middleware to check can user delete the file
     * 
     * @var \Closure
     */
    public \Closure $canDelete;

    /**
     * Middleware to check can user write on the file
     * 
     * @var \Closure
     */
    public \Closure $canWrite;

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

        if (!(isset($options['can_see']) && is_callable($options['can_see'])))
        {
            $options['can_see'] = function () { return true; };
        }
        if (!(isset($options['can_read']) && is_callable($options['can_read'])))
        {
            $options['can_read'] = function () { return true; };
        }
        if (!(isset($options['can_delete']) && is_callable($options['can_delete'])))
        {
            $options['can_delete'] = function () { return true; };
        }
        if (!(isset($options['can_write']) && is_callable($options['can_write'])))
        {
            $options['can_write'] = function () { return true; };
        }

        $this->canSee = $options['can_see'];
        $this->canRead = $options['can_read'];
        $this->canDelete = $options['can_delete'];
        $this->canWrite = $options['can_write'];
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

        $admin->addPage($admin->getWord('file-manager.page-title', 'File Manager'), $this->pageSlug, function($request) {
            return (new Controller($this))->handle($request);
        });
    }
}
