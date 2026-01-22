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
        Schema::create('davinci_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->unique();
            $table->enum('category',['administrative','accounting','courses','staffs','certificate']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('davinci_permissions');
    }
};
