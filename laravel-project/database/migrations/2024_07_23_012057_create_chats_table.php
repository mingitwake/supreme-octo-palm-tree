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
        Schema::create('chats', function (Blueprint $table) {
            $table->integer('chatid', true);
            $table->integer('logid')->index('logid');
            $table->text('content')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->integer('role');
            $table->integer('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
