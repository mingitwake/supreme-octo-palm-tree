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
        Schema::table('checkbox_options', function (Blueprint $table) {
            $table->foreign(['question_id'], 'checkbox_options_ibfk_1')->references(['id'])->on('questions')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkbox_options', function (Blueprint $table) {
            $table->dropForeign('checkbox_options_ibfk_1');
        });
    }
};
