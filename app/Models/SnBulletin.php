<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de `BulletinV2_model::savePdf/getPdfs/getPdf` (table
 * `amos_sn_bulletins`) — le PDF généré est stocké tel quel (colonne
 * `longblob`), une ligne par génération (historique des versions).
 */
class SnBulletin extends Model
{
    protected $table = 'amos_sn_bulletins';
    protected $primaryKey = 'id_bulletin';
    const CREATED_AT = 'date_insert';
    const UPDATED_AT = 'date_update';

    protected $fillable = [
        'pdf', 'annee', 'semestre', 'id_etablissement', 'id_eleve', 'id_niveau', 'session', 'active',
    ];

    protected $hidden = ['pdf'];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }
}
