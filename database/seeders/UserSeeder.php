<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "CLIENTE",
                'email' => "cliente@gmail.com",
                "password" => Hash::make('@1234@5678'),
                "role_id" => 1,
            ],
            [
                "name" => "FUNCIONÁRIO",
                'email' => "funcionario@gmail.com",
                "password" => Hash::make('@1234@5678'),
                "role_id" => 2,
            ],
        ];
        DB::table('users')->insert($data);
    }
}


userSeeders