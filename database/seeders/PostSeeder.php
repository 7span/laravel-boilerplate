<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();

        if (empty($userIds)) {
            $defaultUser = User::query()->create([
                'first_name' => 'Demo',
                'last_name' => 'User',
                'username' => 'demo_user',
                'email' => 'demo@example.com',
                'password' => 'password',
            ]);

            $userIds = [$defaultUser->id];
        }

        $posts = [];

        for ($i = 0; $i < 200; $i++) {
            $title = fake()->sentence(6);

            $posts[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(6),
                'description' => fake()->paragraphs(3, true),
                'status' => fake()->randomElement(['draft', 'published']),
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Post::query()->insert($posts);
    }
}
