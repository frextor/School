<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `configuration_model` (signatures), table `amos_referentiel_signatures`. */
class Signature extends Model
{
    protected $table = 'amos_referentiel_signatures';
    protected $primaryKey = 'id_signature';
    public $timestamps = false;

    protected $fillable = [
        'civilite', 'nom_directeur', 'id_etablissement', 'principal',
        'signature', 'date_creation', 'date_modification', 'fonction',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    /** Dossier de stockage (disk "public") du fichier de signature (nom seul en base, cf. varchar(100)). */
    public function cheminFichier(): ?string
    {
        return $this->signature ? "signatures/{$this->signature}" : null;
    }
}
