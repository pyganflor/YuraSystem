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
        'poda_siembra', // char(1) Poda, Siembra
        'plantas_iniciales',
        'plantas_muertas',
        'conteo',
        'curva',
        'semana_poda_siembra',
        'desecho',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function modulo()
    {
        return $this->belongsTo('\yura\Modelos\Modulo', 'id_modulo');
    }

    public function monitoreos()
    {
        return $this->hasMany('\yura\Modelos\Monitoreo', 'id_ciclo');
    }

    public function temperaturas()
    {
        return $this->hasMany('\yura\Modelos\CicloTemperatura', 'id_ciclo');
    }

    public function getTallosCosechados($dias_ini = 1)  // dias_ini: Dias despues del inicio de ciclo a tener en cuenta
    {
        $fin = date('Y-m-d');
        if ($this->fecha_fin != '')
            $fin = $this->fecha_fin;

        $r = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cantidad'))
            ->where('dr.estado', '=', 1)
            ->where('r.estado', '=', 1)
            ->where('dr.id_modulo', '=', $this->id_modulo)
            ->where('r.fecha_ingreso', '>=', opDiasFecha('+', $dias_ini, $this->fecha_inicio))
            ->where('r.fecha_ingreso', '<=', $fin . ' 23:59:59')
            ->get()[0]->cantidad;

        return $r;
    }

    public function getTallosCosechadosByFecha($fecha)
    {
        $r = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cantidad'))
            ->where('dr.estado', '=', 1)
            ->where('r.estado', '=', 1)
            ->where('dr.id_modulo', '=', $this->id_modulo)
            ->where('r.fecha_ingreso', 'like', $fecha . '%')
            ->where('r.fecha_ingreso', '>=', opDiasFecha('+', 1, $this->fecha_inicio))
            ->get()[0]->cantidad;

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
            ->where('c.fecha_ingreso', '>=', opDiasFecha('+', 1, $this->fecha_inicio));
        if ($this->fecha_fin != '') {
            $cosechas = $cosechas->where('c.fecha_ingreso', '<=', $this->fecha_fin);
        }
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

    public function getMortalidad()
    {
        if ($this->plantas_actuales() > 0 && $this->plantas_iniciales > 0) {
            $r = ($this->plantas_actuales() / $this->plantas_iniciales) * 100;
            return round(100 - $r, 2);
        }
        return 0;
    }

    public function getDensidadIniciales()
    {
        return round($this->plantas_iniciales / $this->area, 2);
    }

    public function plantas_actuales()
    {
        if ($this->plantas_iniciales > 0)
            if ($this->plantas_muertas > 0)
                return $this->plantas_iniciales - $this->plantas_muertas;
            else
                return $this->plantas_iniciales;
        return 0;
    }

    public function semana()
    {
        return Semana::All()
            ->where('estado', 1)
            ->where('id_variedad', $this->id_variedad)
            ->where('fecha_inicial', '<=', $this->fecha_inicio)
            ->where('fecha_final', '>=', $this->fecha_inicio)
            ->first();
    }

    public function getTallosProyectados()
    {
        return round(($this->plantas_actuales() * $this->conteo) * ((100 - $this->desecho) / 100), 2);
    }
}