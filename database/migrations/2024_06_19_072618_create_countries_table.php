<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->char('iso', 2);
            $table->string('name', 80);
            $table->char('iso3', 3)->nullable();
            $table->smallinteger('numcode')->nullable();
            $table->integer('phonecode');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name', 'numcode', 'phonecode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
