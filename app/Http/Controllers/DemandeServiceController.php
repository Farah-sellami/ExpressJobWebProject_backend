<?php

namespace App\Http\Controllers;

use App\Models\DemandeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DemandeServiceNotification;
class DemandeServiceController extends Controller
{
        /**
     * Client envoie une demande à un professionnel spécifique.
     */
    public function envoyerDemande(Request $request)
    {
        // Validation de la requête
    $request->validate([
        'professionnel_id' => 'required|exists:users,id',
    ]);
    
    // Utiliser la date actuelle du système (le serveur)
    $dateExecution = now(); // Cela récupère la date et l'heure actuelles du serveur

    $clientId = auth('api')->id(); 

    // Créer la demande de service avec la date du système
    $demandeService = DemandeService::create([
        'client_id' => $clientId,
        'professionnel_id' => $request->professionnel_id,
        'DateExecution' => $dateExecution, // Remplace ici par la date système
    ]);

        // Trouver le professionnel à notifier
        $professionnel = User::find($request->professionnel_id);

        // Envoyer la notification au professionnel par email
        $professionnel->notify(new DemandeServiceNotification($demandeService));

        return response()->json(['message' => 'Demande envoyée avec succès.'], 200);
    }

    /**
     * Professionnel confirme ou refuse une demande.
     */
    public function changerStatutDemande($demandeId, $nouveauStatut)
    {
        $demandeService = DemandeService::findOrFail($demandeId);
        
        // Vérifier si le professionnel est celui qui reçoit la demande
        if ($demandeService->professionnel_id != auth('api')->id()) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cette demande.'], 403);
        }
    
        // Valider le statut
        $statutsValides = ['en_attente', 'terminé', 'annulé'];
        if (!in_array($nouveauStatut, $statutsValides)) {
            return response()->json(['error' => 'Statut invalide.'], 400);
        }
    
        // Mettre à jour le statut
        $demandeService->updateStatut($nouveauStatut);
    
        // Renvoyer le statut mis à jour dans la réponse
        return response()->json([
            'message' => 'Statut de la demande mis à jour.',
            'demande' => $demandeService
        ], 200);
    }
    

/**
 * Consulter les demandes de service en fonction du rôle de l'utilisateur.
 */
public function consulterDemandes()
{
    $user = auth('api')->user(); // Récupère l'utilisateur connecté

      // Si l'utilisateur est un client, il peut voir seulement ses propres demandes
      if ($user->role === 'client') {
        $demandes = DemandeService::with(['client', 'professionnel'])  // Charger les informations liées au client et au professionnel
            ->where('client_id', $user->id)
            ->get();
        return response()->json(['demandes' => $demandes], 200);
    }

    // Si l'utilisateur est un professionnel, il peut voir les demandes qui le concernent
    if ($user->role === 'professionnel') {
        $demandes = DemandeService::with(['client', 'professionnel'])  // Charger les informations liées au client et au professionnel
            ->where('professionnel_id', $user->id)
            ->get();
        return response()->json(['demandes' => $demandes], 200);
    }

    // Si l'utilisateur est un administrateur, il peut voir toutes les demandes
    if ($user->role === 'admin') {
        $demandes = DemandeService::with(['client', 'professionnel'])  // Charger les informations liées au client et au professionnel
            ->get();
        return response()->json(['demandes' => $demandes], 200);
    }

    // Si l'utilisateur n'est ni un client, ni un professionnel, ni un administrateur, accès refusé
    return response()->json(['error' => 'Accès interdit.'], 403);
}


    /**
     * Récupérer les notifications de l'utilisateur connecté.
     */
    public function getNotifications()
    {
        // Récupérer toutes les notifications pour l'utilisateur connecté
        $notifications = auth('api')->user()->notifications;

        return response()->json(['notifications' => $notifications], 200);
    }
}
