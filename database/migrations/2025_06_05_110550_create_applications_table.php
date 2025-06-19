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
            Schema::create('applications', function (Blueprint $table) {
                $table->id('application_id');
                $table->foreignId('job_id')->constrained('jobs', 'job_id')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->text('cover_letter');
                $table->string('resume_file'); // path to uploaded resume
                $table->timestamp('applied_at');
                $table->enum('status', ['pending', 'reviewed', 'hired', 'rejected']);
            });
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
