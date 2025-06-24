<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('cnic', 15)->nullable()->after('phone');
            $table->string('nfc_uid', 50)->nullable()->after('cnic');
            $table->string('designation', 100)->nullable()->after('nfc_uid');
            $table->enum('role', ['admin', 'supervisor', 'guard'])->default('guard')->after('designation');
            $table->foreignId('client_id')->nullable()->after('role');
            $table->decimal('latitude', 10, 8)->nullable()->after('client_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('longitude');
            
            // Add foreign key constraint
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['client_id']);
            
            // Drop columns
            $table->dropColumn([
                'phone',
                'cnic',
                'nfc_uid',
                'designation',
                'role',
                'client_id',
                'latitude',
                'longitude',
                'status'
            ]);
        });
    }
};
