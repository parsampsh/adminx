<?php

// Checks \App\Models\User class. if this is not exists, creates a alias from \App\User for this

if (! class_exists('\App\Models\User')) {
    // TODO : fix error of this line for php cs fixer
    //class_alias('App\User', 'App\Models\User');
}
