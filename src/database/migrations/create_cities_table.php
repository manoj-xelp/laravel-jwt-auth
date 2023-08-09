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
        Schema::create('cities', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->unsignedInteger('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
			$table->string('name');
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(0)->comment("1-active 0-pending -1-disabled");
            $table->timestamps();
            $table->softDeletes();

            $table->fullText(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
