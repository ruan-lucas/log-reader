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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('consumer_uuid');
            $table->uuid('service_uuid');
            $table->string('method');
            $table->string('uri');
            $table->integer('status');
            $table->integer('request_time');
            $table->integer('proxy_time');
            $table->integer('gateway_time');
            $table->timestamp('started_at');

            $table->foreign('consumer_uuid')->references('uuid')->on('consumers');
            $table->foreign('service_uuid')->references('uuid')->on('services');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
