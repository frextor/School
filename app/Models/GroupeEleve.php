<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage du "groupe" élève (table `amos_eleves_groupes`, géré par `eleves_model` côté legacy). */
class GroupeEleve extends Model
{
    protected $table = 'amos_eleves_groupes';
    protected $primaryKey = 'id_groupe';
    public $timestamps = false;

    protected $fillable = ['nom_groupe', 'date_creation', 'date_modification'];

    protected $casts = [
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];

    public function eleves()
    {
        return $this->belongsToMany(
            Eleve::class,
            'amos_eleves_groupes_refs',
            'id_groupe',
            'id_eleve'
        );
    }
}
