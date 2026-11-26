<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Postgres compares text case-sensitively, so `foo@bar.com` and `Foo@bar.com`
     * are two distinct accounts there and a login with the wrong casing fails.
     * The citext type restores the case-insensitive behaviour MySQL gives by
     * default through its `utf8mb4_unicode_ci` collation.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS citext');
        DB::statement('ALTER TABLE users ALTER COLUMN email TYPE citext');
        DB::statement('ALTER TABLE password_reset_tokens ALTER COLUMN email TYPE citext');
    }

    /**
     * The extension itself is left installed: it is a database-wide object that
     * other columns may already depend on.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE users ALTER COLUMN email TYPE varchar(128)');
        DB::statement('ALTER TABLE password_reset_tokens ALTER COLUMN email TYPE varchar(255)');
    }
};
