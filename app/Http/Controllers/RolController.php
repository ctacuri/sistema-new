<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;
use App\RolPermiso;
use DB;
use Auth;

class RolController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $roles = Rol::orderBy('id', 'desc');
        }
        else{
            $roles = Rol::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc');
        }
         
        // Filtro multiempresa 
        $roles->whereIn('idempresa', array(0,Auth::user()->idempresa));

        $roles = $roles->paginate(6);
 
        return [
            'pagination' => [
                'total'        => $roles->total(),
                'current_page' => $roles->currentPage(),
                'per_page'     => $roles->perPage(),
                'last_page'    => $roles->lastPage(),
                'from'         => $roles->firstItem(),
                'to'           => $roles->lastItem(),
            ],
            'roles' => $roles
        ];
    } 

    public function selectRol(Request $request)
    {
        $roles = Rol::where('condicion', '=', '1')
        ->select('id','nombre')
        ->orderBy('nombre','asc');

        // Filtro multiempresa 
        $roles->whereIn('idempresa', array(0,Auth::user()->idempresa));
        
        $roles = $roles->get();

        return ['roles' => $roles];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rol = new Rol();
        $rol->idempresa = Auth::user()->idempresa;
        $rol->nombre = strtoupper($request->nombre);
        $rol->descripcion = strtoupper($request->descripcion);
        $rol->condicion = '1';
        $rol->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rol = Rol::findOrFail($request->id);
        //$rol->idempresa = Auth::user()->idempresa;
        $rol->nombre = strtoupper($request->nombre);
        $rol->descripcion = strtoupper($request->descripcion);
        //$rol->condicion = '1';
        $rol->save();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rol = Rol::findOrFail($request->id);
        $rol->condicion = '0';
        $rol->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rol = Rol::findOrFail($request->id);
        $rol->condicion = '1';
        $rol->save();
    }


    /* Permisos de rol */
    public function obtenerPermisosDeRol(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rol = Rol::findOrFail($request->id);

        return ['permisos' => $rol->permisos()];
    }
    public function updatePermisosDeRol(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $idrol = $request->id;
        $rol = Rol::findOrFail($idrol);
        $permisosDeRol = $rol->permisos();

        $checkedPermisos =  $request->checked;
       
        foreach ($permisosDeRol as $permiso) {
            $idpermiso = $permiso->id;
            $valor = 0;
            if (in_array($idpermiso, $checkedPermisos)) {
                $valor = 1;
            }

            $rolpermiso = RolPermiso::where('idrol', '=', $idrol)
            ->where('idpermiso', '=', $idpermiso)
            ->first();

            if($rolpermiso == null && $valor == 1)
            {
                $rolpermiso = new RolPermiso();
                $rolpermiso->idrol = $idrol;
                $rolpermiso->idpermiso = $idpermiso;
                $rolpermiso->valor = $valor;
                $rolpermiso->save();
            }
            elseif($rolpermiso != null && $rolpermiso->valor != $valor){                
                DB::update('update rol_permiso set valor = ? where idrol = ? and idpermiso = ?', [$valor,$idrol,$idpermiso]);
            }
        }
    }
}
