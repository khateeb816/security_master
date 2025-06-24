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
        Schema::table('clients', function (Blueprint $table) {
            // Remove the zip column if it exists
            if (Schema::hasColumn('clients', 'zip')) {
                $table->dropColumn('zip');
            }

            // Make sure all other columns exist with correct types
            if (!Schema::hasColumn('clients', 'language')) {
                $table->string('language')->after('status')->default('English');
            }

            if (!Schema::hasColumn('clients', 'arc_id')) {
                $table->string('arc_id')->after('language')->nullable();
            }

            if (!Schema::hasColumn('clients', 'incident_report_email')) {
                $table->boolean('incident_report_email')->after('arc_id')->default(false);
            }

            if (!Schema::hasColumn('clients', 'mobile_form_email')) {
                $table->boolean('mobile_form_email')->after('incident_report_email')->default(false);
            }

            if (!Schema::hasColumn('clients', 'additional_recipients')) {
                $table->text('additional_recipients')->after('mobile_form_email')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it fixes an issue
    }
};
