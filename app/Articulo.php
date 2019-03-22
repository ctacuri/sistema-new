<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $fillable = [
        'idcategoria','codigo','nombre','precio_venta','precio_02','precio_03','precio_04','stock','descripcion','condicion'
    ];
    public function categoria(){
        return $this->belongsTo('App\Categoria', 'idcategoria');
    }
}
