<?php

use App\Http\Controllers\AvisController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DemandeServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route pour récupérer l'utilisateur authentifié
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

/* Les routes des categories*/
Route::get('/categories/{id}', [CategorieController::class, 'show']);
Route::get('categories', [CategorieController::class, 'index']); // Tous les utilisateurs peuvent consulter les catégories
Route::middleware(['auth:api'])->group(function () {
    Route::post('categories', [CategorieController::class, 'store']); // Admin peut ajouter des catégories
    Route::put('categories/{id}', [CategorieController::class, 'update']); // Admin peut modifier une catégorie
    Route::delete('categories/{id}', [CategorieController::class, 'destroy']); // Admin peut supprimer une catégorie
});

/* Les routes des avis*/

route::middleware('api')->group(function ()
{Route::resource('avis', AvisController::class);});

// Routes des services
Route::middleware(['auth:api'])->group(function () {
    Route::get('/services/{serviceId}/professionnels', [ServiceController::class, 'getProfessionalsByService']);

    // Routes accessibles uniquement par les administrateurs
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/services', [ServiceController::class, 'store']);        // Ajouter un service
        Route::put('/services/{id}', [ServiceController::class, 'update']);  // Modifier un service
        Route::delete('/services/{id}', [ServiceController::class, 'destroy']); // Supprimer un service
    });
});

// Routes accessibles à tous les utilisateurs (authentifiés ou non)
Route::get('/services', [ServiceController::class, 'index']);                  // Consulter la liste des services
Route::get('/services/{id}', [ServiceController::class, 'show']);              // Consulter un service spécifique
Route::get('/services/category/{categorieId}', [ServiceController::class, 'getServiceByCategorie']); // Consulter les services par catégorie

// Routes des utilisateurs
route::middleware(['auth:api'])->group(function ()
{    // Routes RESTful
    Route::resource('user', UserController::class)->except(['index']);

    // Liste complète des utilisateurs (non paginée, admin uniquement)
    Route::get('user/list', [UserController::class, 'index']);

    // Liste paginée
    Route::get('user-pagination', [UserController::class, 'indexPagination']);

    Route::delete('/{Id}', [UserController::class, 'destroy']);

});
 // Mise à jour du profil
Route::put('profile', [UserController::class, 'updateProfile']); // Client/Professional: Update own profile
  // Suppression du profil
Route::delete('profile', [UserController::class, 'deleteProfile']); // Client/Professional: Delete own profile
// Routes pour les utilisateurs avec pagination
Route::get('/usersByRole', [UserController::class, 'getUsersByRole']);
Route::get('/count-professionnels', [UserController::class, 'countProfessionnels']);
Route::get('/count-clients', [UserController::class, 'countClients']);

// Routes pour les demandes de service
Route::middleware(['auth:api'])->group(function () {
    // Route pour envoyer une demande de service
    Route::post('/demandes/envoyer', [DemandeServiceController::class, 'envoyerDemande']);

    // Route pour changer le statut d'une demande (pour les professionnels)
    Route::put('/demandes/{demandeId}/statut/{nouveauStatut}', [DemandeServiceController::class, 'changerStatutDemande']);

    // Route pour consulter les demandes (clients et administrateurs)
    Route::get('/demandes', [DemandeServiceController::class, 'consulterDemandes']);

    // Route pour récupérer les notifications de l'utilisateur connecté
    Route::get('/notifications', [DemandeServiceController::class, 'getNotifications']);
    Route::get('/demandes/client/{id}', [DemandeServiceController::class, 'getDemandesByClient']);


});

// Routes d'authentification
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::middleware('auth:api')->get('/user', [AuthController::class, 'userProfile']);
// Route pour la vérification de l'email
Route::get('/verify-email/{email}', [AuthController::class,'verifyEmail'])->name('verify.email');
