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


class UserController extends Controller
{

   /**
     * Consulter tous les utilisateurs (Admin uniquement).
     */
    public function index()
    {
        try {
            // Vérifier si l'utilisateur est un administrateur
    if (auth('api')->user()->role !== 'admin') {
        return response()->json(['error' => 'Accès interdit.'], 403);
    }
             $users = User::all();
          
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un utilisateur (Admin uniquement).
     */
    public function destroy($id)
    {
        try {
            $this->authorize('isAdmin', User::class);
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Modifier le profil de l'administrateur connecté.
     */
    public function updateAdminProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'name' => 'string|max:255',
                'email' => [
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'nullable|min:8|confirmed',
                'avatar' => 'nullable|image',
                'telephone' => 'nullable|string|max:15',
                'adresse' => 'nullable|string|max:255',
            ]);

            if ($request->has('password')) {
                $validated['password'] = Hash::make($request->password);
            }

            $user->update($validated);

            return response()->json(['message' => 'Admin profile updated successfully.', 'user' => $user]);
        } catch (Exception $e) {
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
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'string|max:255',
                'email' => [
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'nullable|min:8|confirmed',
                'avatar' => 'nullable|image',
                'telephone' => 'nullable|string|max:15',
                'adresse' => 'nullable|string|max:255',
            ]);

            if ($request->has('password')) {
                $validated['password'] = Hash::make($request->password);
            }

            $user->update($validated);

            return response()->json(['message' => 'Profile updated successfully.', 'user' => $user]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

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
}
