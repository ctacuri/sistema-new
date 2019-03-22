<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    //protected $table = 'departamentos';
    //protected $primaryKey = 'id';
    protected $fillable = ['descripcion','condicion'];

    public function provincias()
    {
        return $this->hasMany('App\Provincia','iddepartamento');
    }

    /*
    public function empresas()
    {
        return $this->hasMany('App\Empresa','iddepartamento');
    }
    public function proveedores()
    {
        return $this->hasMany('App\Proveedor','iddepartamento');
    }
    */
}
