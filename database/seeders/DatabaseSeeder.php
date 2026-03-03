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
>>>>>>> 33baeb89651948608801199ef8dceec70f723e41
    $this->call([
        AdminSeeder::class,
    ]);
}
}
