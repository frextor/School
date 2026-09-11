<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `config_periode_formation_model`, table `amos_config_periodes_formation`. */
class PeriodeFormation extends Model
{
    protected $table = 'amos_config_periodes_formation';
    protected $primaryKey = 'id_config_periode_formation';
    public $timestamps = false;

    protected $fillable = ['annee_scolaire', 'periode', 'nb_heure_annuel', 'diplome_rncp', 'code_diplome'];

    public function niveaux()
    {
        return $this->belongsToMany(
            Niveau::class,
            'amos_config_periodes_formation_niveaux',
            'id_config_periode_formation',
            'id_niveau'
        );
    }

    public function classes()
    {
        return $this->belongsToMany(
            Classe::class,
            'amos_config_periodes_formation_classes',
            'id_config_periode_formation',
            'id_classe'
        );
    }

    public function periodesTrimestrielles()
    {
        return $this->hasMany(PeriodeFormationTrimestre::class, 'id_config_periode_formation', 'id_config_periode_formation');
    }
}
