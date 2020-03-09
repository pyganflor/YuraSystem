<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Monitoreo extends Model
{
    protected $table = 'monitoreo';
    protected $primaryKey = 'id_monitoreo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'num_sem',  // int, que indica el número de la semana del ciclo
        'estado',
        'id_ciclo',
        'altura',
    ];

    public function ciclo()
    {
        return $this->belongsTo('\yura\Modelos\Ciclo', 'id_ciclo');
    }
}
