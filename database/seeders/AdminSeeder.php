<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
=======
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@admin.com',
<<<<<<< HEAD
            'password' => Hash::make('admin123'),
=======
            'password' => Hash::make('password'),
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
            'role'     => 'admin',
        ]);
    }
}