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

    /**
     * Gets a path and validates that
     * If the path is under the main paths in $this->plugin->dirs, it is valid
     * 
     * @param string $path
     * @return bool
     */
    protected function checkPathIsValid(string $path): bool
    {
        $realPath = realpath($path);
        $isPathValid = false;
        foreach ($this->plugin->dirs as $mainDir) {
            $mainDir = realpath($mainDir);

            if ($mainDir !== false) {
                if (str_starts_with($realPath, $mainDir)) {
                    $isPathValid = true;
                    break;
                }
            }
        }

        return $isPathValid;
    }

    public function handle()
    {
        if (!call_user_func_array($this->plugin->accessMiddleware, [auth()->user()])) {
            abort(403);
            return;
        }

        $request = request();
        $currentLoc = '/';
        $items = [];
        $parentDir = null;

        if ($request->get('currentLoc') !== null)
        {
            $currentLoc = $request->get('currentLoc');
        }

        if ($currentLoc === '/') {
            $items = $this->plugin->dirs;

            for($i = 0; $i < count($items); $i++) {
                $items[$i] = realpath($items[$i]);
            }
        } else {
            // validate path
            $isCurrentLocValid = $this->checkPathIsValid($currentLoc);
            $parentDir = dirname($currentLoc);
            if (!$this->checkPathIsValid($parentDir)) {
                $parentDir = '/';
            }

            if (is_dir($currentLoc) && $isCurrentLocValid) {
                $items = glob($currentLoc . '/*');
                $currentLoc = realpath($currentLoc);
            } else if (is_file($currentLoc) && $isCurrentLocValid) {
                $f = fopen($currentLoc, 'r');
                $content = fread($f, filesize($currentLoc));
                fclose($f);
                $currentLoc = realpath($currentLoc);

                return view('adminx.builtin_plugins.filemanager.main', [
                    'fileContent' => $content,
                    'currentLoc' => $currentLoc,
                    'parentDir' => $parentDir,
                ]);
            } else {
                // file not found
                return view('adminx.builtin_plugins.filemanager.main', [
                    'currentLocNotFound' => true,
                ]);
            }
        }

        // let's sort the items: directories first
        $newItems = [];

        foreach($items as $item) {
            if (is_dir($item)) {
                $tmp = new FileItem($item, $this->plugin);
                if ($tmp->canSee()) {
                    array_push($newItems, $tmp);
                }
            }
        }

        foreach($items as $item) {
            if (is_file($item)) {
                $tmp = new FileItem($item, $this->plugin);
                if ($tmp->canSee()) {
                    array_push($newItems, $tmp);
                }
            }
        }

        $items = $newItems;

        return view('adminx.builtin_plugins.filemanager.main', [
            'items' => $items,
            'currentLoc' => $currentLoc,
            'parentDir' => $parentDir,
        ]);
    }
}
