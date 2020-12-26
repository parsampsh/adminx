<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under GPL-v3.
 * For more information, please see the LICENSE file.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminxTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adminx_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('adminx_group_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adminx_group_id')->constrained();
            $table->string('permission');
            $table->boolean('flag');
            $table->timestamps();
        });

        Schema::create('adminx_user_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('adminx_group_id')->constrained();
            $table->timestamps();
        });

        Schema::create('adminx_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('permission');
            $table->boolean('flag');
            $table->timestamps();
        });

        Schema::create('adminx_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->integer('item_id');
            $table->string('model');
            $table->string('action');
            $table->string('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adminx_groups');
        Schema::dropIfExists('adminx_group_permissions');
        Schema::dropIfExists('adminx_user_groups');
        Schema::dropIfExists('adminx_user_permissions');
        Schema::dropIfExists('adminx_logs');
    }
}
