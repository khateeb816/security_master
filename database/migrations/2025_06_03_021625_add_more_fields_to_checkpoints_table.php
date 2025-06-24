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
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->string('point_code')->after('branch_id');
            $table->decimal('latitude', 10, 8)->nullable()->after('point_code');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('geofence_radius')->nullable()->after('longitude');
            $table->boolean('geofence_enabled')->default(false)->after('geofence_radius');
            $table->string('site')->nullable()->after('geofence_enabled');
            $table->string('client_site_code')->nullable()->after('site');
            $table->string('checkpoint_code')->nullable()->after('client_site_code');
            $table->text('notes')->nullable()->after('checkpoint_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn([
                'point_code',
                'latitude',
                'longitude',
                'geofence_radius',
                'geofence_enabled',
                'site',
                'client_site_code',
                'checkpoint_code',
                'notes'
            ]);
        });
    }
};
