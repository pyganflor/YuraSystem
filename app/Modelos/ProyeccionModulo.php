<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ProyeccionModulo extends Model
{
    protected $table = 'proyeccion_modulo';
    protected $primaryKey = 'id_proyeccion_modulo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'id_semana',
        'fecha_registro',
        'estado',
        'id_variedad',
        'tipo', // Poda, Siembra, Cerrado
        'curva',
        'semana_poda_siembra',
        'poda_siembra', // numero de poda o 0 si es siembra
        'plantas_iniciales',
        'desecho',
        'tallos_planta',
        'tallos_ramo',
        'fecha_inicio',
    ];

    public function modulo()
    {
        return $this->belongsTo('\yura\Modelos\Modulo', 'id_modulo');
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function restaurar_proyecciones()
    {
        $sum_semana = intval($this->semana_poda_siembra) + intval(count(explode('-', $this->curva)));
        $semana = $this->semana;
        $codigo = $semana->codigo;
        $new_codigo = $semana->codigo;
        $i = 1;
        $next = 1;
        while ($i < $sum_semana && $new_codigo <= getLastSemanaByVariedad($this->id_variedad)->codigo) {
            $new_codigo = $codigo + $next;
            $query = Semana::All()
                ->where('estado', '=', 1)
                ->where('codigo', '=', $new_codigo)
                ->where('id_variedad', '=', $this->id_variedad)
                ->first();

            if ($query != '') {
                $i++;
            }
            $next++;
        }

        $next = ProyeccionModulo::All()
            ->where('estado', 1)
            ->where('id_modulo', $this->id_modulo)
            ->where('fecha_inicio', '>', $this->fecha_inicio);

        if (count($next) > 0) {
            if ($query != '') {
                $proy = new ProyeccionModulo();
                $proy->id_modulo = $this->id_modulo;
                $proy->id_semana = $query->id_semana;
                $proy->id_variedad = $this->id_variedad;
                $proy->tipo = 'P';
                $proy->curva = $this->curva;
                $proy->semana_poda_siembra = $this->semana_poda_siembra;
                $proy->poda_siembra = $this->poda_siembra + 1;
                $proy->plantas_iniciales = $this->plantas_iniciales != '' ? $this->plantas_iniciales : 0;
                $proy->desecho = $this->desecho;
                $proy->tallos_planta = $this->tallos_planta != '' ? $this->tallos_planta : 0;
                $proy->tallos_ramo = $query->tallos_ramo != '' ? $query->tallos_ramo : 0;
                $proy->fecha_inicio = $query->fecha_final;

                $proy->save();
            }
        }

        foreach ($next as $proy) {
            $proy->delete();
        }
    }
}
