<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class StockEmpaquetado extends Model
{
    protected $table = 'stock_empaquetado';
    protected $primaryKey = 'id_stock_empaquetado';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_stock_empaquetado',
        'id_variedad',
        'id_semana',
        'fecha_registro',
        'fecha_ingreso',
        'cantidad_ingresada',
        'cantidad_empaquetada',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }
}
