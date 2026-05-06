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
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id('attendance_session_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('administrator_id')->nullable();

            $table->string('attendance_name');
            $table->date('attendance_date');
            $table->time('time_in_start')->nullable();
            $table->time('time_out_end')->nullable();

            $table->timestamps();

            $table->foreign('event_id')
                  ->references('event_id')
                  ->on('events')
                  ->onDelete('cascade');

            $table->foreign('administrator_id')
                  ->references('administrator_id')
                  ->on('administrators')
                  ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
