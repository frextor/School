<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentEleve extends Model
{
    protected $table = 'amos_document_eleve';
    protected $primaryKey = 'id_document';
    public $timestamps = false;

    protected $fillable = [
        'document_eleve', 'titre', 'description', 'visible',
        'id_eleve', 'id_etablissement', 'id_niveau', 'id_classe', 'id_intervenant',
    ];

    protected $casts = ['visible' => 'boolean'];

    /** Les fichiers sont stockés en base sous forme de chaîne "fichier1.pdf|fichier2.png" (legacy). */
    public function getFichiersAttribute(): array
    {
        return array_filter(explode('|', (string) $this->document_eleve));
    }

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'id_classe', 'id_classe');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    /** Dossier de stockage (disk "public") pour ce document, selon sa cible. */
    public function dossierStockage(): string
    {
        return match (true) {
            $this->id_eleve > 0 => "document_eleve/eleve/{$this->id_eleve}",
            $this->id_classe > 0 => "document_eleve/classe/{$this->id_classe}",
            $this->id_niveau > 0 => "document_eleve/niveau/{$this->id_niveau}",
            $this->id_etablissement > 0 => "document_eleve/etablissement/{$this->id_etablissement}",
            default => 'document_eleve/divers',
        };
    }
}
