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
        Schema::create('cleared_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->integer('checkpoint_id');
            $table->integer('user_id');
            $table->string('status')->default('pending');
            $table->integer('round')->nullable();
            $table->string('description')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('time')->nullable();
            $table->string('date')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleared_checkpoints');
    }
};
