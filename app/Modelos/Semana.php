<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use DB;

class Semana extends Model
{
    protected $table = 'semana';
    protected $primaryKey = 'id_semana';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'anno',
        'codigo',
        'fecha_inicial',
        'fecha_final',
        'curva',
        'desecho',
        'semana_poda',
        'semana_siembra',
        'fecha_registro',
        'estado',
        'id_variedad',
        'tallos_planta_siembra',
        'tallos_planta_poda',
        'tallos_ramo_siembra',
        'tallos_ramo_poda',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function getTotalesProyeccionVentaSemanal($idsCliente,$idVariedad){

        $proyeccion = ProyeccionVentaSemanalReal::where([
            ['id_variedad',$idVariedad],
            ['codigo_semana',$this->codigo]
        ])->whereIn('id_cliente',$idsCliente)
            ->select(
                DB::raw('sum(valor) as total_valor'),
                DB::raw('sum(cajas_fisicas) as total_cajas_fisicas'),
                DB::raw('sum(cajas_equivalentes) as total_cajas_equivalentes'))
                ->groupBy('codigo_semana')->first();

        return $proyeccion;

    }
}
