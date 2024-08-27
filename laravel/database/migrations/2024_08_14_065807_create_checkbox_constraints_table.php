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
        Schema::create('checkbox_constraints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('question_id')->index('question_id');
            $table->integer('minselect')->nullable()->default(1);
            $table->integer('maxselect')->nullable()->default(1);
            $table->integer('others')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkbox_constraints');
    }
};
