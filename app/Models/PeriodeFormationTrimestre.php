<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeFormationTrimestre extends Model
{
    protected $table = 'amos_config_periodes_formation_periodes_trimestrielles';
    protected $primaryKey = 'id_config_periode_formation_periode_trimestrielle';
    public $timestamps = false;

    protected $fillable = ['id_config_periode_formation', 'periode', 'nb_heure'];

    public function periodeFormation()
    {
        return $this->belongsTo(PeriodeFormation::class, 'id_config_periode_formation', 'id_config_periode_formation');
    }
}
