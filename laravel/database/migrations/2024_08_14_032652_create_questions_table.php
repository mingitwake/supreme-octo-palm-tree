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
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('form_id')->index('form_id');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('no')->nullable();
            $table->integer('required')->nullable()->default(1);
            $table->enum('type', ['text','tel','number','url','date','email','checkbox','table']);
            $table->uuid('constraint_id')->nullable(); // UUID for the polymorphic ID
            $table->string('constraint_type')->nullable(); // Type for the polymorphic type
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
