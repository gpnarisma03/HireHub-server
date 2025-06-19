<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_categories', function (Blueprint $table) {
            $table->string('category_logo')->nullable()->after('category_name');
        });
    }

    public function down(): void
    {
        Schema::table('job_categories', function (Blueprint $table) {
            $table->dropColumn('category_logo');
        });
    }
};
