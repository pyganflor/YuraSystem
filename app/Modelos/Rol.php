<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_rol',
        'nombre',
        'fecha_registro',
        'estado',
        'tipo',
    ];

    public function submenus()
    {
        return $this->hasMany('\yura\Modelos\Rol_Submenu', 'id_rol');
    }

    public function usuarios()
    {
        return $this->hasMany('\yura\Modelos\Usuario', 'id_rol');
    }

    public function getSubmenusByTipo($tipo)
    {
        $r = [];
        foreach ($this->submenus as $s)
            if ($s->submenu->tipo == $tipo)
                array_push($r, $s->submenu);
        return $r;
    }
}