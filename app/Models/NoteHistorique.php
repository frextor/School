<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteHistorique extends Model
{
    protected $table = 'amos_sn_base_notes_historique';
    protected $primaryKey = 'id_sn_base_notes_historique';
    const CREATED_AT = 'date';
    const UPDATED_AT = null;

    protected $fillable = ['id_note', 'ancienne_note', 'nouvelle_note', 'raison', 'session', 'id_admin'];

    public function note()
    {
        return $this->belongsTo(Note::class, 'id_note', 'id_note');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
