<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = config('user-seeder');

        if (!empty($users)) {
            foreach (collect($users) as $user) {
                User::create([
                    'name' => $user->name,
                    'email' => $user['email'],
                    'email_verified_at' => $user['email_verified_at'],
                    'password' => $user['password'],
                ]);
            }
        }
    }
}
