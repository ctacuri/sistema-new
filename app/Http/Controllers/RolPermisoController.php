<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use App\RolPermiso;
 
class RolPermisoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rolpermiso = new RolPermiso();
        $rolpermiso->idrol = $request->idrol;
        $rolpermiso->idpermiso = $request->idpermiso;
        $rolpermiso->valor = $request->valor;
        $rolpermiso->save();
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
        $rolpermiso = RolPermiso::where('idrol', '=', $request->idrol)
        ->where('idpermiso', '=', $request->idpermiso)
        ->first();

        $rolpermiso->valor = $request->valor;
        $rolpermiso->save();
    }
 
   
}