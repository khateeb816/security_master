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
            $table->string('zip')->after('postal_code')->nullable();
            $table->string('language')->after('status')->default('English');
            $table->string('arc_id')->after('language')->nullable();
            $table->boolean('incident_report_email')->after('arc_id')->default(false);
            $table->boolean('mobile_form_email')->after('incident_report_email')->default(false);
            $table->text('additional_recipients')->after('mobile_form_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'zip',
                'language',
                'arc_id',
                'incident_report_email',
                'mobile_form_email',
                'additional_recipients'
            ]);
        });
    }
};
