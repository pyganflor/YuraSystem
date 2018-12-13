<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    protected $table = 'submenu';
    protected $primaryKey = 'id_submenu';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_submenu',
        'nombre',
        'url',
        'id_menu',
        'fecha_registro',
        'estado',
    ];

    public function menu()
    {
        return $this->belongsTo('\yura\Modelos\Menu', 'id_menu');
    }
}