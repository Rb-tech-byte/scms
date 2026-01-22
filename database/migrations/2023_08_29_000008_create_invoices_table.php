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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->double('balance');
            $table->double('paid_amount');
            $table->text('description')->nullable();
            $table->string('invoice_prefix');
            $table->boolean('has_discount')->default(false);
            $table->double('discount_amount')->nullable();
            $table->string('discount_reason')->nullable();
            $table->string('discount_file')->nullable();
            $table->date('date');
            $table->enum('status', ['0', '1', '2'])->default('0');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
