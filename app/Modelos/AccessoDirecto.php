<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class AccessoDirecto extends Model
{
    protected $table = 'acceso_directo';
    protected $primaryKey = 'id_acceso_directo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_submenu',
        'id_icono',
    ];

    public function usuario()
    {
        return $this->belongsTo('\yura\Modelos\Usuario', 'id_usuario');
    }

    public function submenu()
    {
        return $this->belongsTo('\yura\Modelos\Submenu', 'id_submenu');
    }

    public function icono()
    {
        return $this->belongsTo('\yura\Modelos\Icon', 'id_icono');
    }
}
