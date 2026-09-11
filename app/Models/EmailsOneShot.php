<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_emails_one_shot`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EmailsOneShot extends Model
{
    protected $table = 'amos_emails_one_shot';
    protected $primaryKey = 'id_email';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
        'id_template_mautic',
    ];
}
