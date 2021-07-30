<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user_name');
            $table->string('avatar')->nullable();
            $table->string('email')->unique();
            $table->timestamp('registered_at')->nullable();
            $table->integer('email_confirmation_pin')->nullable();
            $table->string('password');
            $table->string('api_token', 80)->unique()->nullable();
            $table->string('user_role')->default('user');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
