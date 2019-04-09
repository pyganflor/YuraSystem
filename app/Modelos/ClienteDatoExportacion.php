<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ClienteDatoExportacion extends Model
{
    protected $table = 'cliente_datoexportacion';
    protected $primaryKey = 'id_cliente_datoexportacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_dato_exportacion',
        'id_cliente'
    ];

    public function datos_exportacion()
    {
        return $this->belongsTo('\yura\Modelos\DatosExportacion', 'id_dato_exportacion');
    }

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }
}
