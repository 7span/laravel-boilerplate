<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function (): void {
            Role::truncate();
        });

        Role::insert([
            [
                'name' => config('site.roles.admin'),
                'guard_name' => 'api',
            ],
            [
                'name' => config('site.roles.user'),
                'guard_name' => 'api',
            ],
        ]);
    }
}
