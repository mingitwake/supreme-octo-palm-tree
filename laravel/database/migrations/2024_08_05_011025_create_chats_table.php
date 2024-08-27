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
            $table->uuid('id')->primary();
            $table->uuid('log_id')->index('log_id');
            $table->text('content')->nullable();
            $table->enum('role', ['user', 'asst', 'admin'])->nullable()->default('user');
            $table->enum('class', ['MScFees', 'MScCourses', 'GeneralInformation', 'MScApplication', 'MScEntranceRequirement', 'StudentSupport', 'Visa', 'Others'])->nullable();
            $table->timestamps();
            $table->softDeletes();
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
