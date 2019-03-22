<?php

namespace App\Http\Controllers;
use App\User;
use App\Persona;
use App\UserPermiso;
use Illuminate\Support\Facades\DB; //Me permite trabajar con gestion de transacciones

use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $personas = User::join('personas','users.id','=','personas.id')
            ->join('roles','users.idrol','=','roles.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
            'personas.num_documento','personas.direccion','personas.telefono',
            'personas.email','users.usuario','users.password',
            'users.condicion','users.idrol','roles.nombre as rol')
            ->orderBy('personas.id', 'desc');
        }
        else{
            $personas = User::join('personas','users.id','=','personas.id')
            ->join('roles','users.idrol','=','roles.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
            'personas.num_documento','personas.direccion','personas.telefono',
            'personas.email','users.usuario','users.password',
            'users.condicion','users.idrol','roles.nombre as rol')            
            ->where('personas.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('personas.id', 'desc');
        }
         
        // Filtro multiempresa 
        $personas->where('users.idempresa','=', Auth::user()->idempresa);

        $personas = $personas->paginate(6);
 
        return [
            'pagination' => [
                'total'        => $personas->total(),
                'current_page' => $personas->currentPage(),
                'per_page'     => $personas->perPage(),
                'last_page'    => $personas->lastPage(),
                'from'         => $personas->firstItem(),
                'to'           => $personas->lastItem(),
            ],
            'personas' => $personas
        ];
    }
 
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
         
        try{
            DB::beginTransaction();
            $persona = new Persona();
            $persona->idempresa = Auth::user()->idempresa;
            $persona->nombre = strtoupper($request->nombre);
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = strtoupper($request->num_documento);
            $persona->direccion = strtoupper($request->direccion);
            $persona->telefono = strtoupper($request->telefono);
            $persona->email = strtoupper($request->email);
            $persona->save();
 
            $user = new User();
            $user->usuario = strtoupper($request->usuario);
            $user->password = bcrypt( $request->password);
            $user->condicion = '1';
            $user->idrol = $request->idrol;          
 
            $user->id = $persona->id; //Al id de este usuario le enviamos el id previamente insertado el id del objeto persona
 
            $user->save();
 
            DB::commit();
 
        } catch (Exception $e){
            DB::rollBack();
        }

    }
 
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
         
        try{
            DB::beginTransaction();
             //Buscar primero el proveedor a modificar
            $user = User::findOrFail($request->id);
 
            $persona = Persona::findOrFail($user->id);
 
            //$persona->idempresa = Auth::user()->idempresa;
            $persona->nombre = strtoupper($request->nombre);
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = strtoupper($request->num_documento);
            $persona->direccion = strtoupper($request->direccion);
            $persona->telefono = strtoupper($request->telefono);
            $persona->email = strtoupper($request->email);
            $persona->save();
 
             
            $user->usuario = strtoupper($request->usuario);
            $user->password = bcrypt( $request->password);
            $user->condicion = '1';
            $user->idrol = $request->idrol;
            $user->save();
 
 
            DB::commit();
 
        } catch (Exception $e){
            DB::rollBack();
        }
 
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '0';
        $user->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '1';
        $user->save();
    }

     /* Permisos de usuario */
     public function obtenerPermisosDeUsuario(Request $request)
     {
         if (!$request->ajax()) return redirect('/');
         $user = User::findOrFail($request->id);
 
         return ['permisos' => $user->permisos()];
     }
     public function updatePermisosDeUsuario(Request $request)
     {
         if (!$request->ajax()) return redirect('/');
 
         $iduser = $request->id;
         $user = User::findOrFail($iduser);
         $permisosDeUsuario = $user->permisos();
 
         $checkedPermisos =  $request->checked;
        
         foreach ($permisosDeUsuario as $permiso) {
             $idpermiso = $permiso->id;
             $valor = 0;
             if (in_array($idpermiso, $checkedPermisos)) {
                 $valor = 1;
             }
 
             $userpermiso = UserPermiso::where('iduser', '=', $iduser)
             ->where('idpermiso', '=', $idpermiso)
             ->first();
 
             if($userpermiso == null && $valor == 1)
             {
                 $userpermiso = new UserPermiso();
                 $userpermiso->iduser = $iduser;
                 $userpermiso->idpermiso = $idpermiso;
                 $userpermiso->valor = $valor;
                 $userpermiso->save();
             }
             elseif($userpermiso != null && $userpermiso->valor != $valor){
                 DB::update('update user_permiso set valor = ? where iduser = ? and idpermiso = ?', [$valor,$iduser,$idpermiso]);
             }
         }
 
         return ['checked' => $request->checked, 'id' => $request->id, 'userpermiso' => $userpermiso];
     }
}
