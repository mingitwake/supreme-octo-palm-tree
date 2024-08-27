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
        Schema::table('number_constraints', function (Blueprint $table) {
            $table->foreign(['question_id'], 'number_constraints_ibfk_1')->references(['id'])->on('questions')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('number_constraints', function (Blueprint $table) {
            $table->dropForeign('number_constraints_ibfk_1');
        });
    }
};
