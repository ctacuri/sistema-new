<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresa;
use Auth;

class EmpresaController extends Controller
{

    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if($buscar==''){
            $empresas = Empresa::orderBy('id', 'desc');
        }else{
            $empresas = Empresa::where($criterio, 'like', '%'.$buscar.'%')->orderBy('id', 'desc');
        }

        // Filtro multiempresa 
        $empresas->where('id','=', Auth::user()->idempresa);

        $empresas = $empresas->paginate(6);
 
        return [
            'pagination' => [
                'total'        => $empresas->total(),
                'current_page' => $empresas->currentPage(),
                'per_page'     => $empresas->perPage(),
                'last_page'    => $empresas->lastPage(),
                'from'         => $empresas->firstItem(),
                'to'           => $empresas->lastItem(),
            ],
            'empresas' => $empresas
        ];
    }

    public function selectEmpresa(Request $request){
        if (!$request->ajax()) return redirect('/');
        $empresas = Empresa::where('condicion','=','1')
        ->select('id','nombre')->orderBy('nombre', 'asc')->get();
        return ['empresas' => $empresas];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $empresa = new Empresa();
        $empresa->ruc = $request->ruc;
        $empresa->nombre = strtoupper($request->nombre);
        $empresa->direccion = strtoupper($request->direccion);
        $empresa->iddepartamento = strtoupper($request->iddepartamento);
        $empresa->idprovincia = strtoupper($request->idprovincia);
        $empresa->iddistrito = strtoupper($request->iddistrito);
        $empresa->telefono = strtoupper($request->telefono);
        $empresa->email = strtoupper($request->email);
        $empresa->condicion = '1';
        $empresa->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $empresa = Empresa::findOrFail($request->id);
        $empresa->ruc = $request->ruc;
        $empresa->nombre = strtoupper($request->nombre);
        $empresa->direccion = strtoupper($request->direccion);
        $empresa->iddepartamento = strtoupper($request->iddepartamento);
        $empresa->idprovincia = strtoupper($request->idprovincia);
        $empresa->iddistrito = strtoupper($request->iddistrito);
        $empresa->telefono = strtoupper($request->telefono);
        $empresa->email = strtolower($request->email);
        //$empresa->condicion = '1';
        $empresa->save();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $empresa = Empresa::findOrFail($request->id);
        $empresa->condicion = '0';
        $empresa->save();
    }
 
    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $empresa = Empresa::findOrFail($request->id);
        $empresa->condicion = '1';
        $empresa->save();
    }

}
