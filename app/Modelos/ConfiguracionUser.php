<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionUser extends Model
{
    protected $table = 'configuracion_user';
    protected $primaryKey = 'id_configuracion_user';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_configuracion_user',
        'fixed_layout', // char S,N
        'boxed_layout', // char S,N
        'toggle_color_config', // char S,N
        'config_online', // char S,N
        'skin', // varchar 250
        'fecha_registro',
        'id_usuario',
    ];

    public function usuario()
    {
        return $this->belongsTo('\yura\Modelos\Usuario', 'id_usuario');
    }
}
