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
        Schema::table('answers', function (Blueprint $table) {
            $table->foreign(['response_id'], 'answers_ibfk_1')->references(['id'])->on('responses')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['question_id'], 'answers_ibfk_2')->references(['id'])->on('questions')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign('answers_ibfk_1');
            $table->dropForeign('answers_ibfk_2');
        });
    }
};
