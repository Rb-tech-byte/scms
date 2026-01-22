<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('davinci_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('davinci_roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('davinci_permissions')->onDelete('restrict');
            $table->enum('granted', ['no', 'yes'])->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('davinci_role_permissions');
    }
};
