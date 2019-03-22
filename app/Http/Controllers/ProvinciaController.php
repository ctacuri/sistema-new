<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use App\Provincia;
 
class ProvinciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if($buscar==''){
            $provincias = Provincia::orderBy('id', 'desc')->paginate(6);
        }else{
            $provincias = Provincia::where($criterio, 'like', '%'.$buscar.'%')->orderBy('id', 'desc')->paginate(6);
        }
 
        return [
            'pagination' => [
                'total'        => $provincias->total(),
                'current_page' => $provincias->currentPage(),
                'per_page'     => $provincias->perPage(),
                'last_page'    => $provincias->lastPage(),
                'from'         => $provincias->firstItem(),
                'to'           => $provincias->lastItem(),
            ],
            'provincias' => $provincias
        ];
    }   

    public function selectProvincia(Request $request){
        if (!$request->ajax()) return redirect('/');
        $provincias = Provincia::where('condicion','=','1')
        ->select('id','iddepartamento','descripcion')
        ->orderBy('iddepartamento', 'asc')
        ->orderBy('descripcion', 'asc')
        ->get();
        return ['provincias' => $provincias];
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
        $provincia = new Provincia();
        $provincia->iddepartamento = $request->iddepartamento;
        $provincia->descripcion = strtoupper($request->descripcion);
        $provincia->condicion = '1';
        $provincia->save();
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
        $provincia = Provincia::findOrFail($request->id);
        $provincia->iddepartamento = $request->iddepartamento;
        $provincia->descripcion = strtoupper($request->descripcion);
        $provincia->condicion = '1';
        $provincia->save();
    }
 
    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $provincia = Provincia::findOrFail($request->id);
        $provincia->condicion = '0';
        $provincia->save();
    }
 
    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $provincia = Provincia::findOrFail($request->id);
        $provincia->condicion = '1';
        $provincia->save();
    }

}