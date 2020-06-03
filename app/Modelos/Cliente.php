<?php

namespace yura\Modelos;

use DB;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'id_cliente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'factor',
        'fc',
        'csv',
        'le',
        'dc',
        'fc_sri'
    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetalleCliente', 'id_cliente');
    }

    public function detalle()
    {
        return $this->hasMany('\yura\Modelos\DetalleCliente', 'id_cliente')->where('estado', '=', 1)->first();
    }

    public function cliente_agencia_carga()
    {
        return $this->hasMany('\yura\Modelos\ClienteAgenciaCarga', 'id_cliente');
    }

    public function cliente_pedido_especificaciones()
    {
        return $this->hasMany('\yura\Modelos\ClientePedidoEspecificacion', 'id_cliente');
    }

    public function cliente_datoexportacion()
    {
        return $this->hasMany('\yura\Modelos\ClienteDatoExportacion', 'id_cliente');
    }

    public function contacto_principal(){
        return DB::table('detalle_cliente as dc')
            ->where('id_cliente',$this->id_cliente)
            ->join('detalle_cliente_contacto as dcc','dc.id_detalle_cliente','=','dcc.id_detalle_cliente')
            ->join('contacto as c','dcc.id_contacto','=','c.id_contacto')
            ->first();
    }

    public function precio_promedio($idVariedad=null){
        if(isset($idVariedad)){
            $precio = $this->hasOne('yura\Modelos\PrecioVariedadCliente','id_cliente')->where('id_variedad',$idVariedad)->first();
        }else{
            $precio = $this->hasMany('yura\Modelos\PrecioVariedadCliente','id_cliente');
        }
        return $precio;
    }

}
