<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = ['Titre', 'Description' , 'image'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
