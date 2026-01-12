<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('master_settings', 'settings');

        Schema::table('settings', function (Blueprint $table) {
            $table->string('collection', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('collection', 255)->nullable(false)->change();
        });

        Schema::rename('settings', 'master_settings');
    }
};
