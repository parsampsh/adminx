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

    public function test_access_middleware_should_return_true_to_user_access()
    {
        $admin = new \Adminx\Core;

        $admin->set_middleware(function(){
            return false;
        });
        
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertStatus(403);

        $admin = new \Adminx\Core;

        $admin->set_middleware(function($user){
            return $user->username === 'manager';
        });

        $user->username .= '1';
        $user->save();
        
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin')->assertStatus(403);

        $user->username = 'manager';
        $user->save();

        $this->actingAs($user)->get('/admin')->assertStatus(200);
    }
}
