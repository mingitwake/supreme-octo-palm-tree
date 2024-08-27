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
        Schema::table('table_rows', function (Blueprint $table) {
            $table->foreign(['answer_id'], 'table_rows_ibfk_1')->references(['id'])->on('answers')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_rows', function (Blueprint $table) {
            $table->dropForeign('table_rows_ibfk_1');
        });
    }
};
