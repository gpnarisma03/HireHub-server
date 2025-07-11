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
                Schema::create('users', function (Blueprint $table) {
                    $table->id('user_id');
                    $table->string('first_name');
                    $table->string('middle_initial')->nullable();
                    $table->string('last_name');
                    $table->string('email')->unique();
                    $table->string('mobile_number');
                    $table->string('password');
                    $table->enum('role', ['admin', 'employer', 'employee']);
                    $table->timestamps();
                });
            }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
