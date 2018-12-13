<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Recepcion extends Model
{
    protected $table = 'recepcion';
    protected $primaryKey = 'id_recepcion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_recepcion',
        'id_semana',
        'fecha_ingreso',
        'fecha_registro',
        'estado',
    ];

    public function desgloses()
    {
        return $this->hasMany('\yura\Modelos\DesgloseRecepcion', 'id_recepcion')->where('estado', '=', 1);
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }

    public function clasificaciones_verdes()
    {
        return $this->hasMany('\yura\Modelos\RecepcionClasificacionVerde', 'id_recepcion');
    }

    public function cantidad_tallos()
    {
        $r = 0;
        foreach ($this->desgloses as $item) {
            $r += $item->cantidad_mallas * $item->tallos_x_malla;
        }
        return $r;
    }

    public function tallos_x_variedad($variedad)
    {
        $r = 0;
        foreach ($this->desgloses as $item) {
            if ($item->id_variedad == $variedad)
                $r += $item->cantidad_mallas * $item->tallos_x_malla;
        }
        return $r;
    }

    public function variedades()
    {
        $r = DB::table('desglose_recepcion as d')
            ->select('d.id_variedad')->distinct()
            ->where('d.id_recepcion', '=', $this->id_recepcion)
            ->where('d.estado', '=', 1)->get();
        $rr = [];
        foreach ($r as $item) {
            array_push($rr, Variedad::find($item->id_variedad));
        }
        return $rr;
    }

    public function total_x_variedad($variedad)
    {
        $r = 0;
        foreach ($this->desgloses as $item) {
            if ($item->id_variedad == $variedad)
                $r += ($item->cantidad_mallas * $item->tallos_x_malla);
        }
        return $r;
    }

    public function totalRamos_clasificacionVerde()
    {
        $r = 0;
        foreach ($this->clasificaciones_verde as $item) {
            $r += $item->cantidad_ramos;
        }
        return $r;
    }

    public function totalTallos_clasificacionVerde()
    {
        $r = 0;
        foreach ($this->clasificaciones_verde as $item) {
            $r += ($item->cantidad_ramos * $item->tallos_x_ramo);
        }
        return $r;
    }

    public function clasificacionesVerdeByVariedad($variedad)
    {
        $r = ClasificacionVerde::All()
            ->where('id_recepcion', '=', $this->id_recepcion)
            ->where('id_variedad', '=', $variedad);
        return $r;
    }

    public function ramosClasificacionVerdeByVariedad($variedad)
    {
        $r = 0;
        $q = ClasificacionVerde::All()
            ->where('id_recepcion', '=', $this->id_recepcion)
            ->where('id_variedad', '=', $variedad);
        foreach ($q as $item) {
            $r += $item->cantidad_ramos;
        }
        return $r;
    }

    public function tallosClasificacionVerdeByVariedad($variedad)
    {
        $r = 0;
        $q = ClasificacionVerde::All()
            ->where('id_recepcion', '=', $this->id_recepcion)
            ->where('id_variedad', '=', $variedad);
        foreach ($q as $item) {
            $r += ($item->cantidad_ramos * $item->tallos_x_ramo);
        }
        return $r;
    }
}
