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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Denver International Airport');
            $table->string('aka')->comment('e.g. DEN, DIA');
            $table->string('sub')->comment('Sub name - like "Terminal East"');
            $table->string('address');
            $table->decimal('lat', total: 9, places: 6);
            $table->decimal('lon', total: 9, places: 6);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
