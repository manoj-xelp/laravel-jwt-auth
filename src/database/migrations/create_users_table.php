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
            $table->string('email')->unique()->index();
            $table->string('alternative_email')->nullable();
            $table->string('password')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('dial_code')->nullable();
            $table->enum('signup_method',['Email','Google','Facebook','Apple','Mobile'])->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('profile_pic')->nullable();
            $table->text('about_me')->nullable();
            $table->string('referal_code')->nullable();
            $table->unsignedBigInteger('refered_by')->nullable();
            $table->foreign('refered_by')->references('id')->on('users')->onDelete('cascade');
            $table->date('date_of_birth')->nullable()->comment("user's date of birth");
            $table->string('verification_code')->nullable();
            $table->integer('status')->default(0)->comment("0-unverified 1-verified -1-deleted");
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

