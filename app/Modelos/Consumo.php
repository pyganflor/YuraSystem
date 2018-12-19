<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Consumo extends Model
{
    protected $table = 'consumo';
    protected $primaryKey = 'id_consumo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_consumo',
        'fecha_registro',
        'estado',
        'fecha_pedidos',
    ];

    public function stocks_frio()
    {
        return $this->hasMany('\yura\Modelos\StockFrio', 'id_consumo');
    }
}
