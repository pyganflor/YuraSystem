<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ciclo extends Model
{
    protected $table = 'ciclo';
    protected $primaryKey = 'id_ciclo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_ciclo',
        'id_modulo',
        'id_variedad',
        'fecha_registro',
        'estado',
        'area',
        'fecha_inicio',
        'fecha_cosecha',
        'fecha_fin',
        'activo',   // boolean 1
        'poda_siembra', // char(1) P
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function modulo()
    {
        return $this->belongsTo('\yura\Modelos\Modulo', 'id_modulo');
    }

    public function getTallosCosechados()   // optimizar consulta de cosechas
    {
        $cosechas = Cosecha::All()
            ->where('estado', 1)
            ->where('fecha_ingreso', '>=', $this->fecha_inicio);
        if ($this->fecha_fin != '')
            $cosechas = $cosechas->where('fecha_ingreso', '<=', $this->fecha_fin);

        $r = 0;
        foreach ($cosechas as $c) {
            $r += $c->getTotalTallosByModulo($this->id_modulo);
        }

        return $r;
    }

    public function get80Porciento()
    {
        /* ================== OBTENER LAS COSECHAS DEL MODULO RELACIONADO AL CICLO =============== */
        $cosechas = DB::table('cosecha as c')
            ->join('recepcion as r', 'r.id_cosecha', '=', 'c.id_cosecha')
            ->join('desglose_recepcion as dr', 'dr.id_recepcion', '=', 'r.id_recepcion')
            ->select('c.id_cosecha as id')->distinct()
            ->where('c.estado', '=', 1)
            ->where('dr.id_modulo', '=', $this->id_modulo)
            ->where('c.fecha_ingreso', '>=', $this->fecha_inicio);
        if ($this->fecha_fin != '')
            $cosechas = $cosechas->where('c.fecha_ingreso', '<=', $this->fecha_fin);
        $cosechas = $cosechas->orderBy('c.fecha_ingreso')->get();

        $meta = round($this->getTallosCosechados() * 0.8, 2);
        $dia = $this->fecha_inicio;

        foreach ($cosechas as $c) {
            $c = Cosecha::find($c->id);
            if ($meta > 0) {
                $meta -= $c->getTotalTallosByModulo($this->id_modulo);

                if ($meta <= 0) {
                    $dia = $c->fecha_ingreso;
                    break;
                }
            } else {
                break;
            }
        }

        return difFechas($dia, $this->fecha_inicio)->days;
    }
}