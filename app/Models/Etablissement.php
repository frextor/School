<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `etablissement_model` (table `amos_etablissement`). */
class Etablissement extends Model
{
    protected $table = 'amos_etablissement';
    protected $primaryKey = 'id_etablissement';
    public $timestamps = false;

    protected $fillable = ['nom_etablissement', 'code_ville', 'adresse', 'visible'];

    protected $casts = ['visible' => 'boolean'];

    public function classes()
    {
        return $this->hasMany(Classe::class, 'id_etablissement', 'id_etablissement');
    }

    public function niveaux()
    {
        return $this->belongsToMany(
            Niveau::class,
            'amos_etablissements_niveaux',
            'id_etablissement',
            'id_niveau'
        );
    }

    public function admins()
    {
        return $this->belongsToMany(
            Admin::class,
            'amos_admins_etablissement',
            'id_etablissement',
            'id_admin'
        )->withPivot('etablissement_principal');
    }
}
