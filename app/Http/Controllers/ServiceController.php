<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $services = Service::with('categorie')->get(); // Récupère les services avec les informations de la catégorie
            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la récupération des services.", 500);
        }
    }

     /**
     * Afficher les services d'une catégorie spécifique.
     */
    public function getServiceByCategorie($categorieId)
    {
        try {
            $services = Service::where('categorie_id', $categorieId)->with('categorie')->get();
            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la récupération des services pour cette catégorie.", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est un administrateur
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }

          // Validation des données
          $request->validate([
            'Titre' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'DateCreation' => 'nullable|date',
            'categorie_id' => 'required|exists:categories,id', // Vérifie que l'ID de catégorie existe
        ]);

        try {
            $service = new Service([
                'Titre' => $request->input('Titre'),
                'Description' => $request->input('Description'),
                'DateCreation' => $request->input('DateCreation', now()),
                'categorie_id' => $request->input('categorie_id'),
            ]);
            $service->save();

            return response()->json($service, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Erreur lors de la création du service.",
                'message' => $e->getMessage(), // Message de l'exception
                'trace' => $e->getTrace() // Trace de l'exception pour déboguer
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            $service = Service::with('categorie')->findOrFail($id); // Récupère le service avec la catégorie associée
            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json("Service non trouvé.", 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Vérifier si l'utilisateur est un administrateur
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }

        // Validation des données
        $request->validate([
            'Titre' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'DateCreation' => 'nullable|date',
            'categorie_id' => 'required|exists:categories,id', // Vérifie que l'ID de catégorie existe
        ]);

        try {
            $service = Service::findOrFail($id);
            $service->update([
                'Titre' => $request->input('Titre'),
                'Description' => $request->input('Description'),
                'DateCreation' => $request->input('DateCreation', now()),
                'categorie_id' => $request->input('categorie_id'),
            ]);
            // Charger la relation 'categorie' et retourner la réponse avec les informations complètes
        $service->load('categorie');


            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la mise à jour du service.", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur est un administrateur
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }

        try {
            $service = Service::findOrFail($id);
            $service->delete();
            return response()->json("Service supprimé avec succès.");
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la suppression du service.", 500);
        }
    }

    /**
 * Récupérer les professionnels associés à un service spécifique.
 *
 * @param int $serviceId
 * @return \Illuminate\Http\JsonResponse
 */
public function getProfessionalsByService($serviceId)
{
    try {
        // Trouver le service par son ID
        $service = Service::findOrFail($serviceId);

       // Récupérer les professionnels associés
       $professionnels = $service->professionnels; // Relation correcte

       // Si nécessaire, formater les professionnels
       $formattedProfessionnels = $professionnels->map(function ($pro) {
           return [
               'id' => $pro->id,
               'name' => $pro->name,
               'email' => $pro->email,
               // Ajoutez d'autres colonnes nécessaires
           ];
       });

        // Retourner les professionnels sous forme JSON
        return response()->json([
            'service' => $service->Titre,
            'professionnels' => $professionnels->toArray() // Conversion explicite en tableau
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors de la récupération des professionnels.',
            'message' => $e->getMessage()
        ], 500);
    }
}

}
