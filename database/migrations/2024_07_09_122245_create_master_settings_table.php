<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->index();
            $table->text('value');
            $table->string('collection', 255);
            $table->boolean('is_public')->default(false)->comment('If key is private, visible only for authenticated user. If public, visible for every user.');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_settings');
    }
};
