<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    //protected $table = 'provincias';
    //protected $primaryKey = 'id';
    protected $fillable = ['iddepartamento','descripcion','condicion'];

    public function departamento()
    {
        return $this->belongsTo('App\Departamento','iddepartamento'); //Una provincia pertenece a un departamento
    }

    public function distritos()
    {
        return $this->hasMany('App\Distrito','idprovincia')->where('iddepartamento', $this->departamento->id);
    }

    /*
    public function empresas()
    {
        return $this->hasMany('App\Empresa','idprovincia')->where('iddepartamento', $this->departamento->id);
    }
    public function proveedores()
    {
        return $this->hasMany('App\Proveedor','idprovincia')->where('iddepartamento', $this->departamento->id);
    }
    */
}
