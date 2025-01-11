<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;


class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Constantes pour les rôles
    const ROLE_ADMIN = 'admin';
    const ROLE_PROFESSIONNEL = 'professionnel';
    const ROLE_CLIENT = 'client';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'isActive',
        'avatar',
        'telephone',
        'adresse',
        'competence',
        'available_hours',
        'note_moyenne',
        'location',
        'service_id', // Clé étrangère vers Service
        'last_connection',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'isActive' => 'boolean',
        ];
    }

     // Relation avec les services proposés par le professionnel
     public function services()
     {
         return $this->hasMany(Service::class);
     }
     
     // Relation avec user
     public function avis()
     {
         return $this->hasMany(Avis::class);
     }
     
    // Relation avec les demandes de services reçues par le professionnel
    public function servicesReçus()
    {
        return $this->hasMany(DemandeService::class, 'professionnelid');
    }

    // Relation avec les demandes de services effectuées par le client
    public function servicesDemandés()
    {
        return $this->hasMany(DemandeService::class, 'clientid');
    }
 
     // Déterminer si un utilisateur est un admin, un professionnel ou un client
     public function isAdmin()
     {
         return $this->role  === self::ROLE_ADMIN;
     }
 
     public function isProfessionnel()
     {
         return $this->role === self::ROLE_PROFESSIONNEL;
     }
 
     public function isClient()
     {
         return $this->role === self::ROLE_CLIENT;
     }

     /** 
     * Get the identifier that will be stored in the subject claim of the JWT. 
     * 
     * @return mixed 
     */ 
    public function getJWTIdentifier() { 
        return $this->getKey(); 
    } 
 
    /** 
     * Return a key value array, containing any custom claims to be added to the JWT. 
     * 
     * @return array 
     */ 
    public function getJWTCustomClaims() { 
        return [
            'role' => $this->role,
        ]; 
    }    

}
