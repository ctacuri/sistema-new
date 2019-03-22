<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $fillable = ['idempresa','nombre','descripcion','condicion'];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany('App\User','idrol'); //Un rol puede tener varios usuarios
    }

    public function permisosPersonalizados()
    {
        return $this->hasMany('App\RolPermiso','idrol');
    }

    public function permisos()
    {
        $permisos = null;
        
        $permisos = Permiso::where('condicion','=','1')
        ->leftJoin('rol_permiso',function ($join) {
            $join->on('permisos.id','=','rol_permiso.idpermiso')
            ->where('rol_permiso.idrol','=',$this->id);
        })
        ->selectRaw('permisos.id,permisos.codigo,permisos.descripcion,ifnull(rol_permiso.valor,permisos.valor) as valor')
        ->get();

        return $permisos;
    }

    public function privilegios()
    {
        /*
        $privilegios = [];
        $allPermisos = Permiso::where('condicion','=','1')->pluck('valor','codigo')->toArray();
        $permisosPorRol = $this->permisosPersonalizados->pluck('valor','permiso.codigo')->toArray();
        $privilegios =  array_merge($allPermisos,$permisosPorRol);
        */
        
        $privilegios = $this->permisos()->pluck('valor','codigo')->toArray();

        return $privilegios;
    }

    public function puede($key)
    {
        $privilegios = $this->privilegios();

        if(isset($privilegios[$key])){
            return boolval($privilegios[$key]);
        }

        return false;
    }
}
