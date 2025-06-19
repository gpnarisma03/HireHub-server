<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
            public function up()
            {
                Schema::create('jobs', function (Blueprint $table) {
                    $table->id('job_id');
                    $table->foreignId('company_id')->constrained('companies', 'company_id')->onDelete('cascade');
                    $table->foreignId('category_id')->constrained('job_categories', 'category_id')->onDelete('cascade');
                    $table->string('job_title');
                    $table->text('job_description');
                    $table->text('job_qualifications');
                    $table->text('job_responsibilities');
                    $table->enum('job_type', ['full-time', 'part-time']);
                    $table->integer('job_vacancy');
                    $table->string('payment_range');
                    $table->enum('status', ['open', 'closed']);
                    $table->date('date_start');
                    $table->timestamp('posted_at');
                });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
