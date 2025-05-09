<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserController extends Controller
{

   /**
     * Consulter tous les utilisateurs (Admin uniquement). sans pagination
     */
    public function index()
    {
        try {
            //Vérifier si l'utilisateur est un administrateur
    if (auth('api')->user()->role !== 'admin') {
        return response()->json(['error' => 'Accès interdit.'], 403);
    }
             $users = User::all();

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // public function indexPagination(Request $request)
    // {
    //     try {
    //         // Vérifier si l'utilisateur est un administrateur
    //         if (auth('api')->user()->role !== 'admin') {
    //             return response()->json(['error' => 'Accès interdit.'], 403);
    //         }

    //         // Valider les paramètres de pagination et de filtrage par rôle
    //         $validated = $request->validate([
    //             'per_page' => 'integer|min:1|max:100', // Limite par page : 1 à 100
    //             'role' => 'nullable|string|in:admin,client,professionnel', // Rôles valides
    //         ]);

    //         // Déterminer la valeur par défaut de la pagination
    //         $perPage = $validated['per_page'] ?? 5;

    //         // Récupérer les utilisateurs, en appliquant le filtre par rôle si nécessaire
    //         $query = User::query();

    //        // Si un rôle est spécifié, on l'applique
    //     if (!empty($validated['role'])) {
    //         $query->where('role', $validated['role']);
    //     }


    //         // Appliquer la pagination
    //         $users = $query->paginate($perPage);

    //         return response()->json([
    //             'users' => $users->items(),
    //             'total_pages' => $users->lastPage(),
    //             'current_page' => $users->currentPage(),
    //             'per_page' => $users->perPage(),
    //             'total_users' => $users->total(),
    //         ]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json(['error' => $e->errors()], 422); // Retourne les erreurs de validation
    //     } catch (Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }



    /**
     * Supprimer un utilisateur (Admin uniquement).
     */
    public function destroy($id)
    {
        try {
            if (auth('api')->user()->role !== 'admin') {
                return response()->json(['error' => 'Accès interdit.'], 403);
            }
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Modifier le profil de l'user connecté.
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            // Validation des données spécifiques selon le rôle
            $validated = $request->validate([
                'name' => 'string|max:255',
                'email' => [
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'nullable|min:8|confirmed',
                'avatar' => 'nullable|image|max:2048', // Avatar limité à 2MB
                'telephone' => 'nullable|string|max:15',
                'adresse' => 'nullable|string|max:255',
            ]);

            // Validation spécifique pour les professionnels
            if ($user->role == 'professionnel') {
                $validated = array_merge($validated, $request->validate([
                    'competence' => 'nullable|string|required_if:role,professionnel',
                    'available_hours' => 'nullable|string|required_if:role,professionnel',
                    'location' => 'nullable|string|required_if:role,professionnel',
                    'service_id' => 'nullable|exists:services,id|required_if:role,professionnel',
                ]));
            }

            // Upload de l'avatar si présent
            if ($request->hasFile('avatar')) {
                $uploadedFileUrl = Cloudinary::upload($request->file('avatar')->getRealPath())->getSecurePath();
                $validated['avatar'] = $uploadedFileUrl;
            }

            // Hash du mot de passe si fourni
            if ($request->has('password')) {
                $validated['password'] = Hash::make($request->password);
            }

            // Mise à jour des informations de l'utilisateur
            $user->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully.',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Consulter le profil de l'utilisateur connecté.
     */
    public function show()
    {
        try {
            $user = Auth::user();
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Modifier les données personnelles de l'utilisateur connecté.
     */
    // public function updateProfile(Request $request)
    // {
    //     try {
    //         $user = Auth::user();

    //         $validated = $request->validate([
    //             'name' => 'string|max:255',
    //             'email' => [
    //                 'email',
    //                 Rule::unique('users')->ignore($user->id),
    //             ],
    //             'password' => 'nullable|min:8|confirmed',
    //             'avatar' => 'nullable|image',
    //             'telephone' => 'nullable|string|max:15',
    //             'adresse' => 'nullable|string|max:255',
    //         ]);

    //         if ($request->has('password')) {
    //             $validated['password'] = Hash::make($request->password);
    //         }

    //         $user->update($validated);

    //         return response()->json(['message' => 'Profile updated successfully.', 'user' => $user]);
    //     } catch (Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Supprimer le compte de l'utilisateur connecté.
     */
    public function deleteProfile()
    {
        try {
            $user = Auth::user();
            $user->delete();
            return response()->json(['message' => 'Profile deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUsersByRole(Request $request)
{
    $role = $request->input('role'); // Le rôle passé dans la requête (par exemple, 'professionnel', 'client')

    $users = User::when($role, function ($query) use ($role) {
        return $query->where('role', $role);
    })
    ->paginate(10); // Pagination des résultats

    return response()->json($users);
}

public function countProfessionnels()
{
    // Compte les utilisateurs avec le rôle 'professionnel'
    $count = User::where('role', 'professionnel')->count();

    return response()->json([
        'count_professionnels' => $count
    ], 200);
}

public function countClients()
{
    // Compte les utilisateurs avec le rôle 'client'
    $count = User::where('role', 'client')->count();

    return response()->json([
        'count_clients' => $count
    ], 200);
}


}
