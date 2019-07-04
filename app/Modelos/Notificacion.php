<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';
    protected $primaryKey = 'id_notificacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_notificacion',
        'nombre',
        'tipo',
        'id_icono',
        'estado',
    ];

    public function icono()
    {
        return $this->belongsTo('\yura\Modelos\Icon', 'id_icono');
    }

    public function usuarios()
    {
        return $this->hasMany('\yura\Modelos\NotificacionUsuario', 'id_notificacion');
    }

    public function getNotificacionUsuario($id_user)
    {
        $r = $this->usuarios->where('id_usuario', $id_user)->first();
        return $r;
    }
}