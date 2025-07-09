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
        Schema::create('assign_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->integer('checkpoint_id');
            $table->integer('guard_id');
            $table->date('date_to_check');
            $table->time('time_to_check');
            $table->string('checked_time')->nullable();
            $table->string('status')->default('pending');
            $table->integer('priority')->default(0);
            $table->json('images')->nullable();//{type , url}
            $table->json('videos')->nullable();//{type , url}
            $table->json('audios')->nullable();//{type , url}
            $table->string('nfc_tag')->nullable();
            $table->string('notes')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_checkpoints');
    }
};
