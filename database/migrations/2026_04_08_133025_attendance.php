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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id');

            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('administrator_id')->nullable();
            $table->unsignedBigInteger('attendance_session_id')->nullable();

            $table->timestamp('attended_at')->nullable();
            $table->timestamp('time_in')->nullable();
            $table->timestamp('time_out')->nullable();

            $table->enum('status', ['Pending', 'Present', 'Rejected'])
                  ->default('Pending');

            $table->timestamps();

            $table->foreign('attendance_session_id')->references('attendance_session_id')
                  ->on('attendance_sessions')->onDelete('cascade');

            $table->foreign('member_id')->references('member_id')
                  ->on('members')->onDelete('cascade');

            $table->foreign('event_id')->references('event_id')
                  ->on('events')->onDelete('cascade');

            $table->foreign('administrator_id')->references('administrator_id')
                  ->on('administrators')->onDelete('set null');

            $table->unique(['member_id', 'attendance_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
