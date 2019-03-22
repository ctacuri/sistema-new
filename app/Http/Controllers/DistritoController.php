<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use App\Distrito;
 
class DistritoController extends Controller
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
            $distritos = Distrito::orderBy('id', 'desc')->paginate(6);
        }else{
            $distritos = Distrito::where($criterio, 'like', '%'.$buscar.'%')->orderBy('id', 'desc')->paginate(6);
        }
 
        return [
            'pagination' => [
                'total'        => $distritos->total(),
                'current_page' => $distritos->currentPage(),
                'per_page'     => $distritos->perPage(),
                'last_page'    => $distritos->lastPage(),
                'from'         => $distritos->firstItem(),
                'to'           => $distritos->lastItem(),
            ],
            'distritos' => $distritos
        ];
    }   

    public function selectDistrito(Request $request){
        if (!$request->ajax()) return redirect('/');
        $distritos = Distrito::where('condicion','=','1')
        ->select('id','iddepartamento','idprovincia','descripcion')
        ->orderBy('iddepartamento', 'asc')
        ->orderBy('idprovincia', 'asc')
        ->orderBy('descripcion', 'asc')
        ->get();
        return ['distritos' => $distritos];
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
        $distrito = new Distrito();
        $distrito->iddepartamento = $request->iddepartamento;
        $distrito->idprovincia = $request->idprovincia;
        $distrito->descripcion = strtoupper($request->descripcion);
        $distrito->condicion = '1';
        $distrito->save();
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
        $distrito = Distrito::findOrFail($request->id);
        $distrito->iddepartamento = $request->iddepartamento;
        $distrito->idprovincia = $request->idprovincia;
        $distrito->descripcion = strtoupper($request->descripcion);
        $distrito->condicion = '1';
        $distrito->save();
    }
 
    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $distrito = Distrito::findOrFail($request->id);
        $distrito->condicion = '0';
        $distrito->save();
    }
 
    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $distrito = Distrito::findOrFail($request->id);
        $distrito->condicion = '1';
        $distrito->save();
    }

}