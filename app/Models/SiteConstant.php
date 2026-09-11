<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `Configuration::site()` / `configuration_model->get_constants()` (table `amos_constants`). */
class SiteConstant extends Model
{
    protected $table = 'amos_constants';
    protected $primaryKey = 'id_constant';
    public $timestamps = false;

    protected $fillable = ['title', 'value', 'label'];
}
