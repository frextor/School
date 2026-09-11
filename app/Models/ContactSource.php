<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSource extends Model
{
    protected $table = 'amos_contacts_sources';
    protected $primaryKey = 'id_source';
    public $timestamps = false;

    protected $fillable = ['titre', 'id_famille_des_sources'];
}
