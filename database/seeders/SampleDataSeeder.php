<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bookmark;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(3)->has(
            Bookmark::factory()->count(2)
        )->create();
    }
}

