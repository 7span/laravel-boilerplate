<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 128)->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 128);
            $table->rememberToken();
            $table->string('firstname', 128)->nullable();
            $table->string('lastname', 128)->nullable();
            $table->string('username', 128)->index()->nullable();
            $table->integer('country_code')->nullable();
            $table->string('mobile_number', 32)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
