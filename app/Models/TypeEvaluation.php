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
}
