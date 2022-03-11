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

    /**
     * Handles the rename operation
     * 
     * @param \Request $request
     */
    public function rename($request)
    {
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

    /**
     * Generates a zip file from a directory to be downloaded
     *
     * @param FileItem $file
     */
    public function prepareDirectoryForDownload($file)
    {
        $tmpPath = sys_get_temp_dir() . '/' . time() . rand() . '.zip';
        DirectoryUtils::compressDir($file, $tmpPath);

        return new \Adminx\Views\NoBaseViewResponse(response()->file($tmpPath));
    }

    /**
     * Handles the download operation
     * 
     * @param \Request $request
     */
    public function download($request)
    {
        $file = new FileItem($request->get('download'), $this->plugin);

        if (file_exists($file->path)) {
            if (!$file->isDir()) {
                if ($file->canRead()) {
                    return new \Adminx\Views\NoBaseViewResponse(response()->file($file->path));
                } else {
                    abort(403);
                }
            } else {
                if ($file->canRead() && $file->canDownloadDirectory()) {
                    return $this->prepareDirectoryForDownload($file);
                } else {
                    abort(403);
                }
            }
        } else {
            abort(404);
        }
    }

    /**
     * Handles the paste operation
     * 
     * @param \Request $request
     */
    public function paste($request)
    {
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

    /**
     * Handles the copy operation
     * 
     * @param \Request $request
     */
    public function copy($request)
    {
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

    /**
     * Handles the delete operation
     * 
     * @param \Request $request
     */
    public function delete($request)
    {
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

    public function upload($request)
    {
        $file = $request->file('upload_file');

        $currentDirectory = new FileItem('/', $this->plugin);

        if ($request->get('currentLoc') !== null)
        {
            if ($this->checkPathIsValid($request->get('currentLoc'))) {
                $currentDirectory = new FileItem($request->get('currentLoc'), $this->plugin);
            } else {
                abort(403);
            }
        } else {
            abort(403);
        }

        if (!$currentDirectory->canWrite() || $currentDirectory->path === '/') {
            abort(403);
        }

        if (!call_user_func_array($this->plugin->uploadMiddleware, [$currentDirectory, $file])) {
            abort(403); // TODO : show a message
        }

        $clientOriginalName = str_replace('/', '', $file->getClientOriginalName());
        $clientOriginalName = str_replace('\\', '', $clientOriginalName);

        if (file_exists($currentDirectory->path . '/' . $clientOriginalName)) {
            abort(403); // TODO : show a message
        }

        $file->move($currentDirectory->path, $clientOriginalName);

        return new NoBaseViewResponse(redirect($request->fullUrl()));
    }

    public function handle($request)
    {
        if (!call_user_func_array($this->plugin->accessMiddleware, [auth()->user()])) {
            abort(403);
            return;
        }

        if ($request->file('upload_file') !== null) {
            return $this->upload($request);
        }

        if ($request->post('rename_file') !== null && $request->post('rename_to') !== null) {
            return $this->rename($request);
        }

        if ($request->get('download') !== null) {
            return $this->download($request);
        }

        if ($request->post('paste_file') !== null) {
            return $this->paste($request);
        }

        if ($request->post('copy_file') !== null || $request->post('cut_file') !== null) {
            return $this->copy($request);
        }

        if ($request->post('delete_file') !== null) {
            return $this->delete($request);
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
                    'core' => $this->plugin->core,
                ]);
            } else {
                // file not found
                return view('adminx.builtin_plugins.filemanager.main', [
                    'currentLocNotFound' => true,
                    'core' => $this->plugin->core,
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
            'core' => $this->plugin->core,
        ]);
    }
}
