<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under GPL-v3.
 * For more information, please see the LICENSE file.
 */

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

        $this->assertEquals($admin->get_logout(), '/auth/logout');

        $admin->set_logout('/logout');
        $this->assertEquals($admin->get_logout(), '/logout');

        $admin->set_logout('/somelink');
        $this->assertEquals($admin->get_logout(), '/somelink');

        $this->assertEquals($admin->get_userinfo(), ['username' => 'unset', 'image' => 'unset']);

        $admin->set_userinfo(function () {
            return [
                'username' => 'hello world',
                'image' => '/link'
            ];
        });
        $this->assertEquals($admin->get_userinfo(), ['username' => 'hello world', 'image' => '/link']);

        $admin->set_userinfo(function () {
            return [
                'username' => 'hello world',
                'fsfgfh' => 'gfghfh',
            ];
        });
        $this->assertEquals($admin->get_userinfo(), ['username' => 'hello world', 'image' => 'unset']);

        $admin->set_userinfo(function () {
            return [
                'username' => 'hello world',
                'fsfgfh' => 'gfghfh',
            ];
        });
        $this->assertEquals($admin->get_userinfo(), ['username' => 'hello world', 'image' => 'unset']);

        $admin->set_userinfo(function ($user) {
            return [
                'username' => 'hello world',
            ];
        });
        $this->assertEquals($admin->get_userinfo(), ['username' => 'hello world', 'image' => 'unset']);

        $user = \App\Models\User::factory()->create();
        auth()->login($user);

        $admin->set_userinfo(function ($user) {
            return [
                'username' => $user->email,
            ];
        });
        $this->assertEquals($admin->get_userinfo(), ['username' => $user->email, 'image' => 'unset']);

        $this->assertEquals($admin->get_layout(), 'adminx.layouts.default');
        $admin->set_layout('the-layout');
        $this->assertEquals($admin->get_layout(), 'the-layout');

        auth()->logout();
    }

    public function test_localization_words(){
        $admin = new \Adminx\Core;

        $this->assertEquals($admin->get_word('hello'), '');
        $this->assertEquals($admin->get_word('hello', 'the default'), 'the default');

        $admin->set_word('hello', 'hello world');

        $this->assertEquals($admin->get_word('hello'), 'hello world');
        $this->assertEquals($admin->get_word('hello', 'the default'), 'hello world');

        $this->assertEquals($admin->get_word('bye'), '');
        $this->assertEquals($admin->get_word('bye', 'the default'), 'the default');

        $admin->set_word('bye', 'good bye');

        $this->assertEquals($admin->get_word('bye'), 'good bye');
        $this->assertEquals($admin->get_word('bye', 'the default'), 'good bye');

        $this->assertEquals($admin->get_all_words(), ['hello' => 'hello world', 'bye' => 'good bye']);
    }
}
