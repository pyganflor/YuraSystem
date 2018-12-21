<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Grosor extends Model
{
    protected $table = 'grosor_ramo';
    protected $primaryKey = 'id_grosor_ramo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado'
    ];
}
