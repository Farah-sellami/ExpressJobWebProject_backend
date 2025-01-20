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
          // Vérifier si l'utilisateur est un administrateur
    if (auth('api')->user()->role !== 'admin') {
        return response()->json(['error' => 'Accès interdit.'], 403);
    }

    // Validation des données
    $validator = Validator::make($request->all(), [
        'Titre' => 'required|unique:categories,Titre',
        'Description' => 'nullable|string',
        'image' => 'nullable|image|'
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => 'Validation échouée', 'messages' => $validator->errors()]);
    }

    try {
        // Gérer l'upload de l'image avec Cloudinary
        $imageUrl = null;
        if ($request->hasFile('image')) {
            // Upload de l'image sur Cloudinary
            $image = $request->file('image');
            $uploadResult = Cloudinary::upload($image->getRealPath())->getSecurePath();
            $imageUrl = $uploadResult; // L'URL sécurisée de l'image uploadée
        }

        // Création de la catégorie avec les données validées
        $categorie = new Categorie([
            'Titre' => $request->input('Titre'),
            'Description' => $request->input('Description'),
            'image' => $imageUrl // Utiliser l'URL de l'image
        ]);

        // Sauvegarde de la catégorie dans la base de données
        $categorie->save();

        return response()->json($categorie);

    } catch (\Exception $e) {
        return response()->json(
            ['error' => 'Insertion impossible',
        'message' => $e->getMessage(),
        'trace' => $e->getTrace()
    ], 500);    }
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
          // Vérifier si l'utilisateur est un administrateur
          if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }
             // Validation des données
             $validator = Validator::make($request->all(), [
                'Titre' => 'required|unique:categories,Titre,' . $id,
                'Description' => 'nullable|string',
                'image' => 'nullable|file|image'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => 'Validation échouée', 'messages' => $validator->errors()]);
            }
            try {
                // Récupérer la catégorie à mettre à jour
                $categorie = Categorie::findOrFail($id);
        
                // Si une nouvelle image est fournie, la télécharger sur Cloudinary
                if ($request->hasFile('image')) {
                    // Supprimer l'ancienne image si nécessaire
                    if ($categorie->image) {
                        // Extraire l'identifiant public de l'image existante depuis l'URL
                        $publicId = basename($categorie->image, '.' . pathinfo($categorie->image, PATHINFO_EXTENSION));
                        Cloudinary::destroy($publicId);
                    }
        
                    // Télécharger la nouvelle image sur Cloudinary
                    $uploadedImage = Cloudinary::upload($request->file('image')->getRealPath(), [
                        'folder' => 'categories', // Dossier où l'image sera stockée
                    ]);
        
                    // Mettre à jour l'URL de l'image dans la catégorie
                    $categorie->image = $uploadedImage->getSecurePath();
                }
        
                // Mettre à jour les autres champs
                $categorie->Titre = $request->input('Titre');
                $categorie->Description = $request->input('Description', $categorie->Description);
                $categorie->save();
        
                return response()->json($categorie);
        
            } catch (\Exception $e) {
                return response()->json(['error' => 'Problème lors de la modification', 'message' => $e->getMessage()], 500);
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
