<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $name = fake()->name;

        User::query()->create([
            'name'     => $name,
            'email'    => Str::snake($name) . '@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
