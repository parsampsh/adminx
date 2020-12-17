<?php

namespace Adminx\Tests;

use Adminx\Tests\TestCase;

class HttpControllerTest extends TestCase
{
    public function test_user_should_be_login_to_acces_the_panel()
    {
        $admin = new \Adminx\Core;

        $admin->set_title('Sometitle');
        
        $admin->register('/admin');

        $this->get('/admin')->assertStatus(302);

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertStatus(200);
    }
}
