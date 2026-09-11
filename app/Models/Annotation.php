<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
    protected $table = 'amos_annotations';
    protected $primaryKey = 'id_annotations';
    public $timestamps = true;

    protected $fillable = ['contact_id', 'content'];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id_contact');
    }
}
