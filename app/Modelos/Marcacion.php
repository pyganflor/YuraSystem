<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Marcacion extends Model
{
    protected $table = 'marcacion';
    protected $primaryKey = 'id_marcacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_marcacion',
        'nombre',
        'fecha_registro',
        'estado',
        'ramos',
        'piezas',
        'id_detalle_pedido',
        'id_especificacion_empaque',
    ];

    public function detalle_pedido()
    {
        return $this->belongsTo('\yura\Modelos\DetallePedido', 'id_detalle_pedido');
    }

    public function especificacion_empaque()
    {
        return $this->belongsTo('\yura\Modelos\EspecificacionEmpaque', 'id_especificacion_empaque');
    }

    public function marcaciones_coloraciones()
    {
        return $this->hasMany('\yura\Modelos\MarcacionColoracion', 'id_marcacion');
    }

    public function distribuciones()
    {
        return $this->hasMany('\yura\Modelos\Distribucion', 'id_marcacion');
    }

    public function getMarcacionColoracionByDetEsp($color, $det_esp)
    {
        $r = MarcacionColoracion::where('id_marcacion', $this->id_marcacion)
            ->where('id_coloracion', $color)
            ->where('id_detalle_especificacionempaque', $det_esp)->first();
        return $r;
    }

    public function eliminarDistribuciones()
    {
        foreach ($this->distribuciones as $d) {
            $d->delete();
        }
    }
}
