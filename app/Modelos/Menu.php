<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_menu',
        'nombre',
        'id_grupo_menu',
        'fecha_registro',
        'estado',
        'id_icono',
    ];

    public function icono()
    {
        return $this->belongsTo('\yura\Modelos\Icon', 'id_icono');
    }

    public function grupo_menu()
    {
        return $this->belongsTo('\yura\Modelos\GrupoMenu', 'id_grupo_menu');
    }

    public function submenus()
    {
        return $this->hasMany('\yura\Modelos\Submenu', 'id_menu')->orderBy('nombre');
    }

    public function submenus_activos()
    {
        return $this->hasMany('\yura\Modelos\Submenu', 'id_menu')->where('estado', '=', 'A')->orderBy('nombre');
    }
}