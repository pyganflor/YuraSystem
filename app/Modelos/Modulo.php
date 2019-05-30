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
}