<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

if (! class_exists('\App\Models\User')) {
    if (class_exists('\App\User')){
        class_alias('App\User', 'App\Models\User');
    }
}
