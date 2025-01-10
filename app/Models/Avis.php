<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id', 
        'professionnel_id',
        'Commentaire',
        'Rate', 'DateAvis',
        'Reponse'
    ];

   // Relation avec le client qui a laissé l'avis
   public function client()
   {
       return $this->belongsTo(User::class, 'client_id');
   }

   // Relation avec le professionnel destinataire de l'avis
   public function professionnel()
   {
       return $this->belongsTo(User::class, 'professionnel_id');
   }
}
