<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cosecha extends Model
{
    protected $table = 'cosecha';
    protected $primaryKey = 'id_cosecha';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cosecha',
        'fecha_ingreso',
        'personal',
        'fecha_registro',
        'estado',
        'hora_inicio',
    ];

    public function recepciones()
    {
        return $this->hasMany('\yura\Modelos\Recepcion', 'id_cosecha');
    }

    public function getTotalTallos()
    {
        $r = 0;
        foreach ($this->recepciones as $recepcion) {
            $r += $recepcion->cantidad_tallos();
        }
        return $r;
    }

    public function getTotalTallosByVariedad($variedad)
    {
        $r = 0;
        foreach ($this->recepciones as $recepcion) {
            $r += $recepcion->tallos_x_variedad($variedad);
        }
        return $r;
    }

    public function getVariedades()
    {
        $listado = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select('dr.id_variedad')->distinct()
            ->where('r.id_cosecha', '=', $this->id_cosecha)
            ->get();
        return $listado;
    }

    public function getTotalTallosByIntervalo($inicio, $fin)
    {
        $r = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cantidad'))
            ->where('r.estado', '=', 1)
            ->where('dr.estado', '=', 1)
            ->where('r.id_cosecha', '=', $this->id_cosecha)
            ->where('r.fecha_ingreso', '>=', $inicio)
            ->where('r.fecha_ingreso', '<', $fin)
            ->get()[0];

        return $r->cantidad;
    }

    public function getCantidadHorasTrabajo()
    {
        $last_ingreso = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select(DB::raw('max(dr.fecha_registro) as fecha'))
            ->where('dr.estado', '=', 1)
            ->where('r.id_cosecha', '=', $this->id_cosecha)
            ->get();

        if ($last_ingreso[0]->fecha != '') {
            $horas = difFechas($last_ingreso[0]->fecha, $this->fecha_ingreso . ' ' . $this->hora_inicio)->h;
            $horas += round(difFechas($last_ingreso[0]->fecha, $this->fecha_ingreso . ' ' . $this->hora_inicio, 2)->i / 60);

            return $horas;
        } else {
            return 0;
        }
    }

    public function getRendimiento()
    {
        if ($this->getCantidadHorasTrabajo() != 0) {
            $r = $this->getTotalTallos() / $this->personal;
            return round($r / $this->getCantidadHorasTrabajo(), 2);
        } else
            return 0;
    }

    public function getIntervalosHoras()
    {
        $r = [];
        $listado_fechas = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select('dr.fecha_registro')->distinct()
            ->where('r.estado', '=', 1)
            ->where('dr.estado', '=', 1)
            ->where('r.id_cosecha', '=', $this->id_cosecha)
            ->get();

        $listado = [];
        foreach ($listado_fechas as $item)
            array_push($listado, $item->fecha_registro);

        foreach ($listado as $item) {
            $intervalo = [
                'fecha_inicio' => substr($item, 0, 10),
                'fecha_fin' => substr(opHorasFecha('+', 1, substr($item, 0, 13) . ':00'), 0, 10),
                'fecha_inicio_full' => substr($item, 0, 13) . ':00',
                'fecha_fin_full' => opHorasFecha('+', 1, substr($item, 0, 13) . ':00'),
                'hora_inicio' => substr($item, 11, 2) . ':00',
                'hora_fin' => substr(opHorasFecha('+', 1, substr($item, 0, 13) . ':00'), 11, 2) . ':00',
            ];
            if (!in_array($intervalo, $r)) {
                array_push($r, $intervalo);
            }
        }

        return $r;
    }

    public function getDetallesByIntervalo($inicio, $fin)
    {
        $r = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cantidad'), 'dr.id_variedad')
            ->where('r.estado', '=', 1)
            ->where('dr.estado', '=', 1)
            ->where('r.id_cosecha', '=', $this->id_cosecha)
            ->where('r.fecha_ingreso', '>=', $inicio)
            ->where('r.fecha_ingreso', '<', $fin)
            ->groupBy('dr.id_variedad')
            ->get();

        return $r;
    }
}