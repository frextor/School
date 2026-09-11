<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de "épreuves d'admission" (table `amos_epreuves_admission`). */
class EpreuveAdmission extends Model
{
    protected $table = 'amos_epreuves_admission';
    protected $primaryKey = 'id_epreuve_admission';
    public $timestamps = false;

    protected $fillable = ['date_epreuve', 'lieu', 'effectif', 'distanciel', 'url_distanciel'];

    protected $casts = [
        'date_epreuve' => 'datetime',
        'distanciel' => 'boolean',
    ];

    public function formations()
    {
        return $this->belongsToMany(
            Formation::class,
            'amos_epreuves_admission_formations',
            'id_epreuve_admission',
            'id_formation'
        );
    }

    public function inscriptions()
    {
        return $this->hasMany(EpreuveAdmissionEleve::class, 'id_epreuve_admission', 'id_epreuve_admission');
    }

    public function resultats()
    {
        return $this->hasMany(ResultatEpreuveEleve::class, 'id_epreuve_admission', 'id_epreuve_admission');
    }

    public function scopeAVenir($query)
    {
        return $query->where('date_epreuve', '>=', now())->orderBy('date_epreuve');
    }
}
