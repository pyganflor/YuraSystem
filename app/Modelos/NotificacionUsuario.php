<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class NotificacionUsuario extends Model
{
    protected $table = 'notificacion_usuario';
    protected $primaryKey = 'id_notificacion_usuario';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_notificacion',
        'id_usuario',
        'estado',
    ];

    public function notificacion()
    {
        return $this->belongsTo('\yura\Modelos\Notificacion', 'id_notificacion');
    }

    public function usuario()
    {
        return $this->belongsTo('\yura\Modelos\Usuario', 'id_usuario');
    }
}
