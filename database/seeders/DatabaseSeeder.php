<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
<<<<<<< HEAD
{
=======
   {
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
    $this->call([
        AdminSeeder::class,
    ]);
}
}
