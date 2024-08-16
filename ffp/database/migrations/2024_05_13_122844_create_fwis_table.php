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
        Schema::create('fwis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi');
            $table->foreignId('kabupaten');
            $table->float('ffmc');
            $table->float('dmc');
            $table->float('dc');
            $table->float('isi');
            $table->float('bui');
            $table->float('fwi');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fwis');
    }
};
