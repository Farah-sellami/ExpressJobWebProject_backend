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
            'DateExecution' => 'required|date',
        ]);
        $clientId = auth('api')->id(); 

        // Créer la demande de service
        $demandeService = DemandeService::create([
            'client_id' => $clientId,
            'professionnel_id' => $request->professionnel_id,
            'DateExecution' => $request->DateExecution,
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

        return response()->json(['message' => 'Statut de la demande mis à jour.'], 200);
    }

    /**
     * Client consulte la liste de ses demandes de service.
     */
    public function consulterDemandesClient()
    {
        $demandes = DemandeService::where('client_id', auth('api')->id())->get();

        return response()->json(['demandes' => $demandes], 200);
    }

    /**
     * Admin consulte toutes les demandes de service.
     */
    public function consulterDemandesAdmin()
    {
        // Vérifier si l'utilisateur est un administrateur
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }
        $demandes = DemandeService::all();

        return response()->json(['demandes' => $demandes], 200);
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
