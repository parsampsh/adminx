<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

// in laravel 8, models are moved from app/ to app/models
// but some of laravel apps that migrated from 7 to 8,
// maybe don't moved models to app/models/
// location of models is not important for adminx
// but as exception, location of User model is.
// this code checks if user model is App\User,
// makes a alias for it in App\Models\User
if (! class_exists('\App\Models\User')) {
    if (class_exists('\App\User')) {
        class_alias('App\User', 'App\Models\User');
    }
}
