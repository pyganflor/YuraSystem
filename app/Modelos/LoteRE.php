<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class LoteRE extends Model
{
    protected $table = 'lote_re';
    protected $primaryKey = 'id_lote_re';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_lote_re',
        'cantidad_tallos',
        'fecha_registro',
        'estado',
        'dias_guarde_clasificacion',
        'dias_guarde_apertura',
        'id_variedad',
        'id_clasificacion_unitaria',
        'id_clasificacion_verde',
        'etapa',    //A => Apertura, C => Guarde Clasificacion, G => Guarde Apertura, F => StockFrio, E => Empaquetado
        'guarde_clasificacion', //date
        'apertura', //date
        'guarde_apertura',  //date
        'empaquetado',  //date
        'stock_frio',  //date
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function clasificacion_verde()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionVerde', 'id_clasificacion_verde');
    }

    public function clasificacion_unitaria()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionUnitaria', 'id_clasificacion_unitaria');
    }

    public function stock_apertura()
    {
        return $this->hasOne('\yura\Modelos\StockApertura', 'id_lote_re');
    }

    public function getCurrentFecha()
    {
        $fechas = [
            'A' => $this->apertura,
            'C' => $this->guarde_clasificacion,
            'G' => $this->guarde_apertura,
            'E' => $this->empaquetado,
        ];

        return $fechas['' . $this->etapa];
    }

    public function getCurrentEtapa()
    {
        $etapas = [
            'A' => 'Apertura',
            'C' => 'Guarde (clasificación)',
            'G' => 'Guarde (apertura)',
            'E' => 'Empaquetado',
        ];

        return $etapas['' . $this->etapa];
    }

    public function getCurrentDiasEstimados()
    {
        $dias_apertura = $this->etapa == 'A' ? $this->stock_apertura->dias : '0';

        $dias = [
            'A' => $dias_apertura,
            'C' => $this->dias_guarde_clasificacion,
            'G' => $this->dias_guarde_apertura,
            'E' => '-',
        ];

        return $dias['' . $this->etapa];
    }

    public function getCurrentEstancia()
    {
        return difFechas(date('Y-m-d'), $this->getCurrentFecha())->days;
    }

    public function getEstanciaByEtapa($etapa)
    {
        if ($etapa == 'C') {
            return difFechas($this->apertura, $this->guarde_clasificacion)->days;
        }
        if ($etapa == 'A') {
            if ($this->guarde_apertura != '')
                return difFechas($this->guarde_apertura, $this->apertura)->days;
            if ($this->empaquetado != '')
                return difFechas($this->empaquetado, $this->apertura)->days;
        }
        if ($etapa == 'G') {
            return difFechas($this->empaquetado, $this->guarde_apertura)->days;
        }
        return false;
    }
}