<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('davinci_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('davinci_permissions')->onDelete('cascade');
            $table->enum('granted', ['no', 'yes'])->default('no');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('davinci_user_permissions');
    }
};
