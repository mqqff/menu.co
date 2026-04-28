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
        Schema::create('ingredient_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('recipe_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->timestamps();

            $table->index('recipe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_groups');
    }
};
