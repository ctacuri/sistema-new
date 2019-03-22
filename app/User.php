<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Permiso;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'idempresa', 'usuario', 'password', 'avatar', 'idrol', 'condicion'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
 
    public function persona(){
        return $this->hasOne('App\Persona','id'); //Un usuario hace referencia a uan persona
    }

    public function empresa()
    {
        return $this->belongsTo('App\Empresa','idempresa'); //Un usuario pertenece a una empresa
    }

    public function rol(){
        return $this->belongsTo('App\Rol','idrol'); //Un usuario pertenece a un Rol
    }

    public function permisosPersonalizados(){
        return $this->hasMany('App\UserPermiso','iduser'); 
    }

    public function permisos()
    {
        $permisos = null;
        
        $permisos = Permiso::where('condicion','=','1')
        ->leftJoin('rol_permiso',function ($join) {
            $join->on('permisos.id','=','rol_permiso.idpermiso')
            ->where('rol_permiso.idrol','=',$this->rol->id);
        })
        ->leftJoin('user_permiso',function ($join) {
            $join->on('permisos.id','=','user_permiso.idpermiso')
            ->where('user_permiso.iduser','=',$this->id);
        })
        ->selectRaw('permisos.id,permisos.codigo,permisos.descripcion,ifnull(ifnull(user_permiso.valor,rol_permiso.valor),permisos.valor) as valor')
        ->get();

        return $permisos;
    }

    public function privilegios()
    {
        /*
        $privilegios = [];
        $allPermisos = Permiso::where('condicion','=','1')->pluck('valor','codigo')->toArray();
        $permisosPorRol = $this->rol->permisosPersonalizados->pluck('valor','permiso.codigo')->toArray();
        $permisosPorUsuario = $this->permisosPersonalizados->pluck('valor','permiso.codigo')->toArray();
        $privilegios = array_merge($allPermisos,array_merge($permisosPorRol,$permisosPorUsuario));
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
