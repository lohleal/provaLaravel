<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["nome" => "CAFÉ QUENTE", "duracao" => 1],
            ["nome" => "CAFÉ GELADO", "duracao" => 2],
            ["nome" => "BEBIDAS", "duracao" => 3],
            ["nome" => "DOCES", "duracao" => 4],
            ["nome" => "SALGADOS", "duracao" => 5],
            ["nome" => "OUTROS", "duracao" => 6],
        ];

        DB::table('cursos')->insert($data);
    }
}