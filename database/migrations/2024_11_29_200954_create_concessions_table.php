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
        Schema::create('concessions', function (Blueprint $table) {
            $table->id();
            $table->char('name', 255);
            $table->string('description');
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable(); // This will store the image path or filename
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concessions');
    }
};