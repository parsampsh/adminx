<?php

namespace Adminx\Tests;

use Adminx\Tests\TestCase;

class GeneralConfigTest extends TestCase
{
    public function test_general_configurations_on_core_object()
    {
        $admin = new \Adminx\Core;

        $this->assertEquals($admin->get_title(), 'Adminx Panel');

        $admin->set_title('Sometitle');
        $this->assertEquals($admin->get_title(), 'Sometitle');

        $admin->set_title('new title');
        $this->assertEquals($admin->get_title(), 'new title');

        $this->assertEquals($admin->get_copyright(), 'Copyright');

        $admin->set_copyright('All rights reserved');
        $this->assertEquals($admin->get_copyright(), 'All rights reserved');

        $admin->set_copyright('new message');
        $this->assertEquals($admin->get_copyright(), 'new message');
    }
}
