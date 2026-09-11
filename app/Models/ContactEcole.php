<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de "contact_ecoles" — établissements (campus) auxquels un contact
 * a candidaté, avec un ordre de préférence. Le champ `etablissement` stocke
 * le nom de ville en texte libre côté legacy (pas de vraie FK vers
 * amos_etablissement), conservé tel quel ici.
 */
class ContactEcole extends Model
{
    protected $table = 'amos_contact_ecoles';
    protected $primaryKey = 'id_contact_ecole';
    public $timestamps = false;

    protected $fillable = ['id_contact', 'etablissement', 'ordre'];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'id_contact', 'id_contact');
    }
}
