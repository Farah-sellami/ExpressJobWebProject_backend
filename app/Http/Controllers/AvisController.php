<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $avis = Avis::with(['client', 'professionnel'])->get();  // Récupère les avis avec les informations du client et du professionnel
            return response()->json($avis);
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la récupération des avis.", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // Validation des données
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'professionnel_id' => 'required|exists:users,id',
            'Commentaire' => 'nullable|string',
            'Rate' => 'required|integer|min:1|max:5',
            'DateAvis' => 'required|date',
        ]);

        try {
            $avis = new Avis([
                'client_id' => $request->input('client_id'),
                'professionnel_id' => $request->input('professionnel_id'),
                'Commentaire' => $request->input('Commentaire'),
                'Rate' => $request->input('Rate'),
                'DateAvis' => $request->input('DateAvis'),
                'Reponse' => $request->input('Reponse'),
            ]);
            $avis->save();

            return response()->json($avis, 201);
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la création de l'avis.", 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            $avis = Avis::with(['client', 'professionnel'])->findOrFail($id);
            return response()->json($avis);
        } catch (\Exception $e) {
            return response()->json("Avis non trouvé.", 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        // Validation des données
        $request->validate([
            'Commentaire' => 'nullable|string',
            'Rate' => 'required|integer|min:1|max:5',
            'Reponse' => 'nullable|string',
        ]);

        try {
            $avis = Avis::findOrFail($id);
            $avis->update([
                'Commentaire' => $request->input('Commentaire'),
                'Rate' => $request->input('Rate'),
                'Reponse' => $request->input('Reponse'),
            ]);

            return response()->json($avis);
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la mise à jour de l'avis.", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $avis = Avis::findOrFail($id);
            $avis->delete();
            return response()->json("Avis supprimé avec succès.");
        } catch (\Exception $e) {
            return response()->json("Erreur lors de la suppression de l'avis.", 500);
        }
    }
}
