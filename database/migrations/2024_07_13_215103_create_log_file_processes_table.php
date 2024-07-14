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
        Schema::create('log_file_processes', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable();
            $table->enum('status', ['processing', 'finished', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_file_processes');
    }
};
