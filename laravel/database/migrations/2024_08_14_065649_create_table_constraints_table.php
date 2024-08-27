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
        Schema::create('table_constraints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('question_id')->index('question_id');
            $table->integer('minrow')->nullable()->default(0);
            $table->integer('maxrow')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_constraints');
    }
};
