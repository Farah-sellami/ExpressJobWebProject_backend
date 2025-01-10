<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
                        'Titre', 
                        'Description', 
                        'DateCreation',  
                        'category_id'
                       ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }
    public function professionnels()
    {
        return $this->belongsTo(user::class, 'service_id');
    }
}
