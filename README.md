# Système de gestion de présence - Coordination Sous-Provinciale

Application de gestion de présence des agents pour la Coordination Sous-Provinciale du Ministère de la Formation Professionnelle (RDC).

## Technologies

- **Backend:** Laravel 11
- **Admin:** Filament 5
- **Base de données:** MySQL
- **Frontend:** Blade + JavaScript
- **Reconnaissance faciale:** face-api.js
- **PDF:** Laravel DomPDF
- **Styles:** Tailwind CSS

## Prérequis

- PHP 8.2+
- Composer
- MySQL 8+
- Node.js / npm (optionnel, pour assets)

## Installation

### 1. Cloner et dépendances

```bash
cd POINTAGE
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Base de données

Configurer `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pointage
DB_USERNAME=root
DB_PASSWORD=
```

Créer la base puis lancer les migrations :

```bash
php artisan migrate
```

### 3. Stockage et lien symbolique

```bash
php artisan storage:link
```

Les photos de référence et les captures de présence sont stockées dans `storage/app/`.

### 4. Modèles face-api.js (reconnaissance faciale)

Télécharger les modèles dans `public/models/` en une commande :

```bash
php artisan pointage:download-face-weights
```

Les fichiers nécessaires sont téléchargés depuis le dépôt officiel face-api.js. Vous pouvez aussi les copier à la main depuis https://github.com/justadudewhohacks/face-api.js/tree/master/weights (tiny_face_detector, face_landmark_68, face_recognition).

### 5. Paramètres par défaut

```bash
php artisan db:seed --class=ParametresSeeder
```

Crée les paramètres (heure limite retard, seuil reconnaissance). Vous pouvez aussi les modifier dans l’admin (Paramètres).

### 6. Premier utilisateur administrateur

**Option A – Commande dédiée (recommandée)**  
Crée ou met à jour un administrateur (role, nom, matricule) :

```bash
php artisan pointage:create-admin
```

Renseigner email, nom, matricule et mot de passe (en interactif ou via options `--email`, `--nom`, `--matricule`, `--password`). Ensuite, connectez-vous à `/admin`, allez dans **Utilisateurs**, éditez cet utilisateur et **ajoutez la photo de référence** (obligatoire pour la reconnaissance faciale).

**Option B – Filament puis mise à jour manuelle**

```bash
php artisan make:filament-user
```

Puis en base : `UPDATE users SET role = 'administrateur', nom = 'Votre Nom', matricule = 'ADMIN001' WHERE email = 'votre@email.com';` et ajouter la photo de référence dans l’admin.

### 7. Lancer l’application

```bash
php artisan serve
```

- Application : http://localhost:8000
- Admin : http://localhost:8000/admin
- Signature présence : http://localhost:8000/presence/sign (après connexion)

## Structure organisationnelle

- **Direction** : Coordinateur Sous-Provincial
- **Secrétariat** : Chef de bureau, Rédacteurs, Chargé de courrier
- **Bureau Administration et Finances** : RH, Comptabilité, Logistique
- **Bureau Planification et Statistiques**
- **Bureau Formation Professionnelle, Apprentissage et Métiers**
- **Bureau Identification, Insertion et Réinsertion**

Chaque bureau a un chef et des agents. Les **bureaux** et **services** se créent dans l’admin (Organisation).

## Rôles

| Rôle | Droits |
|------|--------|
| **Administrateur** | Création des comptes (avec photo de référence), ouverture/fermeture des sessions, tous les rapports, gestion bureaux/services |
| **Coordinateur** | Consultation de toutes les statistiques et rapports |
| **Chef de bureau** | Voir les agents de son bureau, présences du bureau, rapports du bureau |
| **Agent** | Signer sa présence, consulter son historique |

Tout le monde signe sa présence par **reconnaissance faciale**.

## Fonctionnement journalier

1. L’**administration** ouvre la session du jour (Sessions de présence → « Ouvrir session aujourd’hui »).
2. Les agents vont sur **Signer présence** (menu ou `/presence/sign`).
3. La caméra s’active ; le visage est comparé à la photo de référence.
4. En cas de correspondance, la présence est enregistrée (heure, statut présent/retard).
5. L’administration ferme la session.
6. Le **rapport PDF journalier** se télécharge depuis la liste des sessions (action « PDF journalier »).

## Rapports PDF

- **Journalier** : depuis une session (ligne « PDF journalier ») → liste des présences du jour.
- **Mensuel** : URL `/reports/monthly?year=2025&month=3` (et optionnellement `bureau_id=` pour un bureau). Réservé aux administrateurs / coordinateurs ; les chefs de bureau voient uniquement leur bureau.

## Sécurité

- Pas de double signature (un enregistrement par utilisateur et par session).
- Pas de signature si la session est fermée ou s’il n’y a pas de session ouverte pour le jour.
- Vérification côté serveur : session ouverte, date du jour, unicité (session_id + user_id).
- La reconnaissance faciale est effectuée côté client (face-api.js) ; la présence n’est enregistrée qu’après envoi au serveur avec les contrôles ci-dessus.

## Fichiers principaux

- **Migrations :** `database/migrations/` (users, bureaux, services, sessions_presences, presences)
- **Modèles :** `app/Models/` (User, Bureau, Service, SessionPresence, Presence)
- **Admin Filament :** `app/Filament/Resources/` (Users, Bureaux, Services, SessionsPresence)
- **Signature présence :** `resources/views/presence/sign.blade.php`, `app/Http/Controllers/PresenceController.php`
- **PDF :** `app/Services/PresenceReportPdf.php`, `resources/views/pdf/`
- **Widget dashboard :** `app/Filament/Widgets/PresenceStatsWidget.php`
- **Paramètres :** table `parametres`, modèle `App\Models\Parametre`, ressource Filament Paramètres (admin uniquement)
- **Policies :** `app/Policies/` (User, Bureau, Service, SessionPresence, Presence, Parametre) — utilisées automatiquement par Filament

## Licence

Usage interne – Ministère de la Formation Professionnelle, RDC.
