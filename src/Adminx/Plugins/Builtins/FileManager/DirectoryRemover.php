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
 * Here's a static function which removes a directory recursively
 */
class DirectoryRemover
{
    public static function deleteDir(string $folderName): bool
    {
        if (is_dir($folderName)) {
            $folderHandle = opendir($folderName);
        }

        if (!$folderHandle) {
            return false;
        }

        while($file = readdir($folderHandle)) {
            if ($file !== "." && $file !== "..") {
                if (!is_dir($folderName . "/" . $file)) {
                    unlink($folderName . "/" . $file);
                } else {
                    DirectoryRemover::deleteDir($folderName . '/' . $file);
                }
            }
        }

        closedir($folderHandle);

        rmdir($folderName);

        return true;
    }
}
