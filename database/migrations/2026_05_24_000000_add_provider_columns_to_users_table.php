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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'provider')) {
                $table->string('provider', 50)->nullable()->after('avatar_url');
            }

            if (! Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id', 255)->nullable()->after('provider');
            }
        });

        $hasUniqueIndex = collect(DB::select('SHOW INDEX FROM users'))
            ->contains(fn ($index) => ($index->Key_name ?? null) === 'users_provider_provider_id_unique');

        if (! $hasUniqueIndex) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique(['provider', 'provider_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'provider') || Schema::hasColumn('users', 'provider_id')) {
                $table->dropUnique('users_provider_provider_id_unique');
            }

            if (Schema::hasColumn('users', 'provider')) {
                $table->dropColumn('provider');
            }

            if (Schema::hasColumn('users', 'provider_id')) {
                $table->dropColumn('provider_id');
            }
        });
    }
};
