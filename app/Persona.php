<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = ['idempresa','nombre','tipo_documento','num_documento','direccion','telefono','email'];

    public function empresa()
    {
        return $this->belongsTo('App\Empresa','idempresa'); //Un proveedor pertenece a una empresa
    }

    /*
    public function proveedor()
    {
        return $this->hasOne('App\Proveedor','id'); //Una persona esta relacionada de manera directa con un solo proveedor
    }

    public function user()
    {
        return $this->hasOne('App\User','id'); //Una persona esta relacionada de manera directa con un solo usuario
    }
    */
}
