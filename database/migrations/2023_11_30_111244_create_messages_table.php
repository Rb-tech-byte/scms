<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.sender_id', 'receiver_id', 'body','opened','opened_at','status
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id')->nullable()->index('sender_id_foreign');
            $table->unsignedBigInteger('receiver_id')->nullable()->index('receiver_id_foreign');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('opened')->default(0);
            $table->timestamp('opened_at')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
