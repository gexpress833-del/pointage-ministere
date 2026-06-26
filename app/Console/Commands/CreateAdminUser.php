<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'pointage:create-admin
                            {--email= : Email de l\'administrateur}
                            {--nom= : Nom complet}
                            {--matricule= : Matricule}
                            {--password= : Mot de passe}';

    protected $description = 'Crée ou met à jour le premier administrateur (role, nom, matricule). Ajoutez ensuite la photo de référence dans l\'admin.';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Email');
        $nom = $this->option('nom') ?? $this->ask('Nom complet');
        $matricule = $this->option('matricule') ?? $this->ask('Matricule');
        $password = $this->option('password') ?? $this->secret('Mot de passe');

        $validator = Validator::make([
            'email' => $email,
            'nom' => $nom,
            'matricule' => $matricule,
            'password' => $password,
        ], [
            'email' => 'required|email',
            'nom' => 'required|string|max:255',
            'matricule' => 'required|string|max:50',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'nom' => $nom,
                'matricule' => $matricule,
                'name' => $nom,
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make($password),
            ]);
            $this->info("Utilisateur existant mis à jour : {$email} (administrateur).");
        } else {
            User::create([
                'email' => $email,
                'nom' => $nom,
                'name' => $nom,
                'matricule' => $matricule,
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make($password),
            ]);
            $this->info("Administrateur créé : {$email}.");
        }

        $this->newLine();
        $this->info('Prochaines étapes :');
        $this->line('1. Connectez-vous à l\'admin : '.url('/admin'));
        $this->line('2. Allez dans Utilisateurs, éditez cet utilisateur et ajoutez la photo de référence (obligatoire pour la reconnaissance faciale).');

        return self::SUCCESS;
    }
}
