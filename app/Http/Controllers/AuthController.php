<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
// use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
// use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Notifications\VerifyEmailNotification;
// use App\Mail\VerifyEmailMail;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AuthController extends Controller
{
  /**
     * Login the user and return a JWT token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // L'utilisateur authentifié après l'obtention du token
            $user = auth()->user();

            return $this->createNewToken($token, $user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|string|in:admin,professionnel,client',
            'avatar' => 'nullable|string|url',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
            // Champs spécifiques aux professionnels
            'competence' => 'nullable|string|required_if:role,professionnel',
            'available_hours' => 'nullable|string|required_if:role,professionnel',
            'location' => 'nullable|string|required_if:role,professionnel',
            'note_moyenne' => 'nullable|numeric|min:0|max:5|prohibited',
            'service_id' => 'nullable|exists:services,id|required_if:role,professionnel',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

       // Prépare les données pour la création de l'utilisateur
       $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
        'avatar' => $request->avatar,
        'telephone' => $request->telephone,
        'adresse' => $request->adresse,
        'competence' => $request->competence,
        'available_hours' => $request->available_hours,
        'location' => $request->location,
        'isActive' => false,
    ];

     // Gestion de l'avatar avec Cloudinary
    //  if ($request->hasFile('avatar')) {
    //     try {
    //         $uploadedFileUrl = Cloudinary::upload($request->file('avatar')->getRealPath())->getSecurePath();
    //         $userData['avatar'] = $uploadedFileUrl;
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Échec du téléchargement de l\'avatar.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // Ajoute `service_id` seulement si l'utilisateur est un professionnel
    if ($request->role === 'professionnel') {
        $userData['service_id'] = $request->service_id;
    }

    // Création de l'utilisateur
    $user = User::create($userData);


        // Générer l'URL de vérification
        $verificationUrl = route('verify.email', ['email' => $user->email]);
        // Envoi de l'e-mail de vérification via une notification
        try {
            $user->notify(new VerifyEmailNotification($verificationUrl));  // Envoi de la notification

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Utilisateur créé, mais l\'envoi de l\'e-mail de vérification a échoué.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Utilisateur créé avec succès. Veuillez vérifier votre e-mail.',
            'user' => $user,
        ], 201);
    }

    /**
     * Logout the user and invalidate the token.
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out.']);
    }

    /**
     * Refresh the JWT token.
     */
    public function refresh()
    {
        try {
            $token = auth('api')->refresh();
            return $this->createNewToken($token);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token refresh failed'], 401);
        }
    }

    /**
     * Get the authenticated user profile.
     */
    public function userProfile()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Helper function to format the token response.
     */
    protected function createNewToken($token, $user)
    {
        $user = $user ?? auth('api')->user();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }

    public function verifyEmail($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        // Activer l'utilisateur
        $user->isActive = true;
        $user->save();

        return response()->json(['message' => 'Votre compte a été vérifié avec succès.'], 200);
    }


}
