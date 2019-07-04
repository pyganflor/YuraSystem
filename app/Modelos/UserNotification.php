<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'user_notification';
    protected $primaryKey = 'id_user_notification';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_notificacion',
        'fecha_registro',
        'estado',
        'titulo',
        'texto',
        'url',
    ];

    public function usuario()
    {
        return $this->belongsTo('\yura\Modelos\Usuario', 'id_usuario');
    }

    public function notificacion()
    {
        return $this->belongsTo('\yura\Modelos\Notificacion', 'id_notificacion');
    }
}