<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories=Categorie::all();
            return response()->json($categories);
        } catch (\Exception $e) {
        return response()->json("probleme de récupération de la liste des catégories");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'Titre' => 'required|unique:categories,Titre',
            'Description' => 'nullable|string',
            'image' => 'nullable|string|url', // plus file/image ici
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation échouée', 'messages' => $validator->errors()]);
        }

        try {
            $categorie = new Categorie([
                'Titre' => $request->input('Titre'),
                'Description' => $request->input('Description'),
                'image' => $request->input('image'), // image déjà hébergée
            ]);

            $categorie->save();

            return response()->json($categorie);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Insertion impossible',
                'message' => $e->getMessage(),
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
            $categorie=Categorie::findOrFail($id);
            return response()->json($categorie);

        } catch (\Exception $e) {
            return response()->json("probleme de récupération des données");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'Titre' => 'required|unique:categories,Titre,' . $id,
            'Description' => 'nullable|string',
            'image' => 'nullable|string|url', // on attend une URL Cloudinary
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation échouée', 'messages' => $validator->errors()]);
        }

        try {
            $categorie = Categorie::findOrFail($id);

            // Mise à jour simple, car image déjà uploadée sur Cloudinary par le frontend
            $categorie->Titre = $request->input('Titre');
            $categorie->Description = $request->input('Description', $categorie->Description);
            $categorie->image = $request->input('image', $categorie->image); // image optionnelle

            $categorie->save();

            return response()->json($categorie);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Problème lors de la modification',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            // Vérifier si l'utilisateur est un administrateur
            if (auth('api')->user()->role !== 'admin') {
                return response()->json(['error' => 'Accès interdit.'], 403);
            }

            $categorie=Categorie::findOrFail($id);
            $categorie->delete();
            return response()->json("catégorie supprimée avec succes");
            } catch (\Exception $e) {
            return response()->json("probleme de suppression de catégorie");
            }
    }
}
