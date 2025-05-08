<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use App\Models\User; // Import du modèle User
use Illuminate\Support\Facades\Hash; // Import de Hash pour le mot de passe


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Créer un utilisateur admin si aucun utilisateur avec le rôle 'admin' n'existe
        if (!User::where('role', 'admin')->exists()) {
            User::create([
                'name' => 'Admin Principal',
                'email' => 'farah.sellami@gmail.com',  // Remplacez par l'email souhaité
                'password' => Hash::make('azerty'),
                'role' => 'admin',
                'isActive' => true,
                'avatar' => 'https://res.cloudinary.com/defx74d1x/image/upload/v1737276011/icons8-user-100_lxppdo.png',  // Ajoutez une image si nécessaire
                'telephone' => '0000000000',
                'adresse' => 'Sfax City',
                // Champs spécifiques aux professionnels
                'competence' => null,  // Laissez null si l'admin n'a pas de compétence spécifique
                'available_hours' => null,  // Laissez null si non applicable
                'note_moyenne' => 1,  // Laissez null
                'location' => 'Admin Location',
                'service_id' => null,  // Aucune clé étrangère vers Service pour un admin
                // Mettre à jour la dernière connexion
            ]);
        }
    }
}
