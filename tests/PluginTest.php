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

        $admin->addPlugin(new TheTestPlugin);
        $this->assertEquals($admin->getTitle(), 'Set by plugin');

        $admin->addPlugin(new TheTestPlugin2, ['op1' => 'hello']);
        $this->assertEquals($admin->getTitle(), 'Set by plugin. value: hello');
    }
}

class TheTestPlugin implements IPlugin {
    public function run(Core $admin, array $options=[])
    {
        $admin->setTitle('Set by plugin');
    }
}

class TheTestPlugin2 implements IPlugin {
    public function run(Core $admin, array $options=[])
    {
        $admin->setTitle('Set by plugin. value: ' . $options['op1']);
    }
}
