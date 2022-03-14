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
     * Gets file path and returns list of the files and directories inside of it
     * 
     * @param string $path
     * @return array
     */
    public static function dirList(string $path): array
    {
        $files = [];
        $directory = opendir($path);
        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            array_push($files, realpath($path . '/' . $file));
        }

        return $files;
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

    /**
     * Compresses a directory recursively to a zip file
     * 
     * @param FileItem $file
     * @param string $destination
     */
    public static function compressDir(FileItem $file, string $destination)
    {
        $zip = new \ZipArchive();
        $zip->open($destination, \ZipArchive::CREATE);

        $source = $file->path;

        if (is_dir($source) === true)
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
}
