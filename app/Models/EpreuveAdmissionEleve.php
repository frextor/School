<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpreuveAdmissionEleve extends Model
{
    protected $table = 'amos_epreuves_admission_eleves';
    protected $primaryKey = 'id_epreuve_admission_eleve';
    const UPDATED_AT = 'date_operation';
    const CREATED_AT = null;

    protected $fillable = ['id_epreuve_admission', 'id_eleve', 'presence'];

    protected $casts = ['presence' => 'boolean'];

    public function epreuve()
    {
        return $this->belongsTo(EpreuveAdmission::class, 'id_epreuve_admission', 'id_epreuve_admission');
    }

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }
}
