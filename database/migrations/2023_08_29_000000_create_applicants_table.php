<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name');
            $table->string('home_address')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->string('office_of_work')->nullable();
            $table->string('occupation')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('name_of_sponsor')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('education_level');
            $table->boolean('is_staff_family_member')->default(false);
            $table->boolean('is_staff')->default(false);
            $table->boolean('is_out_student')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
