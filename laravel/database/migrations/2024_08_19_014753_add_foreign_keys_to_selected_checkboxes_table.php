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
        Schema::table('selected_checkboxes', function (Blueprint $table) {
            $table->foreign(['answer_id'], 'selected_checkboxes_ibfk_1')->references(['id'])->on('answers')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['checkbox_option_id'], 'selected_checkboxes_ibfk_2')->references(['id'])->on('checkbox_options')->onUpdate('restrict')->onDelete('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('selected_checkboxes', function (Blueprint $table) {
            $table->dropForeign('selected_checkboxes_ibfk_1');
            $table->dropForeign('selected_checkboxes_ibfk_2');
        });
    }
};
