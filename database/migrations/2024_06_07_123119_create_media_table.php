<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 32);
            $table->string('directory');
            $table->string('original_file_name');
            $table->string('file_name');
            $table->string('extension', 32);
            $table->string('mime_type', 128);
            $table->string('aggregate_type', 32)->index();
            $table->unsignedInteger('size');
            $table->morphs('mediable');
            $table->string('tag')->index();
            $table->timestamps();
            $table->unique(['disk', 'directory', 'file_name', 'extension']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
