<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->mergeDuplicateEventTypes();
        $this->mergeDuplicateMinistries();

        if (! Schema::hasColumn('members_ministries', 'status')) {
            Schema::table('members_ministries', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive', 'left'])
                    ->default('active')
                    ->after('date_joined');
            });
        }

        Schema::table('types', function (Blueprint $table) {
            $table->unique('type_name');
        });

        Schema::table('ministries', function (Blueprint $table) {
            $table->unique('ministry_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            $table->dropUnique(['ministry_name']);
        });

        Schema::table('types', function (Blueprint $table) {
            $table->dropUnique(['type_name']);
        });

        Schema::table('members_ministries', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    private function mergeDuplicateEventTypes(): void
    {
        $duplicates = DB::table('types')
            ->select('type_name', DB::raw('MIN(type_id) as keep_id'))
            ->groupBy('type_name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $duplicateIds = DB::table('types')
                ->where('type_name', $duplicate->type_name)
                ->where('type_id', '!=', $duplicate->keep_id)
                ->pluck('type_id');

            DB::table('events')
                ->whereIn('type_id', $duplicateIds)
                ->update(['type_id' => $duplicate->keep_id]);

            DB::table('types')
                ->whereIn('type_id', $duplicateIds)
                ->delete();
        }
    }

    private function mergeDuplicateMinistries(): void
    {
        $duplicates = DB::table('ministries')
            ->select('ministry_name', DB::raw('MIN(ministry_id) as keep_id'))
            ->groupBy('ministry_name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $duplicateIds = DB::table('ministries')
                ->where('ministry_name', $duplicate->ministry_name)
                ->where('ministry_id', '!=', $duplicate->keep_id)
                ->pluck('ministry_id');

            DB::table('members_ministries')
                ->whereIn('ministry_id', $duplicateIds)
                ->delete();

            DB::table('ministries')
                ->whereIn('ministry_id', $duplicateIds)
                ->delete();
        }
    }
};
