<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //protected $table = 'categorias';
    //protected $primaryKey = 'id';
    protected $fillable = ['idempresa','nombre','descripcion','condicion'];

    public function articulos()
    {
        return $this->hasMany('App\Articulo','idcategoria');
    }
}
