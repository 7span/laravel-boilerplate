<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::whenTableDoesntHaveColumn(
            'media',
            'alt',
            function (Blueprint $table) {
                $table->text('alt')->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::whenTableHasColumn(
            'media',
            'alt',
            function (Blueprint $table) {
                $table->dropColumn('alt');
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return config('mediable.connection_name', parent::getConnection());
    }
};
