<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 0) Odredi fallback vlasnika (admin -> is_admin -> prvi user)
        $ownerId = DB::table('users')->where('role', 'admin')->value('id')
            ?? DB::table('users')->where('is_admin', 1)->value('id')
            ?? DB::table('users')->min('id');

        if (! $ownerId) {
            // Ako stvarno nema korisnika u bazi, digni jasnu grešku.
            throw new \RuntimeException('Nije pronađen niti jedan korisnik u tablici users — ne mogu postaviti user_id.');
        }

        // 1) Dodaj privremeni stupac (NULL) – bez DBAL-a
        if (! Schema::hasColumn('first_aid_kits', 'user_id_tmp')) {
            Schema::table('first_aid_kits', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id_tmp')->nullable()->after('id');
            });
        }

        // 2) Popuni privremeni stupac:
        //    - ako postojeći user_id upućuje na stvarnog usera -> preuzmi ga
        //    - u suprotnom -> postavi na $ownerId (npr. admin)
        DB::statement("
            UPDATE first_aid_kits f
            LEFT JOIN users u ON u.id = f.user_id
            SET f.user_id_tmp = IF(u.id IS NULL, {$ownerId}, f.user_id)
        ");

        // 3) Ukloni FK (ako postoji) na starom user_id, pa ukloni stari stupac user_id (ako postoji)
        Schema::table('first_aid_kits', function (Blueprint $table) {
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
        });

        if (Schema::hasColumn('first_aid_kits', 'user_id')) {
            Schema::table('first_aid_kits', function (Blueprint $table) {
                try { $table->dropColumn('user_id'); } catch (\Throwable $e) {}
            });
        }

        // 4) Preimenuj user_id_tmp -> user_id (NOT NULL + DEFAULT)
        //    Koristimo raw SQL (MySQL/MariaDB) – ne traži DBAL.
        DB::statement("
            ALTER TABLE first_aid_kits
            CHANGE COLUMN user_id_tmp user_id BIGINT UNSIGNED NOT NULL DEFAULT {$ownerId}
        ");

        // 5) Dodaj FK na users(id)
        Schema::table('first_aid_kits', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Pokušaj maknuti FK i (po želji) ostavi stupac.
        // Ne diramo podatke natrag jer je ovo “fix” migracija.
        Schema::table('first_aid_kits', function (Blueprint $table) {
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
            // Ako želiš skroz vratiti stanje (bez user_id), otkomentiraj:
            // try { $table->dropColumn('user_id'); } catch (\Throwable $e) {}
        });
    }
};
