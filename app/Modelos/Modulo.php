<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Modulo extends Model
{
    protected $table = 'modulo';
    protected $primaryKey = 'id_modulo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'nombre',   // unico
        'fecha_registro',
        'estado',
        'area', // float
        'descripcion',
        'id_sector',
    ];

    public function sector()
    {
        return $this->belongsTo('\yura\Modelos\Sector', 'id_sector');
    }

    public function ciclos()
    {
        return $this->hasMany('\yura\Modelos\Ciclo', 'id_modulo');
    }

    public function proyecciones()
    {
        return $this->hasMany('\yura\Modelos\ProyeccionModulo', 'id_modulo');
    }

    public function cicloActual()
    {
        foreach ($this->ciclos as $c) {
            if ($c->activo == 1)
                return $c;
        }
        return '';
    }

    public function lotes()
    {
        return $this->hasMany('\yura\Modelos\Lote', 'id_modulo');
    }

    public function lotes_activos()
    {
        return $this->hasMany('\yura\Modelos\Lote', 'id_modulo')->where('estado', '=', 1);
    }

    public function getPodaSiembraActual()
    {
        if ($this->cicloActual() != '') {
            if ($this->cicloActual()->poda_siembra == 'S')
                return 0;
            else {
                $ciclos = Ciclo::All()
                    ->where('id_modulo', $this->id_modulo)
                    ->where('estado', 1)
                    ->sortByDesc('fecha_inicio');

                $r = 0;
                foreach ($ciclos as $c) {
                    if ($c->poda_siembra == 'P')
                        $r++;
                    else
                        break;
                }
                return $r;
            }
        } else
            return '';
    }

    public function getPodaSiembraByCiclo($ciclo)
    {
        if (Ciclo::find($ciclo)->estado == 1) {
            $ciclos = Ciclo::All()
                ->where('id_modulo', $this->id_modulo)
                ->where('estado', 1)
                ->where('fecha_inicio', '<=', Ciclo::find($ciclo)->fecha_inicio)
                ->sortByDesc('fecha_inicio');

            $r = 0;
            foreach ($ciclos as $c) {
                if ($c->poda_siembra == 'P')
                    $r++;
                else
                    break;
            }
            return $r;
        } else
            return '';
    }

    public function getCicloByFecha($fecha)
    {
        $ciclo = DB::table('ciclo')
            ->select('id_ciclo as id')
            ->where('id_modulo', '=', $this->id_modulo)
            ->where('estado', '=', 1)
            ->where('fecha_inicio', '<', $fecha)
            ->orderByDesc('fecha_inicio')
            ->first();

        if ($ciclo != '')
            return Ciclo::find($ciclo->id);
        return '';
    }

    public function getDataBySemana($tiempo, $semana, $variedad, $desde)
    {
        $data = [
            'tipo' => 'otro'
        ];
        $ciclo_ini = $this->ciclos->where('estado', 1)
            ->where('fecha_inicio', '>=', $semana->fecha_inicial)->where('fecha_inicio', '<=', $semana->fecha_final)
            ->where('id_variedad', $variedad)->first();
        if ($ciclo_ini != '') { // esa semana inició un ciclo
            $data = [
                'tipo' => $ciclo_ini->poda_siembra,
                'info' => $ciclo_ini->poda_siembra . '-' . $this->getPodaSiembraByCiclo($ciclo_ini->id_ciclo)
            ];
        } else {
            $ciclo_last = $this->ciclos->where('estado', 1)
                ->where('fecha_inicio', '<=', $semana->fecha_inicial)
                ->where('id_variedad', $variedad)->sortBy('fecha_inicio')->last();

            $data = [
                'tipo' => 'V',  // vacio
                'info' => '',
            ];
            if ($ciclo_last != '') {
                if ($ciclo_last->fecha_inicio >= $desde) {
                    if ($ciclo_last->activo == 1 || ($ciclo_last->activo == 0 && $ciclo_last->fecha_fin >= $semana->fecha_inicial)) {
                        $fecha_inicio = getSemanaByDate($ciclo_last->fecha_inicio)->fecha_inicial;
                        $data = [
                            'tipo' => 'I',  // informacion
                            'info' => (intval(difFechas($semana->fecha_inicial, $fecha_inicio)->days / 7) + 1) . 'º',
                        ];
                    } else {
                        $data = [
                            'tipo' => 'F',  // fin de ciclo
                            'info' => 'F',
                        ];
                    }
                }
            }
        }
        return $data;
    }
}