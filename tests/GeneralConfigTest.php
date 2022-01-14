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

class GeneralConfigTest extends TestCase
{
    public function test_general_configurations_on_core_object()
    {
        $admin = new \Adminx\Core;

        $this->assertEquals($admin->getTitle(), 'Adminx Panel');

        $admin->setTitle('Sometitle');
        $this->assertEquals($admin->getTitle(), 'Sometitle');

        $admin->setTitle('new title');
        $this->assertEquals($admin->getTitle(), 'new title');

        $this->assertEquals($admin->getCopyright(), 'Copyright');

        $admin->setCopyright('All rights reserved');
        $this->assertEquals($admin->getCopyright(), 'All rights reserved');

        $admin->setCopyright('new message');
        $this->assertEquals($admin->getCopyright(), 'new message');

        $this->assertEquals($admin->getLogout(), '/auth/logout');

        $admin->setLogout('/logout');
        $this->assertEquals($admin->getLogout(), '/logout');

        $admin->setLogout('/somelink');
        $this->assertEquals($admin->getLogout(), '/somelink');

        $this->assertEquals($admin->getUserinfo(), ['username' => 'unset', 'image' => 'unset']);

        $admin->setUserinfo(function () {
            return [
                'username' => 'hello world',
                'image' => '/link'
            ];
        });
        $this->assertEquals($admin->getUserinfo(), ['username' => 'hello world', 'image' => '/link']);

        $admin->setUserinfo(function () {
            return [
                'username' => 'hello world',
                'fsfgfh' => 'gfghfh',
            ];
        });
        $this->assertEquals($admin->getUserinfo(), ['username' => 'hello world', 'image' => 'unset']);

        $admin->setUserinfo(function () {
            return [
                'username' => 'hello world',
                'fsfgfh' => 'gfghfh',
            ];
        });
        $this->assertEquals($admin->getUserinfo(), ['username' => 'hello world', 'image' => 'unset']);

        $admin->setUserinfo(function ($user) {
            return [
                'username' => 'hello world',
            ];
        });
        $this->assertEquals($admin->getUserinfo(), ['username' => 'hello world', 'image' => 'unset']);

        $user = \App\Models\User::factory()->create();
        auth()->login($user);

        $admin->setUserinfo(function ($user) {
            return [
                'username' => $user->email,
            ];
        });
        $this->assertEquals($admin->getUserinfo(), ['username' => $user->email, 'image' => 'unset']);

        $this->assertEquals($admin->getLayout(), 'adminx.layouts.default');
        $admin->setLayout('the-layout');
        $this->assertEquals($admin->getLayout(), 'the-layout');

        auth()->logout();
    }

    public function test_localization_words(){
        $admin = new \Adminx\Core;

        $this->assertEquals($admin->getWord('hello'), '');
        $this->assertEquals($admin->getWord('hello', 'the default'), 'the default');

        $admin->setWord('hello', 'hello world');

        $this->assertEquals($admin->getWord('hello'), 'hello world');
        $this->assertEquals($admin->getWord('hello', 'the default'), 'hello world');

        $this->assertEquals($admin->getWord('bye'), '');
        $this->assertEquals($admin->getWord('bye', 'the default'), 'the default');

        $admin->setWord('bye', 'good bye');

        $this->assertEquals($admin->getWord('bye'), 'good bye');
        $this->assertEquals($admin->getWord('bye', 'the default'), 'good bye');

        $this->assertEquals($admin->getAllWords(), ['hello' => 'hello world', 'bye' => 'good bye']);

        $this->assertEquals($admin->getFont(), null);
        $admin->setFont('/test/font.ttf');
        $this->assertEquals($admin->getFont(), '/test/font.ttf');
    }

    public function test_model_get_fields_works()
    {
        $admin = new \Adminx\Core;
        $admin->addModel(\App\Models\User::class, [
        ]);
        $menu = $admin->getMenu();
        $columns = $admin->getModelColumns($menu[count($menu)-1]['config']);

        $this->assertEquals($columns, [
            "id",
            "username",
            "email",
            "password",
            "remember_token",
            "created_at",
            "updated_at",
        ]);

        $admin = new \Adminx\Core;
        $admin->addModel(\App\Models\User::class, [
            'hidden_fields' => [
                'password',
            ],
        ]);
        $menu = $admin->getMenu();
        $columns = $admin->getModelColumns($menu[count($menu)-1]['config']);

        $this->assertEquals($columns, [
            "id",
            "username",
            "email",
            "remember_token",
            "created_at",
            "updated_at",
        ]);
    }
}
