<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'id_bitacora';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_bitacora',
        'tabla',
        'codigo',
        'fecha_creacion',
        'accion',
        'id_usuario',
        'observacion',
        'ip',
    ];

    public function usuario()
    {
        return $this->belongsTo('\yura\Modelos\Usuario', 'id_usuario');
    }
}
