<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $table = 'amos_formations';
    protected $primaryKey = 'id_formation';
    public $timestamps = false;

    /** Bachelor : les 3 premières formations (id 1 à 3), voir Miracle::get_reunions() côté legacy. */
    public const IDS_BACHELOR = [1, 2, 3];

    /** Master : formations 4 et 5. */
    public const IDS_MASTER = [4, 5];

    protected $fillable = ['niveau', 'description', 'priorite'];

    /** Niveaux rattachés à ce cycle (Maternelle → PS/MS/GS, etc.). */
    public function niveaux()
    {
        return $this->hasMany(Niveau::class, 'id_formation', 'id_formation');
    }

    public function reunions()
    {
        return $this->belongsToMany(
            ReunionInformation::class,
            'amos_reunions_information_formations',
            'id_formation',
            'id_reunion_information'
        );
    }
}
