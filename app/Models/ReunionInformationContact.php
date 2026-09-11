<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReunionInformationContact extends Model
{
    protected $table = 'amos_reunions_information_contacts';
    public $timestamps = false;

    protected $fillable = ['id_reunion_information', 'id_contact', 'presence', 'rappel', 'date_operation'];

    protected $casts = [
        'presence' => 'boolean',
        'rappel' => 'boolean',
        'date_operation' => 'datetime',
    ];

    public function reunion()
    {
        return $this->belongsTo(ReunionInformation::class, 'id_reunion_information', 'id_reunion_information');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'id_contact', 'id_contact');
    }
}
