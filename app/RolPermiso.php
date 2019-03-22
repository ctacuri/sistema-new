<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    protected $table = 'rol_permiso';
    protected $fillable = [
        'idrol', 
        'idpermiso',
        'valor'
    ];
    public $timestamps = false;

    public function rol()
    {
        return $this->belongsTo('App\Rol','idrol');
    }

    public function permiso()
    {
        return $this->belongsTo('App\Permiso','idpermiso');
    }
}
