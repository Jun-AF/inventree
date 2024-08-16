<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Admin",
            "email" => "admin@inventree.test",
            "password" => Hash::make("password"),
            "role" => "Super Admin",
            "remember_token" => Str::random(10),
        ]);

        User::create([
            "name" => "planner test",
            "email" => "planner-test@inventree.test",
            "password" => Hash::make("password"),
            "role" => "Planner",
            "remember_token" => Str::random(10),
        ]);

        User::create([
            "name" => "tuk test",
            "email" => "tuk-test@inventree.test",
            "password" => Hash::make("password"),
            "role" => "Super Admin",
            "remember_token" => Str::random(10),
        ]);

        User::create([
            "name" => "harvesting test",
            "email" => "harvesting-test@inventree.test",
            "password" => Hash::make("password"),
            "role" => "Super Admin",
            "remember_token" => Str::random(10),
        ]);
    }
}
