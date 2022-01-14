<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2022 Parsa Shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Tests;

use Adminx\Tests\TestCase;
use Adminx\Plugins\IPlugin;
use Adminx\Core;

class PluginTest extends TestCase
{
    public function test_plugin_can_be_added()
    {
        $admin = new \Adminx\Core;

        $admin->add_plugin(new TheTestPlugin);
        $this->assertEquals($admin->get_title(), 'Set by plugin');

        $admin->add_plugin(new TheTestPlugin2, ['op1' => 'hello']);
        $this->assertEquals($admin->get_title(), 'Set by plugin. value: hello');
    }
}

class TheTestPlugin implements IPlugin {
    public function run(Core $admin, array $options=[])
    {
        $admin->set_title('Set by plugin');
    }
}

class TheTestPlugin2 implements IPlugin {
    public function run(Core $admin, array $options=[])
    {
        $admin->set_title('Set by plugin. value: ' . $options['op1']);
    }
}
