<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Rol_Submenu extends Model
{
    protected $table = 'rol_submenu';
    protected $primaryKey = 'id_rol_submenu';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_rol_submenu',
        'id_rol',
        'id_submenu',
        'fecha_registro',
        'estado',
    ];

    public function rol()
    {
        return $this->belongsTo('\yura\Modelos\Rol', 'id_rol');
    }

    public function submenu()
    {
        return $this->belongsTo('\yura\Modelos\Submenu', 'id_submenu');
    }
}
