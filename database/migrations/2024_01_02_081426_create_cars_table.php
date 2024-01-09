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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('plate_number');
            $table->decimal('price', 8, 2);
            $table->boolean('available');
            $table->string('main_picture');
            $table->string('rear_picture_1');
            $table->string('rear_picture_2');
            $table->string('rear_picture_3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
