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

    public function coloraciones()
    {
        return $this->hasMany('\yura\Modelos\Coloracion', 'id_marcacion');
    }

    public function distribuciones()
    {
        return $this->hasMany('\yura\Modelos\Distribucion', 'id_marcacion');
    }

    public function getColoracionByName($nombre)
    {
        $r = DB::table('coloracion as c')
            ->select('c.*')
            ->where('c.id_marcacion', '=', $this->id_marcacion)
            ->where('c.nombre', '=', $nombre)
            ->first();

        if ($r != '')
            return Coloracion::find($r->id_coloracion);
        else
            return '';
    }

    public function getTotalRamos()
    {
        $r = 0;
        foreach ($this->coloraciones as $item) {
            $r += $item->cantidad;
        }
        return $r;
    }
}