<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Empresa;
use App\User;

class LoginController extends Controller
{
    public function showLoginForm(){
        $empresas = Empresa::all();
        return view('auth.login',['arrayEmpresas' =>  $empresas]);
    }
 
    public function login(Request $request){
        $this->validateLogin($request);        
 
        if (Auth::attempt(['usuario' => $request->usuario,'password' => $request->password,'condicion'=>1,'idempresa' => $request->empresa])){
            return redirect()->route('main');
        }
 
        return back()
        ->withErrors(['password' => trans('auth.failed')])
        ->withInput(request(['usuario','empresa']));
 
    }
 
    protected function validateLogin(Request $request){
        $this->validate($request,[
            'empresa' => 'required|string',
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);
 
    }
 
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    public function userinfo(){
        $usuario  = '';
        $avatar  = '';
        $nombre = '';
        $empresa = '';
        if(Auth::check()){
            $user = Auth::user();
            $usuario = $user->usuario;
            $avatar = $user->avatar;
            $nombre = $user->persona->nombre;
            $empresa = $user->empresa->nombre;
        }
        return response()->json(['usuario' => $usuario, 'nombre' => $nombre, 'avatar' => $avatar, 'empresa' => $empresa]);;
    }
    
    public function granted(){
        $granted = [];
        if(Auth::check()){
            $granted = array_filter(Auth::user()->privilegios(), function($val, $key) {
                return $val == 1;
            }, ARRAY_FILTER_USE_BOTH);
        }
        return $granted;
    }
}