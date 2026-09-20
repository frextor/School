<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Dictionnaire des types d'évaluation (ex. "Contrôle continu", "Examen final"). */
class TypeEvaluation extends Model
{
    protected $table = 'amos_type_evaluation';
    protected $primaryKey = 'id_type';
    public $timestamps = false;

    protected $fillable = ['type'];

    /**
     * Types attendus dans une école marocaine (K-12). Sert de jeu de départ au
     * seeder du référentiel et de suggestions sur l'écran du dictionnaire.
     */
    public const TYPES_MAROC = [
        'Contrôle continu',
        'Activités intégrées',
        'Examen de fin de semestre',
    ];
}
