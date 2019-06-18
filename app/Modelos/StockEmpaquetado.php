<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockEmpaquetado extends Model
{
    protected $table = 'stock_empaquetado';
    protected $primaryKey = 'id_stock_empaquetado';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_stock_empaquetado',
        'id_variedad',
        'fecha_registro',
        'estado',
        'cantidad_ingresada',
        'cantidad_armada',
        'empaquetado',
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
