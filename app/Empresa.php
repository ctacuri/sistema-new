<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['ruc','nombre','direccion','iddepartamento','idprovincia','iddistrito','telefono','email','condicion'];

    public function users()
    {
        return $this->hasMany('App\User','idempresa'); //Una empresa puede tener varios usuarios
    }
    public function departamento()
    {
        return $this->belongsTo('App\Departamento','iddepartamento'); //Un empresa pertenece a un departamento
    }
    public function provincia()
    {
        return $this->belongsTo('App\Provincia','idprovincia')->where('iddepartamento', $this->departamento->id); //Un empresa pertenece a una provincia
    }
    public function distrito()
    {
        return $this->belongsTo('App\Distrito','iddistrito')->where([['idprovincia', $this->provincia->id],['iddepartamento', $this->departamento->id]]); //Un empresa pertenece a un distrito
    }
}
