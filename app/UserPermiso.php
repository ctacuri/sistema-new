<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPermiso extends Model
{
    protected $table = 'user_permiso';
    protected $fillable = [
        'iduser', 
        'idpermiso',
        'valor'
    ];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User','iduser');
    }

    public function permiso()
    {
        return $this->belongsTo('App\Permiso','idpermiso');
    }
}
