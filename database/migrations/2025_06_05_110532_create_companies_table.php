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
            Schema::create('companies', function (Blueprint $table) {
                $table->id('company_id');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->string('company_name');
                $table->text('company_details')->nullable();
                $table->string('company_logo')->nullable();
                $table->string('street');
                $table->string('city');
                $table->string('region');
                $table->string('zip_code');
                $table->timestamps();
            });
        }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
