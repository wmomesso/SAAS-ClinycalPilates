<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'wagner.momesso@criacoder.com.br'],
            [
                'name' => 'Wagner Fernando Momesso',
                'password' => \Illuminate\Support\Facades\Hash::make('W1momesso'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('super-admin');
    }
}
