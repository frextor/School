<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReunionInformation extends Model
{
    protected $table = 'amos_reunions_information';
    protected $primaryKey = 'id_reunion_information';
    public $timestamps = false;

    protected $fillable = ['date', 'lieu', 'effectif', 'distanciel', 'url_distanciel'];

    protected $casts = [
        'date' => 'datetime',
        'distanciel' => 'boolean',
    ];

    public function formations()
    {
        return $this->belongsToMany(
            Formation::class,
            'amos_reunions_information_formations',
            'id_reunion_information',
            'id_formation'
        );
    }

    public function scopeAVenir($query)
    {
        return $query->where('date', '>=', now())->orderBy('date');
    }

    public function scopePassees($query)
    {
        return $query->where('date', '<', now())->orderByDesc('date');
    }

    public function inscriptions()
    {
        return $this->hasMany(ReunionInformationContact::class, 'id_reunion_information', 'id_reunion_information');
    }

    public function inscriptionsPresentes()
    {
        return $this->inscriptions()->where('presence', true);
    }
}
