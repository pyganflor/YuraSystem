<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'id_area';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'fecha_registro',
    ];

    public function otros_gastos()
    {
        return $this->hasMany('\yura\Modelos\OtrosGastos', 'id_area');
    }

    public function actividades()
    {
        return $this->hasMany('\yura\Modelos\Actividad', 'id_area');
    }

    function otrosGastosBySemana($semana)
    {
        return $this->otros_gastos->where('codigo_semana', $semana)->first();
    }

    function getOtrosGastosLastSemana($semana)
    {
        $r = DB::table('otros_gastos')
            ->select('codigo_semana', 'gip', 'ga')
            ->where('id_area', $this->id_area)
            ->where('codigo_semana', '<', $semana)
            ->orderBy('codigo_semana')
            ->get();
        return count($r) > 0 ? $r[count($r) - 1] : '';
    }
}