<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UsuariosSeeder::class);
        $this->call(GestorSeeder::class);
        $this->call(SuperAdminSeeder::class);
        $this->call(CandidatoSeeder::class);
        $this->call(VagasSeeder::class);
        $this->call(CenariosTesteSeeder::class);
    }
}
