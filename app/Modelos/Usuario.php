<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'nombre_completo',
        'correo',
        'username',
        'password',
        'fecha_registro',
        'estado',
        'id_rol',
        'imagen_perfil',
    ];

    public function rol()
    {
        return Rol::find($this->id_rol);
    }

    public function accesos_directos()
    {
        return $this->hasMany('\yura\Modelos\AccessoDirecto', 'id_usuario');
    }

    public function getIdSubmenusAccesoDirecto()
    {
        $r = [];
        foreach ($this->accesos_directos as $item) {
            array_push($r, $item->id_submenu);
        }
        return $r;
    }

    public function configuracion()
    {
        return $this->hasOne('\yura\Modelos\ConfiguracionUser', 'id_usuario');
    }

    public function notificaciones()
    {
        return $this->hasMany('\yura\Modelos\NotificacionUsuario', 'id_usuario');
    }

    public function cantidades()
    {
        return 3;
    }
}
