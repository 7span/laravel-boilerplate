<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->index();
            $table->char('iso', 2);
            $table->char('iso3', 3)->nullable();
            $table->string('calling_code', 8)->index();
            $table->string('icon', 128)->nullable();
            $table->string('currency', 24)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
