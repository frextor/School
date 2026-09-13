<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Demande de démo soumise depuis la landing page publique — fonctionnalité
 * nouvelle (pas un portage), table Laravel "moderne" sans rapport avec le
 * schéma legacy `amos_*`.
 */
class DemandeDemo extends Model
{
    protected $table = 'demandes_demo';

    protected $fillable = ['nom', 'etablissement', 'email', 'telephone', 'message', 'traitee'];

    protected $casts = [
        'traitee' => 'boolean',
    ];
}
