<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class HistoricoVentas extends Model
{
    protected $table = 'historico_ventas';
    protected $primaryKey = 'id_historico_ventas';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_historico_ventas',
        'id_cliente',
        'id_variedad',
        'mes',
        'anno',
        'valor',
        'cajas_fisicas',
        'cajas_equivalentes',
        'precio_x_ramo',
    ];

    public function cliente()
    {
        return $this->hasMany('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function variedad()
    {
        return $this->hasMany('\yura\Modelos\Variedad', 'id_variedad');
    }
}