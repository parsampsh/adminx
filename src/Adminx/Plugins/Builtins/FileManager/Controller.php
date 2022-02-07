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

use Adminx\Views\NoBaseViewResponse;

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
        if ($realPath === false) {
            return false;
        }
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

    public function handle($request)
    {
        if (!call_user_func_array($this->plugin->accessMiddleware, [auth()->user()])) {
            abort(403);
            return;
        }

        if ($request->post('rename_file') !== null && $request->post('rename_to') !== null) {
            $file = new FileItem($request->get('rename_file'), $this->plugin);

            if (file_exists($file->path)) {
                if ($file->canRead() && $file->canDelete()) {
                    $renameTo = $request->post('rename_to');
                    $renameTo = str_replace('/', '', $renameTo);
                    $renameTo = str_replace('\\', '', $renameTo);

                    $newFilePath = $file->dirname() . '/' . $renameTo;

                    if (file_exists($newFilePath)) {
                        abort(403); // TODO : show an alert instead
                    } else {
                        // eveything is ok
                        // renaming the file
                        rename($file->path, $newFilePath);

                        return new NoBaseViewResponse(redirect($request->fullUrl()));
                    }
                } else {
                    abort(403);
                }
            } else {
                abort(404);
            }
        }

        if ($request->get('download') !== null) {
            $file = new FileItem($request->get('download'), $this->plugin);

            if (is_file($file->path)) {
                if ($file->canRead()) {
                    return new \Adminx\Views\NoBaseViewResponse(response()->file($file->path));
                } else {
                    abort(403);
                }
            } else {
                abort(404);
            }
        }

        if ($request->post('paste_file') !== null) {
            if (session()->has('adminx_filemanager_clipboard')) {
                $clipboard = session()->get('adminx_filemanager_clipboard');
                if ($this->checkPathIsValid($clipboard) && $this->checkPathIsValid($request->get('currentLoc'))) {
                    $file = new FileItem($clipboard, $this->plugin);

                    if ($file->canRead()) {
                        $currentDir = new FileItem($request->get('currentLoc'), $this->plugin);

                        if ($currentDir->canWrite()) {
                            $basename = $file->name();

                            $dstPath = $currentDir->path . '/' . $basename;
                            $dstPathBackup = $dstPath;

                            $counter = 1;
                            while (file_exists($dstPath)) {
                                $dstPath = $dstPathBackup . $counter;
                                $counter++;
                            }

                            if (session()->get('adminx_filemanager_clipboard_is_cut') === true) {
                                if ($file->canDelete()) {
                                    rename($file->path, $dstPath);

                                    session()->remove('adminx_filemanager_clipboard_is_cut');
                                    session()->remove('adminx_filemanager_clipboard');
                                } else {
                                    abort(403);
                                }
                            } else {
                                if (is_file($file->path)) {
                                    copy($file->path, $dstPath);
                                } else {
                                    DirectoryUtils::recurseCopy($file->path, $dstPath);
                                }
                            }

                            return new NoBaseViewResponse(redirect($request->fullUrl()));
                        } else {
                            abort(403);
                        }
                    } else {
                        abort(403);
                    }
                } else {
                    abort(403);
                }
            } else {
                abort(403);
            }
        }

        if ($request->post('copy_file') !== null || $request->post('cut_file') !== null) {
            $path = $request->post('copy_file') !== null ? $request->post('copy_file') : $request->post('cut_file');
            if ($this->checkPathIsValid($path)) {
                $file = new FileItem($path, $this->plugin);

                if ($file->canRead() && ($request->post('cut_file') === null || $file->canDelete())) {
                    session()->put('adminx_filemanager_clipboard', $file->path);
                    if ($request->post('cut_file') !== null) {
                        session()->put('adminx_filemanager_clipboard_is_cut', true);
                    } else {
                        session()->put('adminx_filemanager_clipboard_is_cut', false);
                    }
                    return new NoBaseViewResponse(redirect($request->fullUrl()));
                } else {
                    abort(403);
                }
            } else {
                abort(403);
            }
        }

        if ($request->post('delete_file') !== null) {
            if ($this->checkPathIsValid($request->post('delete_file'))) {
                $file = new FileItem($request->post('delete_file'), $this->plugin);

                if ($file->canDelete())
                {
                    if ($file->isDir()) {
                        DirectoryUtils::deleteDir($file->path);
                    } else {
                        unlink($file->path);
                    }

                    return new NoBaseViewResponse(redirect($request->fullUrl()));
                } else {
                    abort(403);
                }
            } else {
                abort(403);
            }
        }

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
                $items = glob($currentLoc . '/*'); // TODO : use readdir instead
                $currentLoc = realpath($currentLoc);
            } else if (is_file($currentLoc) && $isCurrentLocValid) {
                if (!((new FileItem($currentLoc, $this->plugin))->canRead())) {
                    abort(404);
                    return;
                }
                $f = fopen($currentLoc, 'r');
                $content = fread($f, filesize($currentLoc)+1);
                fclose($f);
                $currentLoc = realpath($currentLoc);

                return view('adminx.builtin_plugins.filemanager.main', [
                    'fileContent' => $content,
                    'currentLoc' => $currentLoc,
                    'parentDir' => $parentDir,
                    'fileItem' => new FileItem($currentLoc, $this->plugin),
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
            'currentLocObj' => new FileItem($currentLoc, $this->plugin),
            'parentDir' => $parentDir,
        ]);
    }
}
