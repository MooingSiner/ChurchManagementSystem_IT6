<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmins', function (Blueprint $table) {
            $table->unsignedBigInteger('administrator_id');
            $table->string('permission')->default('full system access');
            $table->timestamps();

            $table->primary('administrator_id');
            $table->foreign('administrator_id')
                ->references('administrator_id')
                ->on('administrators')
                ->cascadeOnDelete();
        });

        DB::statement("
            INSERT INTO superadmins (administrator_id, permission, created_at, updated_at)
            SELECT administrator_id, 'full system access', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM administrators
            WHERE role = 'super_admin'
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('superadmins');
    }
};
