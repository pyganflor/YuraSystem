<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CosechaPlantasMadres extends Model
{
    protected $table = 'cosecha_plantas_madres';
    protected $primaryKey = 'id_cosecha_plantas_madres';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cama',
        'fecha_registro',
        'cantidad',
        'fecha',
        'id_variedad',
    ];

    public function cama()
    {
        return $this->belongsTo('\yura\Modelos\Cama', 'id_cama');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function semana()
    {
        return getSemanaByDate($this->fecha);
    }
}
