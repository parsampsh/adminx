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
 * The file item
 * This is a model for the files while we work with them
 */
class FileItem
{
    function __construct(
        public string $path,
        protected FileManagerPlugin $plugin
    )
    {}

    /**
     * Returns the authenticated user
     * 
     * @return \App\Model\User
     */
    protected function getUser()
    {
        return auth()->user();
    }

    /**
     * Check user permission to see this file in the files list
     */
    public function canSee(): bool
    {
        return (bool) call_user_func_array($this->plugin->canSee, [$this->getUser(), $this]);
    }

    /**
     * Check user permission to read this file
     */
    public function canRead(): bool
    {
        return (bool) call_user_func_array($this->plugin->canRead, [$this->getUser(), $this]);
    }

    /**
     * Says that is this file in the main files list or not
     * 
     * @return bool
     */
    public function isMainDir(): bool
    {
        return in_array(realpath($this->path), $this->plugin->dirs);
    }

    /**
     * Check user permission to delete this file
     */
    public function canDelete(): bool
    {
        if ($this->isMainDir()) {
            return false;
        }

        return (bool) call_user_func_array($this->plugin->canDelete, [$this->getUser(), $this]);
    }

    /**
     * Check user permission to write on this file
     */
    public function canWrite(): bool
    {
        return (bool) call_user_func_array($this->plugin->canWrite, [$this->getUser(), $this]);
    }

    /**
     * Returns the basename of the file from the path
     * 
     * @return string
     */
    public function name(): string
    {
        return pathinfo($this->path)['basename'];
    }

    /**
     * Returns that is this item a directory or not
     * 
     * @return bool
     */
    public function isDir(): bool
    {
        return is_dir($this->path);
    }

    /**
     * Returns parent directory path of the file
     * 
     * @return string
     */
    public function dirname(): string
    {
        return dirname($this->path);
    }

    /**
     * Check user's permission to download the whole directory
     * 
     * @return bool
     */
    public function canDownloadDirectory(): bool
    {
        if (!extension_loaded('zip')) {
            return false;
        }

        return (bool) call_user_func_array($this->plugin->canDownloadDirectory, [$this->getUser(), $this]);
    }
}
