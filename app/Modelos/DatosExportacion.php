<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DatosExportacion extends Model
{
    protected $table = 'dato_exportacion';
    protected $primaryKey = 'id_dato_exportacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado'
    ];
}
