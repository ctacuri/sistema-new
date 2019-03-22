<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Articulo;
use Auth;
use App\Categoria;
 
class ArticuloController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.nombre','categorias.nombre as nombre_categoria','articulos.precio_venta','articulos.precio_02','articulos.precio_03','articulos.precio_04','articulos.stock','articulos.descripcion','articulos.condicion')
            ->orderBy('articulos.id', 'desc');
        }
        else{
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.nombre','categorias.nombre as nombre_categoria','articulos.precio_venta','articulos.precio_02','articulos.precio_03','articulos.precio_04','articulos.stock','articulos.descripcion','articulos.condicion')
            ->where('articulos.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('articulos.id', 'desc');
        }
         
        // Filtro multiempresa 
        $articulos->where([['categorias.idempresa','=', Auth::user()->idempresa],['articulos.idempresa','=', Auth::user()->idempresa]]);

        $articulos = $articulos->paginate(6);
 
        return [
            'pagination' => [
                'total'        => $articulos->total(),
                'current_page' => $articulos->currentPage(),
                'per_page'     => $articulos->perPage(),
                'last_page'    => $articulos->lastPage(),
                'from'         => $articulos->firstItem(),
                'to'           => $articulos->lastItem(),
            ],
            'articulos' => $articulos
        ];
    }

    public function listarArticulo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.nombre','categorias.nombre as nombre_categoria','articulos.precio_venta','articulos.precio_02','articulos.precio_03','articulos.precio_04','articulos.stock','articulos.descripcion','articulos.condicion')
            ->orderBy('articulos.id', 'desc');
        }
        else{
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.nombre','categorias.nombre as nombre_categoria','articulos.precio_venta','articulos.precio_02','articulos.precio_03','articulos.precio_04','articulos.stock','articulos.descripcion','articulos.condicion')
            ->where('articulos.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('articulos.id', 'desc');
        }

        // Filtro multiempresa 
        $articulos->where([['categorias.idempresa','=', Auth::user()->idempresa],['articulos.idempresa','=', Auth::user()->idempresa]]);

        $articulos = $articulos->paginate(10);
        
        return ['articulos' => $articulos];
    }

    public function listarArticuloVenta(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.nombre','categorias.nombre as nombre_categoria','articulos.precio_venta','articulos.precio_02','articulos.precio_03','articulos.precio_04','articulos.stock','articulos.descripcion','articulos.condicion')
            ->where('articulos.stock','>','0')
            ->orderBy('articulos.id', 'desc');
        }
        else{
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.nombre','categorias.nombre as nombre_categoria','articulos.precio_venta','articulos.precio_02','articulos.precio_03','articulos.precio_04','articulos.stock','articulos.descripcion','articulos.condicion')
            ->where('articulos.'.$criterio, 'like', '%'. $buscar . '%')
            ->where('articulos.stock','>','0')
            ->orderBy('articulos.id', 'desc');
        }
         
        // Filtro multiempresa 
        $articulos->where([['categorias.idempresa','=', Auth::user()->idempresa],['articulos.idempresa','=', Auth::user()->idempresa]]);

        $articulos = $articulos->paginate(10);
 
        return ['articulos' => $articulos];
    }

    public function listarPdf(Request $request)
    {
        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.nombre',
        'categorias.nombre as nombre_categoria','articulos.precio_venta'
        ,'articulos.precio_02','articulos.precio_03','articulos.precio_04'
        ,'articulos.stock'
        ,'articulos.descripcion','articulos.condicion')
        ->orderBy('articulos.nombre', 'desc');

        // Filtro multiempresa 
        $articulos->where([['categorias.idempresa','=', Auth::user()->idempresa],['articulos.idempresa','=', Auth::user()->idempresa]]);
        
        $articulos = $articulos->get();

        $cont = count($articulos);       

        $pdf = \PDF::loadView('pdf.articulospdf',['articulos'=>$articulos,'cont'=>$cont]);
        return $pdf->download('articulos.pdf');
    }

    public function buscarArticulo(Request $request){
        if (!$request->ajax()) return redirect('/');
 
        $filtro = $request->filtro;
        $articulos = Articulo::where('codigo','=', $filtro)
        ->select('id','nombre')->orderBy('nombre', 'asc');
        
        // Filtro multiempresa 
        $articulos->where('idempresa','=', Auth::user()->idempresa);
                
        $articulos = $articulos->take(1)->get();
 
        return ['articulos' => $articulos];
    }

    public function buscarArticuloVenta(Request $request){
        if (!$request->ajax()) return redirect('/');
 
        $filtro = $request->filtro;
        $articulos = Articulo::where('codigo','=', $filtro)
        ->select('id','nombre','stock','precio_venta')
        ->where('stock','>','0')
        ->orderBy('nombre', 'asc');

        // Filtro multiempresa 
        $articulos->where('idempresa','=', Auth::user()->idempresa);
                
        $articulos = $articulos->take(1)->get();
 
        return ['articulos' => $articulos];
    }
     
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $articulo = new Articulo();
        $articulo->idcategoria = $request->idcategoria;
        $articulo->idempresa = Auth::user()->idempresa;
        $articulo->codigo = $request->codigo;
        $articulo->nombre = strtoupper($request->nombre);
        $articulo->precio_venta = $request->precio_venta;
        $articulo->precio_02 = $request->precio_02;
        $articulo->precio_03 = $request->precio_03;
        $articulo->precio_04 = $request->precio_04;
        $articulo->stock = $request->stock;
        $articulo->descripcion = strtoupper($request->descripcion);
        $articulo->condicion = '1';
        $articulo->save();
    }
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        //$articulo->idempresa = Auth::user()->idempresa;
        $articulo->idcategoria = $request->idcategoria;
        $articulo->codigo = $request->codigo;
        $articulo->nombre = strtoupper($request->nombre);
        $articulo->precio_venta = $request->precio_venta;
        $articulo->precio_02 = $request->precio_02;
        $articulo->precio_03 = $request->precio_03;
        $articulo->precio_04 = $request->precio_04;
        $articulo->stock = $request->stock;
        $articulo->descripcion = strtoupper($request->descripcion);
        //$articulo->condicion = '1';
        $articulo->save();
    }
    
    private function csvToArray($filename = '', $delimiter = ','){
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }
    
    public function importcsv(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        $InitialMaxET = ini_get('max_execution_time');
        ini_set('max_execution_time', 600); 

        $success = 'false';
        $message = '';
        $maxSize = 2000000;

        if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
            $extension = $request->archivo->getClientOriginalExtension();
            $size = $request->archivo->getClientSize();
            if($extension == 'csv' && $size <= $maxSize){
                $path = $request->archivo->getRealPath();
                $delimitador = $request->delimitador;

                $articulosArray = $this->csvToArray($path,$delimitador);
                $total = count($articulosArray);
                
                if($total > 0 && isset($articulosArray[0]['CODIGO'])){

                    $totalGuardados = 0;

                    for ($i = 0; $i < $total; $i ++)
                    {
                        if(isset($articulosArray[$i]['CODIGO']) && $articulosArray[$i]['CODIGO'] != '')
                        {
                            $articulo = Articulo::where('codigo', '=', $articulosArray[$i]['CODIGO']);
                            /* Filtro multiempresa */
                            $articulo->where('idempresa','=', Auth::user()->idempresa);
                            $articulo = $articulo->first();

                            if($articulo == null)
                            {
                                $articulo = new Articulo();
                            }

                            $categoria = null;
                            if(isset($articulosArray[$i]['CATEGORIA']) && $articulosArray[$i]['CATEGORIA'] != '')
                            {
                                $categoria = Categoria::where('nombre', '=', $articulosArray[$i]['CATEGORIA']);
                                /* Filtro multiempresa */
                                $categoria->where('idempresa','=', Auth::user()->idempresa);
                                $categoria = $categoria->first();
                            }

                            if($categoria == null)
                            {
                                $categoria = new Categoria();
                                $categoria->idempresa = Auth::user()->idempresa;
                                $categoria->nombre = strtoupper($articulosArray[$i]['CATEGORIA']);
                                $categoria->descripcion = '';
                                $categoria->condicion = '1';
                                $categoria->save();
                            }

                            $articulo->idcategoria = $categoria->id;

                            $articulo->idempresa = Auth::user()->idempresa;
                            $articulo->codigo = $articulosArray[$i]['CODIGO'];
                            $articulo->nombre = strtoupper($articulosArray[$i]['NOMBRE']);
                            $articulo->precio_venta = $articulosArray[$i]['PRECIO'];
                            $articulo->precio_02 = $articulosArray[$i]['PRECIO_02'];
                            $articulo->precio_03 = $articulosArray[$i]['PRECIO_03'];
                            $articulo->precio_04 = $articulosArray[$i]['PRECIO_04'];
                            $articulo->stock = $articulosArray[$i]['STOCK'];
                            $articulo->descripcion = strtoupper($articulosArray[$i]['DESCRIPCION']);
                            $articulo->condicion = '1';

                            $articulo->save();

                            $totalGuardados += 1;
                        }
                    }

                    if($totalGuardados > 0){
                        $success = true;
                    }
                    if($total - $totalGuardados > 0){
                        $message = 'Solo ' . $totalGuardados . ' productos fueron importados. El resto fue descartado. Revise el archivo.';
                    }
                    else{
                        $message = 'Productos importados correctamente.';
                    }
                }
                else{
                    $message = 'Verifique el contenido del archivo cargado y su delimitador.';
                }
            }
            else {
                $message = 'El archivo debe tener la extensión ".csv" y pesar máximo 2MB.';
            }

        } else {
            $message = 'El archivo cargado no es válido.';
        }
        
        ini_set('max_execution_time', $InitialMaxET); 

        return ['archivo' => $articulosArray, 'success' => $success, 'message' => $message];

    }
    
    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();
    }
 
    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
 
}