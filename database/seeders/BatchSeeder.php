<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\BatchFile;
use App\Models\User;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        Batch::factory()->count(1)->for($user)->pending()->has(BatchFile::factory()->count(1)->pending(), 'files')->create();
        Batch::factory()->count(2)->for($user)->processing()->has(BatchFile::factory()->count(2)->processing(), 'files')->create();
        Batch::factory()->count(3)->for($user)->completed()->has(BatchFile::factory()->count(3)->completed(), 'files')->create();
        Batch::factory()->count(1)->for($user)->failed()->has(BatchFile::factory()->count(1)->failed(), 'files')->create();
    }
}
