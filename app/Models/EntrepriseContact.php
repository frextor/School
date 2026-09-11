<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrepriseContact extends Model
{
    protected $table = 'amos_entreprises_contacts';
    protected $primaryKey = 'id_entreprises_contacts';
    const CREATED_AT = 'date_de_creation';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_entreprise', 'civilite', 'nom', 'prenom', 'telephone', 'email',
        'id_poste', 'id_type_contact', 'commentaire',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise', 'id_entreprise');
    }
}
