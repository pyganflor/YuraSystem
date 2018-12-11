<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class GrupoMenu extends Model
{
    protected $table = 'grupo_menu';
    protected $primaryKey = 'id_grupo_menu';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_grupo_menu',
        'nombre',
        'fecha_registro',
        'estado',
    ];

    public function menus()
    {
        return $this->hasMany('\yura\Modelos\Menu', 'id_grupo_menu')->orderBy('nombre');
    }

    public function menus_activos()
    {
        return $this->hasMany('\yura\Modelos\Menu', 'id_grupo_menu')->where('estado', '=', 'A')->orderBy('nombre');
    }
}
