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
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('recipe_id')->constrained()->cascadeOnDelete();

            $table->integer('step_order');
            $table->text('text');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->index(['recipe_id', 'step_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
