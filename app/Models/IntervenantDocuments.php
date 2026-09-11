<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_intervenant_documents`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class IntervenantDocuments extends Model
{
    protected $table = 'amos_intervenant_documents';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_intervenant',
        'document',
        'size',
        'extension',
    ];
}
