<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var array<int, array<string, string>> $countries */
        $countries = json_decode(File::get(database_path('data/countries.json')), true);

        $timestamps = ['created_at' => now(), 'updated_at' => now()];

        $rows = array_map(
            fn (array $country): array => array_merge($country, $timestamps),
            $countries,
        );

        Country::query()->truncate();
        Country::query()->insert($rows);
    }
}
