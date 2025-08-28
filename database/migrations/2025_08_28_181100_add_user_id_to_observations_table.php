<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Dodaj kolonu samo ako ne postoji
        if (! Schema::hasColumn('observations', 'user_id')) {
            Schema::table('observations', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index('user_id', 'observations_user_id_index');
            });

            // backfill na admina (promijeni ID po potrebi)
            DB::table('observations')->whereNull('user_id')->update(['user_id' => 1]);

            // bez DBAL-a: pooštri na NOT NULL raw SQL-om (MySQL/MariaDB)
            DB::statement('ALTER TABLE observations MODIFY user_id BIGINT UNSIGNED NOT NULL');
        } else {
            // ako kolona postoji, samo “backfillaj” eventualne NULL-ove
            DB::table('observations')->whereNull('user_id')->update(['user_id' => 1]);
        }

        // 2) Dodaj strani ključ samo ako ne postoji
        $schema = DB::getDatabaseName();
        $fkExists = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'observations'
              AND COLUMN_NAME = 'user_id'
              AND REFERENCED_TABLE_NAME = 'users'
            LIMIT 1
        ", [$schema]);

        if (! $fkExists) {
            Schema::table('observations', function (Blueprint $table) {
                $table->foreign('user_id', 'observations_user_id_fk')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            });
        }

        // 3) Dodaj indeks ako ne postoji (sigurno)
        $idxExists = DB::selectOne("
            SHOW INDEX FROM observations WHERE Key_name = 'observations_user_id_index'
        ");
        if (! $idxExists) {
            Schema::table('observations', function (Blueprint $table) {
                $table->index('user_id', 'observations_user_id_index');
            });
        }
    }

    public function down(): void
    {
        // skini FK ako postoji
        $schema = DB::getDatabaseName();
        $fkExists = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = ?
              AND CONSTRAINT_NAME = 'observations_user_id_fk'
            LIMIT 1
        ", [$schema]);

        Schema::table('observations', function (Blueprint $table) use ($fkExists) {
            if ($fkExists) {
                $table->dropForeign('observations_user_id_fk');
            }
            // skini indeks ako postoji
            $table->dropIndex('observations_user_id_index');
            // i na kraju kolonu (ako želiš reverzirati do kraja)
            if (Schema::hasColumn('observations', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
