<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2022 Parsa Shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Plugins;

use Adminx\Core;

/**
 * The plugin interface
 * 
 * The plugin classes have to implement this interface
 */
interface IPlugin
{
    /**
     * The main method of the plugin
     * The plugin makes changes on the admin configuration at this method
     * Also there can be some additional options at $options parameter
     * Developers can pass these options in $admin->add_pluign() method
     * 
     * @param $admin \Adminx\Core
     * @param $options array
     */
    public function run(Core $admin, array $options=[]);
}
