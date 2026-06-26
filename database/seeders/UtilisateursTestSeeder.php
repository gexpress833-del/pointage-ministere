<?php

namespace Database\Seeders;

use App\Models\Bureau;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UtilisateursTestSeeder extends Seeder
{
    /**
     * Crée les bureaux, services et utilisateurs de test.
     * Mot de passe commun pour tous : password
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Bureaux (sans chef pour l'instant)
        $bureaux = [
            ['nom_bureau' => 'Direction'],
            ['nom_bureau' => 'Secrétariat'],
            ['nom_bureau' => 'Bureau Administration et Finances'],
            ['nom_bureau' => 'Bureau Planification et Statistiques'],
            ['nom_bureau' => 'Bureau Formation Professionnelle, Apprentissage et Métiers'],
            ['nom_bureau' => 'Bureau Identification, Insertion et Réinsertion'],
        ];

        foreach ($bureaux as $b) {
            Bureau::firstOrCreate(
                ['nom_bureau' => $b['nom_bureau']],
                $b
            );
        }

        // 2. Services par bureau
        $servicesData = [
            'Direction' => ['Coordination Sous-Provinciale'],
            'Secrétariat' => ['Chef de bureau', 'Rédacteurs', 'Chargé de courrier'],
            'Bureau Administration et Finances' => ['Ressources humaines', 'Comptabilité', 'Logistique'],
            'Bureau Planification et Statistiques' => ['Planification', 'Statistiques'],
            'Bureau Formation Professionnelle, Apprentissage et Métiers' => ['Formation', 'Apprentissage'],
            'Bureau Identification, Insertion et Réinsertion' => ['Identification', 'Insertion'],
        ];

        foreach ($servicesData as $nomBureau => $services) {
            $bureau = Bureau::where('nom_bureau', $nomBureau)->first();
            if (! $bureau) {
                continue;
            }
            foreach ($services as $nomService) {
                Service::firstOrCreate(
                    ['nom_service' => $nomService, 'bureau_id' => $bureau->id],
                    ['nom_service' => $nomService, 'bureau_id' => $bureau->id]
                );
            }
        }

        // 3. Utilisateurs de test
        $bureauDirection = Bureau::where('nom_bureau', 'Direction')->first();
        $bureauSecretariat = Bureau::where('nom_bureau', 'Secrétariat')->first();
        $bureauAdminFinances = Bureau::where('nom_bureau', 'Bureau Administration et Finances')->first();

        $serviceCoord = $bureauDirection ? Service::where('bureau_id', $bureauDirection->id)->first() : null;
        $serviceSecretariat = $bureauSecretariat ? Service::where('bureau_id', $bureauSecretariat->id)->first() : null;
        $serviceRH = $bureauAdminFinances ? Service::where('nom_service', 'Ressources humaines')->where('bureau_id', $bureauAdminFinances->id)->first() : null;

        $users = [
            [
                'name' => 'Admin Test',
                'nom' => 'Admin Test',
                'email' => 'admin@pointage.cd',
                'matricule' => 'ADMIN001',
                'telephone' => '+243000000001',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'bureau_id' => null,
                'service_id' => null,
            ],
            [
                'name' => 'Coordinateur Test',
                'nom' => 'Coordinateur Test',
                'email' => 'coordinateur@pointage.cd',
                'matricule' => 'COORD001',
                'telephone' => '+243000000002',
                'password' => $password,
                'role' => User::ROLE_COORDINATEUR,
                'bureau_id' => $bureauDirection?->id,
                'service_id' => $serviceCoord?->id,
            ],
            [
                'name' => 'Chef Secrétariat',
                'nom' => 'Chef Secrétariat',
                'email' => 'chef.secretariat@pointage.cd',
                'matricule' => 'CHEF-SEC001',
                'telephone' => '+243000000003',
                'password' => $password,
                'role' => User::ROLE_CHEF_BUREAU,
                'bureau_id' => $bureauSecretariat?->id,
                'service_id' => $serviceSecretariat?->id,
            ],
            [
                'name' => 'Chef Admin Finances',
                'nom' => 'Chef Admin Finances',
                'email' => 'chef.adminfinances@pointage.cd',
                'matricule' => 'CHEF-AF001',
                'telephone' => '+243000000004',
                'password' => $password,
                'role' => User::ROLE_CHEF_BUREAU,
                'bureau_id' => $bureauAdminFinances?->id,
                'service_id' => $serviceRH?->id,
            ],
            [
                'name' => 'Agent Jean Dupont',
                'nom' => 'Jean Dupont',
                'email' => 'agent1@pointage.cd',
                'matricule' => 'AGT001',
                'telephone' => '+243000000011',
                'password' => $password,
                'role' => User::ROLE_AGENT,
                'bureau_id' => $bureauSecretariat?->id,
                'service_id' => $serviceSecretariat?->id,
            ],
            [
                'name' => 'Agent Marie Kabongo',
                'nom' => 'Marie Kabongo',
                'email' => 'agent2@pointage.cd',
                'matricule' => 'AGT002',
                'telephone' => '+243000000012',
                'password' => $password,
                'role' => User::ROLE_AGENT,
                'bureau_id' => $bureauSecretariat?->id,
                'service_id' => $serviceSecretariat?->id,
            ],
            [
                'name' => 'Agent Paul Mbala',
                'nom' => 'Paul Mbala',
                'email' => 'agent3@pointage.cd',
                'matricule' => 'AGT003',
                'telephone' => '+243000000013',
                'password' => $password,
                'role' => User::ROLE_AGENT,
                'bureau_id' => $bureauAdminFinances?->id,
                'service_id' => $serviceRH?->id,
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['photo_reference' => null])
            );
        }

        // 4. Attribuer les chefs de bureau aux bureaux
        $chefSecretariat = User::where('email', 'chef.secretariat@pointage.cd')->first();
        $chefAdminFinances = User::where('email', 'chef.adminfinances@pointage.cd')->first();

        if ($chefSecretariat && $bureauSecretariat) {
            $bureauSecretariat->update(['chef_bureau_id' => $chefSecretariat->id]);
        }
        if ($chefAdminFinances && $bureauAdminFinances) {
            $bureauAdminFinances->update(['chef_bureau_id' => $chefAdminFinances->id]);
        }

        $this->command->info('Utilisateurs de test créés. Mot de passe commun : password');
        $this->command->table(
            ['Email', 'Rôle', 'Matricule'],
            [
                ['admin@pointage.cd', 'Administrateur', 'ADMIN001'],
                ['coordinateur@pointage.cd', 'Coordinateur', 'COORD001'],
                ['chef.secretariat@pointage.cd', 'Chef de bureau (Secrétariat)', 'CHEF-SEC001'],
                ['chef.adminfinances@pointage.cd', 'Chef de bureau (Admin Finances)', 'CHEF-AF001'],
                ['agent1@pointage.cd', 'Agent', 'AGT001'],
                ['agent2@pointage.cd', 'Agent', 'AGT002'],
                ['agent3@pointage.cd', 'Agent', 'AGT003'],
            ]
        );
    }
}
