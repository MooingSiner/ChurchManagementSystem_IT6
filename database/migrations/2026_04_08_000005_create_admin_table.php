<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->unsignedBigInteger('administrator_id');
            $table->string('permission')->default('dashboard, events, attendance, read-only members');
            $table->timestamps();

            $table->primary('administrator_id');
            $table->foreign('administrator_id')
                ->references('administrator_id')
                ->on('administrators')
                ->cascadeOnDelete();
        });

        DB::statement("
            INSERT INTO admins (administrator_id, permission, created_at, updated_at)
            SELECT administrator_id, 'dashboard, events, attendance, read-only members', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM administrators
            WHERE role = 'admin'
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
