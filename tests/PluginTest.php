<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Tests;

use Adminx\Tests\TestCase;

class PluginTest extends TestCase
{
    public function test_plugin_can_be_added()
    {
        $admin = new \Adminx\Core;

        $admin->add_plugin(TheTestPlugin::class);
        $this->assertEquals($admin->get_title(), 'Set by plugin');

        $admin->add_plugin(TheTestPlugin2::class, ['op1' => 'hello']);
        $this->assertEquals($admin->get_title(), 'Set by plugin. value: hello');
    }
}

class TheTestPlugin {
    public function run($admin)
    {
        $admin->set_title('Set by plugin');
    }
}

class TheTestPlugin2 {
    public function run($admin, $options=[])
    {
        $admin->set_title('Set by plugin. value: ' . $options['op1']);
    }
}
