<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClasificacionVerde extends Model
{
    protected $table = 'clasificacion_verde';
    protected $primaryKey = 'id_clasificacion_verde';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_clasificacion_verde',
        'fecha_registro',
        'estado',
        'fecha_ingreso',
        'id_semana',
        'activo',
        'personal',
        'hora_inicio',
    ];

    public function lotes_re()
    {
        return $this->hasMany('\yura\Modelos\LoteRE', 'id_clasificacion_verde');
    }

    public function lotes_reByVariedad($variedad)
    {
        return LoteRE::All()->where('id_clasificacion_verde', '=', $this->id_clasificacion_verde)->where('id_variedad', '=', $variedad);
    }

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetalleClasificacionVerde', 'id_clasificacion_verde');
    }

    public function recepciones()
    {
        return $this->hasMany('\yura\Modelos\RecepcionClasificacionVerde', 'id_clasificacion_verde');
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }

    public function tallos_x_variedad($variedad)
    {
        $r = 0;
        foreach ($this->detalles as $item) {
            if ($item->id_variedad == $variedad)
                $r += ($item->cantidad_ramos * $item->tallos_x_ramos);
        }
        return $r;
    }

    public function total_tallos()
    {
        $r = 0;
        foreach ($this->detalles as $item) {
            $r += ($item->cantidad_ramos * $item->tallos_x_ramos);
        }
        return $r;
    }

    public function total_tallos_rendimiento()
    {
        $r = 0;
        foreach (getDetallesVerdeByFecha($this->fecha_ingreso) as $item) {
            $r += ($item->cantidad_ramos * $item->tallos_x_ramos);
        }
        return $r;
    }

    public function total_tallos_rendimientoByVariedad($variedad)
    {
        $r = 0;
        foreach (getDetallesVerdeByFechaVariedad($this->fecha_ingreso, $variedad) as $item) {
            $r += ($item->cantidad_ramos * $item->tallos_x_ramos);
        }
        return $r;
    }

    public function total_tallos_recepcion()
    {
        $r = 0;
        foreach (DB::table('recepcion')->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')->get() as $item) {
            $r += getRecepcion($item->id_recepcion)->cantidad_tallos();
        }
        return $r;
    }

    public function total_tallos_recepcionByVariedad($variedad)
    {
        $r = 0;
        foreach (DB::table('recepcion')->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')->get() as $item) {
            $r += getRecepcion($item->id_recepcion)->tallos_x_variedad($variedad);
        }
        return $r;
    }

    public function total_ramos()
    {
        $r = 0;
        foreach ($this->detalles as $item) {
            $r += $item->cantidad_ramos;
        }
        return $r;
    }

    public function desecho()
    {
        $total = 0;
        foreach ($this->recepciones as $item) {
            $total += $item->recepcion->cantidad_tallos();
        }

        if ($total > 0)
            return round(100 - round(($this->total_tallos() * 100) / $total, 2), 2);
        else
            return 0;
    }

    public function desechoByVariedad($variedad)
    {
        if ($this->total_tallos_recepcionByVariedad($variedad) > 0)
            return round(100 - (($this->tallos_x_variedad($variedad) * 100) / $this->total_tallos_recepcionByVariedad($variedad)), 2);
        else return 0;
    }

    public function getCalibre()
    {
        if ($this->getTotalRamosEstandar() > 0) {
            return round($this->total_tallos() / $this->getTotalRamosEstandar(), 2);
        } else
            return 0;
    }

    public function calibreByVariedad($variedad)
    {
        if ($this->getTotalRamosEstandarByVariedad($variedad) > 0)
            return round($this->tallos_x_variedad($variedad) / $this->getTotalRamosEstandarByVariedad($variedad), 2);
        else
            return 0;
    }

    public function getRamosByvariedadUnitaria($variedad, $unitaria)
    {
        $r = 0;
        foreach ($this->detalles as $detalle) {
            if ($detalle->id_variedad == $variedad && $detalle->id_clasificacion_unitaria == $unitaria) {
                $r += $detalle->cantidad_ramos;
            }
        }
        return $r;
    }

    public function getTallosByvariedadUnitaria($variedad, $unitaria)
    {
        $r = 0;
        foreach ($this->detalles as $detalle) {
            if ($detalle->id_variedad == $variedad && $detalle->id_clasificacion_unitaria == $unitaria) {
                $r += $detalle->cantidad_ramos * $detalle->tallos_x_ramos;
            }
        }
        return $r;
    }

    public function getTallosByvariedadUnitariaFecha($variedad, $unitaria, $fecha)
    {
        $query = DB::table('detalle_clasificacion_verde')
            ->where('estado', 1)
            ->where('id_clasificacion_verde', $this->id_clasificacion_verde)
            ->where('id_clasificacion_unitaria', $unitaria)
            ->where('id_variedad', $variedad)
            ->where('fecha_ingreso', 'like', $fecha . '%')
            ->get();

        $r = 0;
        foreach ($query as $detalle) {
            $r += $detalle->cantidad_ramos * $detalle->tallos_x_ramos;
        }
        return $r;
    }

    public function getPorcentajeUnitariaByVariedad($variedad, $unitaria)
    {
        $parte = $this->getTallosByvariedadUnitaria($variedad, $unitaria);
        return round(($parte * 100) / $this->tallos_x_variedad($variedad), 2);
    }

    public function getPorcentajeByVariedad($variedad)
    {
        $parte = $this->tallos_x_variedad($variedad);
        return round(($parte * 100) / $this->total_tallos(), 2);
    }

    public function variedades()
    {
        $l = DB::table('detalle_clasificacion_verde as d')
            ->select('d.id_variedad')->distinct()
            ->where('id_clasificacion_verde', '=', $this->id_clasificacion_verde)->get();
        $r = [];
        foreach ($l as $item) {
            array_push($r, Variedad::find($item->id_variedad));
        }
        return $r;
    }

    public function unitarias()
    {
        $l = DB::table('detalle_clasificacion_verde as d')
            ->select('d.id_clasificacion_unitaria')->distinct()
            ->join('clasificacion_unitaria as u', 'u.id_clasificacion_unitaria', '=', 'd.id_clasificacion_unitaria')
            ->where('id_clasificacion_verde', '=', $this->id_clasificacion_verde)
            ->orderBy('u.nombre', 'asc')
            ->get();
        $r = [];
        foreach ($l as $item) {
            array_push($r, ClasificacionUnitaria::find($item->id_clasificacion_unitaria));
        }
        return $r;
    }

    function getRendimiento()
    {
        if (count($this->detalles) > 0 && $this->personal > 0 && $this->getCantidadHorasTrabajo() > 0) {
            $r = $this->total_tallos_rendimiento() / $this->personal;
            $r = $r / $this->getCantidadHorasTrabajo();

            return round($r, 2);
        } else {
            return 0;
        }
    }

    function getRendimientoRamos()
    {
        if (count($this->detalles) > 0 && $this->personal > 0 && $this->getCantidadHorasTrabajo() > 0) {
            $r = $this->total_ramos() / $this->personal;
            $r = $r / $this->getCantidadHorasTrabajo();

            return round($r, 2);
        } else {
            return 0;
        }
    }

    function getRendimientoByVariedad($variedad)
    {
        if (count($this->detalles) > 0 && $this->personal > 0 && $this->getCantidadHorasTrabajoByVariedad($variedad) > 0) {
            $r = $this->total_tallos_rendimientoByVariedad($variedad) / $this->personal;
            $r = $r / $this->getCantidadHorasTrabajoByVariedad($variedad);

            return round($r, 2);
        } else {
            return 0;
        }
    }

    function getDetallesByFecha($fecha)
    {
        $listado = DetalleClasificacionVerde::All()
            ->where('estado', '=', 1)
            //->where('id_clasificacion_verde', '=', $this->id_clasificacion_verde)
            ->where('fecha_ingreso', '=', $fecha)
            ->sortBy('id_variedad');
        return $listado;
    }

    function getIntervalosHoras()
    {
        $r = [];
        $listado_fechas = DB::table('detalle_clasificacion_verde')
            ->select('fecha_registro')->distinct()
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
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

    function getVariedadesByIntervaloFecha($inicio, $fin)
    {
        $listado = DB::table('detalle_clasificacion_verde as dc')
            ->select('dc.id_variedad')->distinct()
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->where('fecha_registro', '>=', $inicio)
            ->where('fecha_registro', '<', $fin)
            ->get();

        return $listado;
    }

    function getDetallesByIntervaloFecha($inicio, $fin)
    {
        $listado = DB::table('detalle_clasificacion_verde as dc')
            ->select('dc.id_variedad', 'dc.id_clasificacion_unitaria', DB::raw('sum(cantidad_ramos * tallos_x_ramos) as cantidad'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->where('fecha_registro', '>=', $inicio)
            ->where('fecha_registro', '<', $fin)
            ->groupBy('dc.id_variedad', 'dc.id_clasificacion_unitaria')
            ->orderBy('dc.id_variedad')
            ->get();

        return $listado;
    }

    function getLastFechaClasificacion()
    {
        $r = DB::table('detalle_clasificacion_verde')
            ->select(DB::raw('max(fecha_registro) as fecha'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->get();
        if (count($r) > 0)
            return $r[0]->fecha;
        else
            return '';
    }

    function getLastFechaClasificacionByVariedad($variedad)
    {
        $r = DB::table('detalle_clasificacion_verde')
            ->select(DB::raw('max(fecha_registro) as fecha'))
            ->where('id_variedad', '=', $variedad)
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->get();
        if (count($r) > 0)
            return $r[0]->fecha;
        else
            return '';
    }

    function getFechaHoraInicio()
    {
        return $this->fecha_ingreso . ' ' . $this->hora_inicio . ':00';
    }

    function getCantidadHorasTrabajo()
    {
        $r = difFechas($this->getLastFechaClasificacion(), $this->getFechaHoraInicio());
        return round($r->h + ($r->i / 60), 2);
    }

    function getCantidadHorasTrabajoByVariedad($variedad)
    {
        $r = difFechas($this->getLastFechaClasificacionByVariedad($variedad), $this->getFechaHoraInicio());
        return round($r->h + ($r->i / 60), 2);
    }

    function getTotalTallosByVariedadIntervaloFecha($variedad, $inicio, $fin)
    {
        $r = DB::table('detalle_clasificacion_verde as dc')
            ->select(DB::raw('sum(cantidad_ramos * tallos_x_ramos) as cantidad'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->where('id_variedad', '=', $variedad)
            ->where('fecha_registro', '>=', $inicio)
            ->where('fecha_registro', '<', $fin)
            ->groupBy('dc.id_variedad')
            ->orderBy('dc.id_variedad')
            ->get();

        if (count($r) > 0)
            return $r[0]->cantidad;
        else
            return 0;
    }

    function getTotalTallosByIntervalo($inicio, $fin)
    {
        $r = DB::table('detalle_clasificacion_verde as dc')
            ->select(DB::raw('sum(cantidad_ramos * tallos_x_ramos) as cantidad'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->where('fecha_registro', '>=', $inicio)
            ->where('fecha_registro', '<', $fin)
            ->get();

        if (count($r) > 0)
            return $r[0]->cantidad;
        else
            return 0;
    }

    function getTotalRamosEstandar()
    {
        $r = 0;
        $listado = DB::table('detalle_clasificacion_verde as d')
            ->join('variedad as v', 'v.id_variedad', '=', 'd.id_variedad')
            ->select('d.id_variedad', 'd.id_clasificacion_unitaria')->distinct()
            ->where('d.id_clasificacion_verde', '=', $this->id_clasificacion_verde)
            ->orderBy('v.nombre', 'asc')->get();

        foreach ($listado as $item) {
            $r += round($this->getTallosByvariedadUnitaria($item->id_variedad, $item->id_clasificacion_unitaria) /
                explode('|', getUnitaria($item->id_clasificacion_unitaria)->nombre)[1], 2);
        }
        return $r;
    }

    function getTotalCajas()
    {
        return round($this->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
    }

    function getTotalCajasByVariedad($variedad)
    {
        return round($this->getTotalRamosEstandarByVariedad($variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
    }

    function getTotalRamosEstandarByVariedad($variedad)
    {
        $r = 0;
        $listado = DB::table('detalle_clasificacion_verde as d')
            ->join('variedad as v', 'v.id_variedad', '=', 'd.id_variedad')
            ->select('d.id_variedad', 'd.id_clasificacion_unitaria')->distinct()
            ->where('d.id_clasificacion_verde', '=', $this->id_clasificacion_verde)
            ->where('d.id_variedad', '=', $variedad)
            ->orderBy('v.nombre', 'asc')->get();

        foreach ($listado as $item) {
            $r += round($this->getTallosByvariedadUnitaria($item->id_variedad, $item->id_clasificacion_unitaria) /
                explode('|', getUnitaria($item->id_clasificacion_unitaria)->nombre)[1], 2);
        }
        return $r;
    }

    function getUnitariasFechaByVariedad($variedad)
    {
        $query = DB::table('detalle_clasificacion_verde')
            ->select('fecha_ingreso')->distinct()
            ->where('estado', 1)
            ->where('id_clasificacion_verde', $this->id_clasificacion_verde)
            ->where('id_variedad', $variedad)
            ->get();

        $fechas = [];
        foreach ($query as $item) {
            if (!in_array(substr($item->fecha_ingreso, 0, 10), $fechas))
                array_push($fechas, substr($item->fecha_ingreso, 0, 10));
        }

        $list = [];
        foreach ($fechas as $f) {
            $unitarias = DB::table('detalle_clasificacion_verde')
                ->select('id_clasificacion_unitaria')->distinct()
                ->where('estado', 1)
                ->where('id_clasificacion_verde', $this->id_clasificacion_verde)
                ->where('id_variedad', $variedad)
                ->where('fecha_ingreso', 'like', $f . '%')
                ->get();

            foreach ($unitarias as $u) {
                array_push($list, [
                    'unitaria' => $u->id_clasificacion_unitaria,
                    'fecha' => $f,
                ]);
            }
        }

        return $list;
    }
}