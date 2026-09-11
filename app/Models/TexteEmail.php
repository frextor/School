<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `Functions::get_email()` (table `amos_textes_email`) — modèles d'emails par catégorie/langue. */
class TexteEmail extends Model
{
    protected $table = 'amos_textes_email';
    public $timestamps = false;

    protected $fillable = ['lang', 'categorie', 'sujet', 'message', 'statut', 'titre'];

    protected $casts = ['statut' => 'boolean'];
}
