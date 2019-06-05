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

    public function getDestinados($variedad)
    {
        $r = 0;
        foreach ($this->stocks_frio as $item) {
            if ($item->estado == 1 && $item->id_variedad == $variedad) {
                $r += $item->cantidad_ramos_estandar;
            }
        }
        return $r;
    }
}
