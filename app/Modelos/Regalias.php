<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Regalias extends Model
{
    protected $table = 'regalias';
    protected $primaryKey = 'id_regalias';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'codigo_semana',
        'valor',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }
}