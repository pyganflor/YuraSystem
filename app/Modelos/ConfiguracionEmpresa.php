<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionEmpresa extends Model
{
    protected $table = 'configuracion_empresa';
    protected $primaryKey = 'id_configuracion_empresa';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_configuracion_empresa',
        'nombre',
        'cantidad_usuarios',
        'cantidad_hectareas',
        'propagacion',
        'campo',
        'postcocecha',  // 'recepcion|clasificacion en verde|apertura|clasificacion en blanco|frio'
        'fecha_registro',
        'estado',
        'tallos_x_ramo',
        'unidad_medida',
        'ramos_x_caja', // pendiente por programar el
        'codigo_pais',
        'razon_social',
        'direccion_matriz',
        'direccion_establecimiento',
        'moneda',
        'baldes_x_coche',    // baldes por coche para sacar en aperturas
        'correo',
        'fax',
        'telefono',
        'permiso_agrocalidad',
        'ruc',
        'firma_electronica',
        'imagen',
        'obligado_contabilidad',
        'contrasena_firma_digital',
        'inicial_factura',
        'inicial_guia_remision',
        'inicial_lote',
        'inicial_despahco',
        'codigo_fpo',
        'codigo_tvn',
        'codigo_etiqueta_empresa'

    ];

    public function clasificaciones_unitarias()
    {
        return $this->hasMany('\yura\Modelos\ClasificacionUnitaria', 'id_configuracion_empresa')
            ->where('estado', '=', 1);
    }

    public function clasificaciones_ramos()
    {
        return $this->hasMany('\yura\Modelos\ClasificacionRamo', 'id_configuracion_empresa')
            ->where('estado', '=', 1);
    }
}
