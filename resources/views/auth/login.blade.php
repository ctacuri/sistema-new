@extends('auth.contenido')

@section('login')
<div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card-group mb-0">
          
          <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%; border-color: transparent !important;">
            <div class="card-body text-center">
              <div>
                <h2 style="font-family: 'Oswald';">Sistema de Gestion Empresarial</h2>
                <p style="padding: 5px 0;">Sistema de gestion de compras, ventas y productos </p>
                <BR>
                <img src="../img/logo-white.png" style="display: inline-block; width: 200px; height: auto;">
              </div>
            </div>
          </div>
          <div class="card p-4" style="border-color: #D6DFE4 !important;">
            <form class="form-horizaontal was-validated" method="POST" action="{{ route('login') }}" style="color: #3e515b;">
            {{ csrf_field() }}
              <div class="card-body">
                <h3 style="font-family: 'Oswald'; padding: 10px 0 20px;">Control de acceso al sistema</h2>
                <div class="form-group mb-3">
                  <span style="font-size: 16px; padding: 3px 0; display: flex; align-items: center;">
                    <i class="icon-briefcase" style="font-size: 22px;"></i> 
                    <span style="padding-left: 5px; font-family: 'Oswald';">Empresa</span>
                  </span>
                  <select name="empresa" id="empresa" class="form-control" required="required" style="border-color: #D6DFE4 !important; box-shadow: none !important;">
                    <option value="">Seleccione su empresa</option>
                    @foreach($arrayEmpresas as $empresa)
                    <option value="{{ $empresa->id }}" {{ (old('empresa') == $empresa->id) ? 'selected' : '' }}>
                        {{ $empresa->nombre }}
                    </option>
                    @endforeach  
                  </select>
                  {!!$errors->first('empresa', '<span class="invalid-feedback">:message</span>')!!}
                </div>
                <div class="form-group mb-3">
                  <span style="font-size: 16px; padding: 3px 0; display: flex; align-items: center;">
                    <i class="icon-user" style="font-size: 22px;"></i> 
                    <span style="padding-left: 5px; font-family: 'Oswald';">Usuario</span>
                  </span>
                  <input type="text" value="{{old('usuario')}}" name="usuario" id="usuario" class="form-control" placeholder="Ingrese su usuario" required="required" style="border-color: #D6DFE4 !important; box-shadow: none !important;">
                  {!!$errors->first('usuario', '<span class="invalid-feedback">:message</span>')!!}
                </div>
                <div class="form-group mb-4">
                  <span style="font-size: 16px; padding: 3px 0; display: flex; align-items: center;">
                    <i class="icon-lock" style="font-size: 22px;"></i> 
                    <span style="padding-left: 5px; font-family: 'Oswald';">Password</span>
                  </span>
                  <input type="password" name="password" id="password" class="form-control" placeholder="Escriba su contraseña" required="required" style="border-color: #D6DFE4 !important; box-shadow: none !important;">
                  {!!$errors->first('password','<span class="invalid-feedback">:message</span>')!!}
                </div>
                <div class="row">
                  <div class="col-6">
                    <button type="submit" class="btn btn-primary px-4" style="font-family: 'Oswald';">Ingresar</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
          
        </div>
      </div>
    </div>
@endsection
