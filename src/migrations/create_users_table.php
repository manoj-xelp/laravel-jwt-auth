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
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('password');
            $table->string('username')->unique();
            $table->integer('account_type')->default(0);
            $table->string('phone_number')->nullable();
            $table->string('photo_url')->nullable();
            $table->text('about_me')->nullable();
            $table->date('date_of_birth')->nullable()->comment("user's date of birth");
            $table->string('verification_code')->nullable();
            $table->integer('status')->default(0)->comment("0-unverified 1-verified");
            $table->text('auth_token')->nullable()->comment("Authorization token generated for the user.");
            $table->timestamps();
            $table->softDeletes();
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
