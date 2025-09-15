<?php

use Plank\Mediable\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::whenTableDoesntHaveColumn(
            'media',
            'variant_name',
            function (Blueprint $table) {
                $table->string('variant_name', 255)
                    ->after('size')
                    ->nullable();
            }
        );
        Schema::whenTableDoesntHaveColumn(
            'media',
            'original_media_id',
            function (Blueprint $table) {
                $table->foreignIdFor(Media::class, 'original_media_id')
                    ->nullable()
                    ->after('variant_name')
                    ->constrained('media')
                    ->nullOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::whenTableHasColumn(
            'media',
            'original_media_id',
            function (Blueprint $table) {
                // SQLite does not support dropping foreign keys or columns with constraints
                // skip removing this column, the `whenTableDoesntHaveColumn`
                // method should make this safe to play back
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropConstrainedForeignIdFor(Media::class, 'original_media_id');
                }
            }
        );
        Schema::whenTableHasColumn(
            'media',
            'variant_name',
            function (Blueprint $table) {
                $table->dropColumn('variant_name');
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
