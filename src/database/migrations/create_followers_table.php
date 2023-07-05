<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('follower_id')->comment('user who followed');
            $table->foreign('follower_id')->references('id')->on('users');
            $table->unsignedBigInteger('leader_id')->comment('user who following');
            $table->foreign('leader_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['follower_id', 'leader_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
