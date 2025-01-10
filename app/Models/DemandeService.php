<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeService extends Model
{
    use HasFactory;
    //
    protected $fillable = ['DateDemande', 'Statut', 'DateExecution',  'client_id', 'professionnel_id'];

    /**
     * Client ayant créé la demande.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Professionnel associé à la demande.
     */
    public function professionnel()
    {
        return $this->belongsTo(User::class, 'professionnel_id');
    }

    /**
     * Mettre à jour le statut.
     */
    public function updateStatut($nouveauStatut)
    {
        $this->Statut = $nouveauStatut;
        $this->save();
    }
}
