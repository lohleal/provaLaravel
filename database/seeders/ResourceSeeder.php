<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // CURSO
            ["name" => "curso.index"],      // 1
            ["name" => "curso.create"],     // 2
            ["name" => "curso.show"],       // 3
            ["name" => "curso.edit"],       // 4
            ["name" => "curso.delete"],     // 5
            // PRODUTO
            ["name" => "produto.index"],      // 6
            ["name" => "produto.create"],     // 7
            ["name" => "produto.show"],       // 8
            ["name" => "produto.edit"],       // 9
            ["name" => "produto.delete"],     // 10
        ];
        DB::table('resources')->insert($data);
    }
}
