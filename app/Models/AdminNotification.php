<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $table = 'amos_admins_notifications';
    protected $primaryKey = 'id_notification';
    public $timestamps = false;

    protected $fillable = ['id_admin', 'event', 'titre', 'message', 'lue', 'lien', 'picto', 'date'];

    protected $casts = [
        'lue' => 'boolean',
        'date' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
