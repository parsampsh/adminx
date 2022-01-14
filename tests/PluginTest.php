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

class PluginTest extends TestCase
{
    public function test_plugin_can_be_added()
    {
        $admin = new \Adminx\Core;

        $admin->addPlugin(TheTestPlugin::class);
        $this->assertEquals($admin->getTitle(), 'Set by plugin');

        $admin->addPlugin(TheTestPlugin2::class, ['op1' => 'hello']);
        $this->assertEquals($admin->getTitle(), 'Set by plugin. value: hello');
    }
}

class TheTestPlugin {
    public function run($admin)
    {
        $admin->setTitle('Set by plugin');
    }
}

class TheTestPlugin2 {
    public function run($admin, $options=[])
    {
        $admin->setTitle('Set by plugin. value: ' . $options['op1']);
    }
}
