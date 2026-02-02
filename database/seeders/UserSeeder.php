<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@pcr.ac.id'],
            [
                'name' => 'Admin Prodi',
                'password' => bcrypt('password'),
                'roles' => ['admin'],
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'koor@pcr.ac.id'],
            [
                'name' => 'Koordinator KP',
                'password' => bcrypt('password'),
                'roles' => ['koordinator'],
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'dosen@pcr.ac.id'],
            [
                'name' => 'Dosen Pembimbing',
                'password' => bcrypt('password'),
                'roles' => ['dosen'],
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'dosenkoor@pcr.ac.id'],
            [
                'name' => 'Dosen & Koordinator',
                'password' => bcrypt('password'),
                'roles' => ['dosen', 'koordinator'],
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'mahasiswa@mahasiswa.pcr.ac.id'],
            [
                'name' => 'Mahasiswa Test',
                'password' => bcrypt('password'),
                'roles' => ['mahasiswa'],
            ]
        );
    }
}
