<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2022 Parsa Shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Adminx Logs
 */
class Log extends Model
{
    protected $table = 'adminx_logs';

    /**
     * Inserts a new log
     *
     * @param string $model_slug
     * @param int $item_id
     * @param int $user_id
     * @param string $action (create, update, delete)
     * @param string $message
     * @return Log
     */
    public static function addLog(string $modelSlug, int $itemId, int $userId, string $action, string $message)
    {
        if ($modelSlug === 'AdminxLog' && $action === 'delete') {
            return null;
        }
        $log = new Log;
        $log->model = $modelSlug;
        $log->item_id = $itemId;
        $log->user_id = $userId;
        $log->action = $action;
        $log->message = $message;
        $log->save();

        return $log;
    }
}
