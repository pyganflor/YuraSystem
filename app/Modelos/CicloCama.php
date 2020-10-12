<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CicloCama extends Model
{
    protected $table = 'ciclo_cama';
    protected $primaryKey = 'id_ciclo_cama';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cama',
        'fecha_registro',
        'activo',
        'fecha_inicio',
        'fecha_fin',
        'plantas_muertas',
        'esq_x_planta', // conteo de la cantidad de esquejes que se cosecha por semana
        'id_variedad',
        'semana_cosecha',
        'total_semanas_cosecha',
    ];

    public function cama()
    {
        return $this->belongsTo('\yura\Modelos\Cama', 'id_cama');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function contenedores()
    {
        return $this->hasMany('\yura\Modelos\CicloCamaContenedor', 'id_ciclo_cama');
    }

    public function semana_ini()
    {
        return getSemanaByDate($this->fecha_inicio);
    }

    public function semana_vida()
    {
        return round(difFechas($this->fecha_fin, $this->fecha_inicio)->days / 7);
    }

    public function getPlantasProductivas()
    {
        $valor = 0;
        foreach ($this->contenedores as $c) {
            $valor += $c->cantidad * $c->contenedor->cantidad;
        }
        return $valor;
    }

    public function getDiasVida()
    {
        return difFechas(date('Y-m-d'), $this->fecha_inicio)->days;
    }

    public function getEsquejesCosechados()
    {
        return DB::table('cosecha_plantas_madres')
            ->select(DB::raw('sum(cantidad) as cant'))
            ->where('id_cama', $this->id_cama)
            ->where('fecha', '>', $this->fecha_inicio)
            ->get()[0]->cant;
    }

    public function getCicloContenedorByContenedor($contenedores, $id_cont)
    {
        foreach ($contenedores as $cont) {
            if ($cont->id_contenedor_propag == $id_cont)
                return $cont;
        }
        return '';
    }

    public function getPorcentajeCosechado()
    {
        $total = $this->getPlantasProductivas() * $this->esq_x_planta * 1;
        $cosechado = $this->getEsquejesCosechados();
        return round(($cosechado * 100) / $total, 2);
    }
}
