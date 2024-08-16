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
        Schema::create('post_predicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi');
            $table->foreignId('kabupaten');
            $table->float('temperature_predict');
            $table->float('rainfall_predict');
            $table->float('humidity_predict');
            $table->float('windspeed_predict');
            $table->date('date');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_predicts');
    }
};
