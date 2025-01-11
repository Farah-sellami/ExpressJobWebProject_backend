<?php

use App\Http\Controllers\AvisController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/* Les routes des categories*/

route::middleware('api')->group(function () 
{Route::resource('categories', CategorieController::class);});
//Une route supplémentaire
//Route::get("/scategoriesByCat/{idcat}",[CategorieController::class,"showSCategorieByCAT"]);

/* Les routes des avis*/

route::middleware('api')->group(function () 
{Route::resource('avis', AvisController::class);});

route::middleware('api')->group(function () 
{Route::resource('services', ServiceController::class);});

route::middleware('api')->group(function () 
{Route::resource('user', UserController::class);});
//supplimentaire
Route::put('profile', [UserController::class, 'updateProfile']); // Client/Professional: Update own profile
Route::delete('profile', [UserController::class, 'deleteProfile']); // Client/Professional: Delete own profile

use App\Http\Controllers\DemandeServiceController;

Route::middleware('auth:api')->group(function () {
    // Route pour envoyer une demande de service
    Route::post('/demandeservice/envoyer', [DemandeServiceController::class, 'envoyerDemande']);

    // Route pour consulter les demandes de service d'un client
    Route::get('/demandeservice/client', [DemandeServiceController::class, 'consulterDemandesClient']);

    // Route pour consulter toutes les demandes de service (réservée à l'administrateur)
    Route::get('/demandeservice/admin', [DemandeServiceController::class, 'consulterDemandesAdmin']);

    // Route pour changer le statut d'une demande de service (réservée au professionnel)
    Route::put('/demandeservice/{demandeId}/statut/{nouveauStatut}', [DemandeServiceController::class, 'changerStatutDemande']);
});


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::get('profile', [AuthController::class, 'userProfile']);
// Route pour la vérification de l'email
Route::get('/verify-email/{email}', [AuthController::class,'verifyEmail'])->name('verify.email');
