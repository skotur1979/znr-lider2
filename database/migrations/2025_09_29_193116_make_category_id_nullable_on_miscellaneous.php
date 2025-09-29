<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('miscellaneouses')) return;

        // 0) Drop postojeći FK ako postoji (ime može varirati)
        try { DB::statement('ALTER TABLE `miscellaneouses` DROP FOREIGN KEY `miscellaneouses_category_id_foreign`;'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `miscellaneouses` DROP FOREIGN KEY `miscellaneous_category_id_foreign`;'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `miscellaneouses` DROP FOREIGN KEY `category_id_foreign`;'); } catch (\Throwable $e) {}

        // 1) Učini kolonu nullable (i unsigned)
        DB::statement('ALTER TABLE `miscellaneouses` MODIFY `category_id` BIGINT UNSIGNED NULL;');

        // 2) OČISTI BAD DATA: gdje category_id ne postoji u categories -> NULL
        DB::statement('
            UPDATE `miscellaneouses` m
            LEFT JOIN `categories` c ON c.`id` = m.`category_id`
            SET m.`category_id` = NULL
            WHERE m.`category_id` IS NOT NULL AND c.`id` IS NULL;
        ');

        // 3) Osiguraj indeks na category_id (FK ga treba). U MySQL 8 nema "IF NOT EXISTS" za INDEX, pa probamo i ignoriramo grešku.
        try { DB::statement('CREATE INDEX `miscellaneouses_category_id_index` ON `miscellaneouses`(`category_id`);'); } catch (\Throwable $e) {}

        // 4) Dodaj FK s ON DELETE SET NULL
        DB::statement('
            ALTER TABLE `miscellaneouses`
            ADD CONSTRAINT `miscellaneouses_category_id_foreign`
            FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
            ON DELETE SET NULL
            ON UPDATE CASCADE;
        ');
    }

    public function down(): void
    {
        if (! Schema::hasTable('miscellaneouses')) return;

        try { DB::statement('ALTER TABLE `miscellaneouses` DROP FOREIGN KEY `miscellaneouses_category_id_foreign`;'); } catch (\Throwable $e) {}

        // Vrati NOT NULL (po potrebi prilagodi)
        DB::statement('ALTER TABLE `miscellaneouses` MODIFY `category_id` BIGINT UNSIGNED NOT NULL;');

        // (Opcionalno) vrati stari FK s CASCADE
        try {
            DB::statement('
                ALTER TABLE `miscellaneouses`
                ADD CONSTRAINT `miscellaneouses_category_id_foreign`
                FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
                ON DELETE CASCADE ON UPDATE CASCADE;
            ');
        } catch (\Throwable $e) {}
    }
};