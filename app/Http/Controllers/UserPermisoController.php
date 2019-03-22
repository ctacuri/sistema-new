<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use App\UserPermiso;
 
class UserPermisoController extends Controller
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
        $userpermiso = new UserPermiso();
        $userpermiso->iduser = $request->iduser;
        $userpermiso->idpermiso = $request->idpermiso;
        $userpermiso->valor = $request->valor;
        $userpermiso->save();
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
        $userpermiso = UserPermiso::where('iduser', '=', $request->iduser)
        ->where('idpermiso', '=', $request->idpermiso)
        ->first();

        $userpermiso->valor = $request->valor;
        $userpermiso->save();
    }
 
   
}