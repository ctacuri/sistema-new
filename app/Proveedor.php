<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'id', 'idempresa', 'contacto', 'direccion', 'iddepartamento', 'idprovincia', 'iddistrito', 'telefono_contacto'
    ];

    public $timestamps = false;

    public function persona()
    {
        return $this->hasOne('App\Persona','id'); //Un proveedor pertenece a una persona
    }

    public function empresa()
    {
        return $this->belongsTo('App\Empresa','idempresa'); //Un proveedor pertenece a una empresa
    }

    public function departamento()
    {
        return $this->belongsTo('App\Departamento','iddepartamento'); //Un proveedor pertenece a un departamento
    }
    public function provincia()
    {
        return $this->belongsTo('App\Provincia','idprovincia')->where('iddepartamento', $this->departamento->id); //Un proveedor pertenece a una provincia
    }
    public function distrito()
    {
        return $this->belongsTo('App\Distrito','iddistrito')->where([['idprovincia', $this->provincia->id],['iddepartamento', $this->departamento->id]]); //Un proveedor pertenece a un distrito
    }
}
