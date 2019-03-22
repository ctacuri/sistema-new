<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Proveedor;
use App\Persona;
use Auth;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $personas = Proveedor::join('personas','proveedores.id','=','personas.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
            'personas.num_documento','personas.direccion','personas.telefono',
            'personas.email','proveedores.contacto','proveedores.telefono_contacto',
            'proveedores.iddepartamento','proveedores.idprovincia','proveedores.iddistrito')
            ->orderBy('personas.id', 'desc');
        }
        else{
            $personas = Proveedor::join('personas','proveedores.id','=','personas.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
            'personas.num_documento','personas.direccion','personas.telefono',
            'personas.email','proveedores.contacto','proveedores.telefono_contacto',
            'proveedores.iddepartamento','proveedores.idprovincia','proveedores.iddistrito')            
            ->where('personas.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('personas.id', 'desc');
        }
         
        // Filtro multiempresa 
        $personas->where('personas.idempresa','=', Auth::user()->idempresa);

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

    public function selectProveedor(Request $request){
        //if (!$request->ajax()) return redirect('/');
 
        $filtro = $request->filtro;
        $proveedores = Proveedor::join('personas','proveedores.id','=','personas.id')
        ->where(function($query) use ($filtro) {
            $query->where('personas.nombre', 'like', '%'. $filtro . '%')
            ->orWhere('personas.num_documento', 'like', '%'. $filtro . '%')
            ->orWhere('proveedores.contacto', 'like', '%'. $filtro . '%');
        })
        ->select('personas.id','personas.nombre','personas.num_documento', 'proveedores.contacto')
        ->orderBy('personas.nombre', 'asc');
        
        // Filtro multiempresa 
        $proveedores->where('personas.idempresa','=', Auth::user()->idempresa);

        //$query = $proveedores->toSql();
        $proveedores = $proveedores->get();

        return ['proveedores' => $proveedores];
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
 
            $proveedor = new Proveedor();
            $proveedor->idempresa = Auth::user()->idempresa;
            $proveedor->contacto = strtoupper($request->contacto);
            $proveedor->telefono_contacto = strtoupper($request->telefono_contacto);
            $proveedor->iddepartamento = strtoupper($request->iddepartamento);
            $proveedor->idprovincia = strtoupper($request->idprovincia);
            $proveedor->iddistrito = strtoupper($request->iddistrito);
            $proveedor->id = $persona->id;
            $proveedor->save();
 
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
            $proveedor = Proveedor::findOrFail($request->id);
 
            $persona = Persona::findOrFail($proveedor->id);
            //$persona->idempresa = Auth::user()->idempresa;
            $persona->nombre = strtoupper($request->nombre);
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = strtoupper($request->direccion);
            $persona->telefono = $request->telefono;
            $persona->email = strtoupper($request->email);
            $persona->save();
 
            //$proveedor->idempresa = Auth::user()->idempresa;
            $proveedor->contacto = strtoupper($request->contacto);
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->iddepartamento = strtoupper($request->iddepartamento);
            $proveedor->idprovincia = strtoupper($request->idprovincia);
            $proveedor->iddistrito = strtoupper($request->iddistrito);
            $proveedor->save();
 
            DB::commit();
 
        } catch (Exception $e){
            DB::rollBack();
        }
 
    }
}
