<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class StockFrio extends Model
{
    protected $table = 'stock_frio';
    protected $primaryKey = 'id_stock_frio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_stock_frio',
        'fecha_registro',
        'estado',
        'fecha_ingreso',
        'id_consumo',
        'id_stock_apertura',
        'id_variedad',
        'id_clasificacion_unitaria',
        'id_semana',
        'dias_maduracion',
        'cantidad_ramos_estandar',
    ];

    public function consumo()
    {
        return $this->belongsTo('\yura\Modelos\Consumo', 'id_consumo');
    }

    public function stock_apertura()
    {
        return $this->belongsTo('\yura\Modelos\StockApertura', 'id_stock_apertura');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function clasificacion_unitaria()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionUnitaria', 'id_clasificacion_unitaria');
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }
}
