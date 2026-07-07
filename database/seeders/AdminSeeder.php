<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@pointage.cd')->first();

        if ($admin) {
            return;
        }

        User::create([
            'name' => 'Administrateur',
            'nom' => 'Administrateur',
            'email' => 'admin@pointage.cd',
            'matricule' => 'ADMIN001',
            'telephone' => '+243000000001',
            'password' => Hash::make('Admin@2026'),
            'role' => User::ROLE_ADMIN,
            'must_change_password' => true,
            'photo_reference' => null,
            'bureau_id' => null,
            'service_id' => null,
        ]);

        $this->command->info('Admin créé : admin@pointage.cd / Admin@2026 (à changer au 1er login)');
    }
}
