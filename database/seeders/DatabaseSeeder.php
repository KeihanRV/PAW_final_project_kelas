<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil seeder dosen yang baru dibuat
        $this->call([
            FilkomRecipientSeeder::class,
        ]);
    }
}