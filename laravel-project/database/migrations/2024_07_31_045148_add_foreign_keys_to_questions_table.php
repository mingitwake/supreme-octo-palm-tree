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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign(['form_id'], 'questions_ibfk_1')->references(['id'])->on('forms')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['form_id'], 'questions_ibfk_2')->references(['id'])->on('forms')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_ibfk_1');
            $table->dropForeign('questions_ibfk_2');
        });
    }
};
