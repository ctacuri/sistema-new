<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
});

Route::group(['middleware' => ['auth']], function () {

    /* 
    --------------------------------------------
    Acceso sin restricción 
    --------------------------------------------
    */

    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/logged/userinfo', 'Auth\LoginController@userinfo');
    Route::get('/logged/granted', 'Auth\LoginController@granted');
    
    //Notificaciones
    Route::post('/notification/get', 'notificationController@get');

    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');
    
    Route::get('/dashboard', 'DashboardController');

    Route::get('/departamento/all', 'DepartamentoController@selectDepartamento');
    Route::get('/provincia/all', 'ProvinciaController@selectProvincia');
    Route::get('/distrito/all', 'DistritoController@selectDistrito');


    /* 
    --------------------------------------------
    Acceso restringido
    --------------------------------------------
    */

    Route::get('/categoria', function(Request $request) {
        if(Auth::user()->puede('VER_CATEGORIAS'))
        {
            return (app()->make('App\Http\Controllers\CategoriaController'))->index($request);
        }
        else{
            return abort(403,'El usuario no tiene permiso para ver las categorías.');
        }
    });
    Route::post('/categoria/registrar', function(Request $request) {
        if(Auth::user()->puede('AGREGAR_CATEGORIAS'))
        {
            return (app()->make('App\Http\Controllers\CategoriaController'))->store($request);
        }
        else{
            return abort(403,'El usuario no puede agregar categorías.');
        }
    });
    Route::put('/categoria/actualizar', function(Request $request) {
        if(Auth::user()->puede('ACTUALIZAR_CATEGORIAS'))
        {
            return (app()->make('App\Http\Controllers\CategoriaController'))->update($request);
        }
        else{
            return abort(403,'El usuario no puede actualizar categorías.');
        }
    });
    Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
    Route::put('/categoria/activar', 'CategoriaController@activar');
    Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');
    Route::get('/categoria/validarSiExiste', 'CategoriaController@validarSiExiste');


    Route::get('/articulo', function(Request $request) {
        if(Auth::user()->puede('VER_PRODUCTOS'))
        {
            return (app()->make('App\Http\Controllers\ArticuloController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/articulo/registrar', 'ArticuloController@store');
    Route::post('/articulo/importarcsv', 'ArticuloController@importcsv');
    Route::put('/articulo/actualizar', 'ArticuloController@update');
    Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
    Route::put('/articulo/activar', 'ArticuloController@activar');
    Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
    Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
    Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
    Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');
    Route::get('/articulo/listarPdf', 'ArticuloController@listarPdf')->name('articulos_pdf');


    Route::get('/proveedor', function(Request $request) {
        if(Auth::user()->puede('VER_PROVEEDORES'))
        {
            return (app()->make('App\Http\Controllers\ProveedorController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/proveedor/registrar', 'ProveedorController@store');
    Route::put('/proveedor/actualizar', 'ProveedorController@update');
    Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

    Route::get('/ingreso', function(Request $request) {
        if(Auth::user()->puede('VER_INGRESOS'))
        {
            return (app()->make('App\Http\Controllers\IngresoController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/ingreso/registrar', 'IngresoController@store');
    Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
    Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
    Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

    Route::get('/cliente', function(Request $request) {
        if(Auth::user()->puede('VER_CLIENTES'))
        {
            return (app()->make('App\Http\Controllers\ClienteController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/cliente/registrar', 'ClienteController@store');
    Route::put('/cliente/actualizar', 'ClienteController@update');
    Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

    Route::get('/venta', function(Request $request) {
        if(Auth::user()->puede('VER_VENTAS'))
        {
            return (app()->make('App\Http\Controllers\VentaController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/venta/registrar', 'VentaController@store');
    Route::put('/venta/desactivar', 'VentaController@desactivar');
    Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
    Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
    Route::get('/venta/pdf/{id}', 'VentaController@pdf')->name('venta_pdf');

    Route::get('/rol', function(Request $request) {
        if(Auth::user()->puede('VER_ROLES'))
        {
            return (app()->make('App\Http\Controllers\RolController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/rol/registrar', 'RolController@store');
    Route::put('/rol/actualizar', 'RolController@update');
    Route::get('/rol/selectRol', 'RolController@selectRol');
    Route::get('/rol/obtenerPermisosDeRol', 'RolController@obtenerPermisosDeRol');
    Route::put('/rol/actualizarPermisosDeRol', 'RolController@updatePermisosDeRol');
    
    
    Route::get('/user', function(Request $request) {
        if(Auth::user()->puede('VER_USUARIOS'))
        {
            return (app()->make('App\Http\Controllers\UserController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/user/registrar', 'UserController@store');
    Route::put('/user/actualizar', 'UserController@update');
    Route::put('/user/desactivar', 'UserController@desactivar');
    Route::put('/user/activar', 'UserController@activar');

    Route::get('/user/obtenerPermisosDeUsuario', 'UserController@obtenerPermisosDeUsuario');
    Route::put('/user/actualizarPermisosDeUsuario', 'UserController@updatePermisosDeUsuario');

    Route::get('/empresa', function(Request $request) {
        if(Auth::user()->puede('VER_EMPRESAS'))
        {
            return (app()->make('App\Http\Controllers\EmpresaController'))->index($request);
        }
        else{
            return abort(403,'Acción no autorizada.');
        }
    });
    Route::post('/empresa/registrar', 'EmpresaController@store');
    Route::put('/empresa/actualizar', 'EmpresaController@update');
    Route::put('/empresa/desactivar', 'EmpresaController@desactivar');
    Route::put('/empresa/activar', 'EmpresaController@activar');
    Route::get('/empresa/selectEmpresa', 'EmpresaController@selectEmpresa');

    /*
    Route::group(['middleware' => ['Almacenero']], function () {
        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::post('/articulo/importarcsv', 'ArticuloController@importcsv');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');
        Route::get('/categoria/validarSiExiste', 'CategoriaController@validarSiExiste');

        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::put('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
        Route::get('/articulo/listarPdf', 'ArticuloController@listarPdf')->name('articulos_pdf');

        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

    });

    Route::group(['middleware' => ['Vendedor']], function () {
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');

        Route::get('/venta', 'VentaController@index');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/desactivar', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}', 'VentaController@pdf')->name('venta_pdf');
    });

    Route::group(['middleware' => ['Administrador']], function () {

        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');
        Route::get('/categoria/validarSiExiste', 'CategoriaController@validarSiExiste');

        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::post('/articulo/importarcsv', 'ArticuloController@importcsv');
        Route::put('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');
        Route::get('/articulo/listarPdf', 'ArticuloController@listarPdf')->name('articulos_pdf');


        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

        Route::get('/venta', 'VentaController@index');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/desactivar', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}', 'VentaController@pdf')->name('venta_pdf');

        Route::get('/rol', 'RolController@index');
        Route::get('/rol/selectRol', 'RolController@selectRol');
        
        Route::get('/user', 'UserController@index');
        Route::post('/user/registrar', 'UserController@store');
        Route::put('/user/actualizar', 'UserController@update');
        Route::put('/user/desactivar', 'UserController@desactivar');
        Route::put('/user/activar', 'UserController@activar');

        Route::get('/empresa', 'EmpresaController@index');
        Route::post('/empresa/registrar', 'EmpresaController@store');
        Route::put('/empresa/actualizar', 'EmpresaController@update');
        Route::put('/empresa/desactivar', 'EmpresaController@desactivar');
        Route::put('/empresa/activar', 'EmpresaController@activar');
        Route::get('/empresa/selectEmpresa', 'EmpresaController@selectEmpresa');
    });
    */
});

//Route::get('/home', 'HomeController@index')->name('home');
