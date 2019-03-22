<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    //protected $table = 'distritos';
    //protected $primaryKey = 'id';
    protected $fillable = ['iddepartamento','idprovincia','descripcion','condicion'];

    public function provincia()
    {
        return $this->belongsTo('App\Provincia','idprovincia')->where('iddepartamento', $this->iddepartamento); //Un distrito pertenece a una provincia
    }

    /*
    public function empresas()
    {
        return $this->hasMany('App\Empresa','iddistrito')->where([['idprovincia', $this->idprovincia],['iddepartamento', $this->iddepartamento]]);
    }
    public function proveedores()
    {
        return $this->hasMany('App\Proveedor','iddistrito')->where([['idprovincia', $this->idprovincia],['iddepartamento', $this->iddepartamento]]);
    }
    */
}
