<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $table = 'envio';
    protected $primaryKey = 'id_envio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'fecha_envio',
        'fecha_registro',
        'estado',   // default: 0
        'guia_madre',
        'guia_hija',
        'dae',
        'email',
        'telefono',
        'direccion',
        'codigo_pais',
        'almacen',
        'codigo_dae',
        'id_consignatario'
    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetalleEnvio', 'id_envio');
    }

    public function comprobante()
    {
        return $this->hasOne('\yura\Modelos\Comprobante', 'id_envio');
    }

    public function fatura_cliente_tercero()
    {
        return $this->hasOne('\yura\Modelos\FacturaClienteTercero', 'id_envio');
    }

    public function pedido()
    {
        return $this->belongsTo('\yura\Modelos\Pedido', 'id_pedido');
    }

    public function empresa()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'id_configuracion_empresa');
    }

    public function consignatario()
    {
        return $this->belongsTo('\yura\Modelos\Consignatario', 'id_consignatario');
    }
}
