<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();

        Batch::factory()->count(1)->for($user)->pending()->has(File::factory()->count(1)->pending(), 'files')->create();
        Batch::factory()->count(2)->for($user)->processing()->has(File::factory()->count(2)->processing(), 'files')->create();
        Batch::factory()->count(3)->for($user)->completed()->has(File::factory()->count(3)->completed(), 'files')->create();
        Batch::factory()->count(1)->for($user)->failed()->has(File::factory()->count(1)->failed(), 'files')->create();
    }
}
