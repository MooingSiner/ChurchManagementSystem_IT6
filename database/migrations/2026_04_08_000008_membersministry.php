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
    Schema::create('members_ministries', function (Blueprint $table) {
        $table->id('members_ministry_id');
        $table->unsignedBigInteger('member_id');
        $table->unsignedBigInteger('ministry_id');
        $table->string('role_in_ministry')->nullable();
        $table->date('date_joined')->nullable();
        $table->enum('status', ['active', 'inactive', 'left'])->default('active');
        $table->timestamps();


        $table->foreign('member_id')
              ->references('member_id')
              ->on('members')
              ->onDelete('cascade');

        $table->foreign('ministry_id')
              ->references('ministry_id')
              ->on('ministries')
              ->onDelete('cascade');

        $table->unique(['member_id', 'ministry_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members_ministries');
    }
};
