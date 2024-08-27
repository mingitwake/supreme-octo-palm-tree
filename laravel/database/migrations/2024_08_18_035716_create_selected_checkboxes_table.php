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
        Schema::create('selected_checkboxes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('answer_id')->index('answer_id');
            $table->uuid('checkbox_option_id')->index('checkbox_option_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selected_checkboxes');
    }
};
