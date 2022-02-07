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
 * Some utilities for working with directories
 */
class DirectoryUtils
{
    /**
     * Deletes a directory
     * 
     * @param string $folderName
     * @return bool
     */
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
                    DirectoryUtils::deleteDir($folderName . '/' . $file);
                }
            }
        }

        closedir($folderHandle);

        rmdir($folderName);

        return true;
    }

    /**
     * Copies a directory recursively
     * 
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @param string $childFolder
     */
    public static function recurseCopy(string $sourceDirectory, string $destinationDirectory, string $childFolder = ''): void {
        $directory = opendir($sourceDirectory);
    
        if (is_dir($destinationDirectory) === false) {
            mkdir($destinationDirectory);
        }
    
        if ($childFolder !== '') {
            if (is_dir("$destinationDirectory/$childFolder") === false) {
                mkdir("$destinationDirectory/$childFolder");
            }
    
            while (($file = readdir($directory)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
    
                if (is_dir("$sourceDirectory/$file") === true) {
                    DirectoryUtils::recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
                } else {
                    copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
                }
            }
    
            closedir($directory);
    
            return;
        }
    
        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
    
            if (is_dir("$sourceDirectory/$file") === true) {
                DirectoryUtils::recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
            }
            else {
                copy("$sourceDirectory/$file", "$destinationDirectory/$file");
            }
        }
    
        closedir($directory);
    }    
}
