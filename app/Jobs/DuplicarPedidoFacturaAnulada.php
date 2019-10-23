<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\Coloracion;
use yura\Modelos\DetalleEnvio;
use yura\Modelos\DetallePedido;
use yura\Modelos\Distribucion;
use yura\Modelos\DistribucionColoracion;
use yura\Modelos\Envio;
use yura\Modelos\Marcacion;
use yura\Modelos\DataTallos;
use yura\Modelos\MarcacionColoracion;
use yura\Modelos\Pedido;

class DuplicarPedidoFacturaAnulada implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $idPedido;
    public function __construct($idPedido)
    {
        $this->idPedido = $idPedido;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Info("Empezo a duplicar un pedido");
        $arrPedido = [];

        $dataPedido = Pedido::where('id_pedido', $this->idPedido)->first();
        //dd($dataPedido->id_pedido);

            $objPedido = new Pedido;
            $objPedido->id_cliente = $dataPedido->id_cliente;
            $objPedido->fecha_pedido = $dataPedido->fecha_pedido;
            $objPedido->empaquetado = $dataPedido->empaquetado;
            $objPedido->variedad = $dataPedido->variedad;
            $objPedido->tipo_especificacion = $dataPedido->tipo_especificacion;
            $objPedido->id_configuracion_empresa = $dataPedido->id_configuracion_empresa;
            if ($objPedido->save()) {
                $modelPedido = Pedido::all()->last();
                //$dataDetallePedido = DetallePedido::where('id_pedido', $this->idPedido)->get();

                bitacora('pedido', $modelPedido->id_pedido, 'I', 'Inserción satisfactoria de un duplicado de pedido');

                $objEnvio = new Envio;
           

                $objEnvio->fecha_envio = $dataPedido->fecha_pedido;
                $objEnvio->id_pedido = $modelPedido->id_pedido;
                $objEnvio->guia_hija = $dataPedido->envios[0]->guia_hija;
                $objEnvio->guia_madre = $dataPedido->envios[0]->guia_madre;
                $objEnvio->dae = $dataPedido->envios[0]->dae;
                $objEnvio->email = $dataPedido->envios[0]->email;
                $objEnvio->telefono = $dataPedido->envios[0]->telefono;
                $objEnvio->direccion = $dataPedido->envios[0]->direccion;
                $objEnvio->codigo_pais = $dataPedido->envios[0]->codigo_pais;
                $objEnvio->almacen = $dataPedido->envios[0]->almacen;
                $objEnvio->codigo_dae = $dataPedido->envios[0]->codigo_dae;
                $objEnvio->id_consignatario = $dataPedido->envios[0]->id_consignatario;
                if($objEnvio->save()) $modelEnvio = Envio::all()->last();

                foreach ($dataPedido->detalles as $detallePedido) {
                    $objDetallePedido = new DetallePedido;
                    $objDetallePedido->id_cliente_especificacion = $detallePedido->id_cliente_especificacion;
                    $objDetallePedido->id_pedido = $modelPedido->id_pedido;
                    $objDetallePedido->id_agencia_carga = $detallePedido->id_agencia_carga;
                    $objDetallePedido->cantidad = $detallePedido->cantidad;
                    $objDetallePedido->precio = $detallePedido->precio;

                    $detalleEnvio = new DetalleEnvio;
                    $detalleEnvio->id_envio = $modelEnvio->id_envio;
                    $objClienteEspecificacion = ClientePedidoEspecificacion::find($detallePedido->id_cliente_especificacion);
                    $detalleEnvio->id_especificacion = $objClienteEspecificacion->id_especificacion;
                    $detalleEnvio->cantidad = $detallePedido->cantidad;
                    $detalleEnvio->save();
                    
                    
                    if ($objDetallePedido->save()) {

                        $model_detalle_pedido = DetallePedido::all()->last();
                        
                        if(isset($detallePedido->data_tallos)){
                    
                          $objDataTallos = new DataTallos;
                          $objDataTallos->id_detalle_pedido = $model_detalle_pedido->id_detalle_pedido;
                          $objDataTallos->mallas = $detallePedido->data_tallos->mallas;
                          $objDataTallos->ramos_x_caja =$detallePedido->data_tallos->ramos_x_caja;
                          $objDataTallos->tallos_x_caja =$detallePedido->data_tallos->tallos_x_caja;
                          $objDataTallos->tallos_x_malla =$detallePedido->data_tallos->tallos_x_malla;
                          $objDataTallos->tallos_x_ramo =$detallePedido->data_tallos->tallos_x_ramo;
                          $objDataTallos->save();
                    
                      }

                        
                        bitacora('detalle_pedido', $model_detalle_pedido->id_detalle_pedido, 'I', 'Inserción satisfactoria del duplicado de un detalle pedio');
                        if($modelPedido->tipo_especificacion === "T") {
                            foreach ($detallePedido->cliente_especificacion->especificacion->especificacionesEmpaque as $z => $esp_emp){
                                $dataColoraciones = Coloracion::where([
                                    ['id_detalle_pedido', $detallePedido->id_detalle_pedido],
                                    ['id_especificacion_empaque',$esp_emp->id_especificacion_empaque]
                                ])->get();
                                $dataMarcaciones = Marcacion::where([
                                    ['id_detalle_pedido', $detallePedido->id_detalle_pedido],
                                    ['id_especificacion_empaque',$esp_emp->id_especificacion_empaque]
                                ])->get();

                                $arr_colores = [];

                                foreach ($dataColoraciones as $dC) {
                                    $objColoracion = new Coloracion;
                                    $objColoracion->id_color = $dC->id_color;
                                    $objColoracion->id_especificacion_empaque = $dC->id_especificacion_empaque;
                                    $objColoracion->id_detalle_pedido = $model_detalle_pedido->id_detalle_pedido;
                                    $objColoracion->precio = $dC->precio;
                                    if ($objColoracion->save()) {
                                        $model_coloraciones = Coloracion::all()->last();
                                        $arr_colores[] = $model_coloraciones->id_coloracion;
                                    }
                                }

                                foreach ($dataMarcaciones as $x => $dM) {
                                    $arr_marcacion_coloracion = [];
                                    $dataMarcacionColoracion = [];
                                    $objMarcacion = new Marcacion;
                                    $objMarcacion->nombre = $dM->nombre;
                                    $objMarcacion->ramos = $dM->ramos;
                                    $objMarcacion->id_detalle_pedido = $model_detalle_pedido->id_detalle_pedido;
                                    $objMarcacion->id_especificacion_empaque = $dM->id_especificacion_empaque;
                                    $objMarcacion->piezas = $dM->piezas;
                                    if ($objMarcacion->save()) {
                                        $model_marcacion = Marcacion::all()->last();
                                        $dataMarcacionColoracion[] = MarcacionColoracion::where('id_marcacion', $dM->id_marcacion)->get();
                                        foreach ($dataMarcacionColoracion as $dMc) {
                                            foreach ($dMc as $y => $mc) {
                                                $objMarcacionColoracion = new MarcacionColoracion;
                                                $objMarcacionColoracion->id_marcacion = $model_marcacion->id_marcacion;
                                                $objMarcacionColoracion->id_coloracion = $arr_colores[$y];
                                                $objMarcacionColoracion->id_detalle_especificacionempaque = $mc->id_detalle_especificacionempaque;
                                                $objMarcacionColoracion->cantidad = $mc->cantidad;
                                                if($objMarcacionColoracion->save())
                                                    $arr_marcacion_coloracion[] = MarcacionColoracion::all()->last();
                                            }
                                        }

                                        $dataDistribucion = Distribucion::where('id_marcacion',$dM->id_marcacion)->get();

                                        //if(isset($dataDistribucion->id_distribucion)){

                                        if($dataDistribucion->count() > 0){
                                            foreach ($dataDistribucion as $d){
                                                $objDistribucion = new Distribucion;
                                                $objDistribucion->id_marcacion = $model_marcacion->id_marcacion;
                                                $objDistribucion->ramos = $d->ramos;
                                                $objDistribucion->piezas = $d->piezas;
                                                $objDistribucion->pos_pieza = $d->pos_pieza;
                                                if($objDistribucion->save()){
                                                    $dataDistribucionColoracion  = DistribucionColoracion::where('id_distribucion',$d->id_distribucion)->get();
                                                    $model_distribucion = Distribucion::All()->last();
                                                    foreach ($dataDistribucionColoracion as $z => $dC) {
                                                        $objDistribucionColoracion = new DistribucionColoracion;
                                                        $objDistribucionColoracion->id_distribucion = $model_distribucion->id_distribucion;
                                                        $objDistribucionColoracion->id_marcacion_coloracion = $arr_marcacion_coloracion[$z]->id_marcacion_coloracion;
                                                        $objDistribucionColoracion->cantidad = $dC->cantidad;
                                                        $objDistribucionColoracion->save();
                                                    }
                                                }
                                            }
                                        }
                                        // }
                                    }
                                }
                            }
                        }
                    }
                }
            }


        /*$arrPedido['pedido']= [
            'id_cliente'=> $pedido->id_cliente,
            'fecha_pedido' => $pedido->fecha_pedido,
            'empaquetado' => $pedido->empaquetado,
            'variedad' => $pedido->variedad,
            'tipo_especificacion' => $pedido->tipo_especificacion,
            'confirmado' => $pedido->confirmado,
            'historico' => $pedido->historico,
            'id_configuracion_empresa' => $pedido->id_configuracion_empresa,
        ];

        foreach($pedido->detalles as $det_ped){
            $arrPedido['detalles'][]=[
                'id_detalle_pedido' => $det_ped->id_detalle_pedido,
                'id_cliente_especificacion' => $det_ped->id_cliente_especificacion,
                'id_agencia_carga' => $det_ped->id_agencia_carga,
                'cantidad' => $det_ped->cantidad,
                'precio' => $det_ped->precio,
                'orden' => $det_ped->orden,
            ];

            if($pedido->tipo_especificacion === "T"){

                foreach($det_ped->coloraciones as $coloracion) {
                    $arrPedido['coloraciones'][$det_ped->id_detalle_pedido][] = [
                        'id_color' => $coloracion->id_color,
                        'id_especificacion_empaque' => $coloracion->id_especificacion_empaque,
                        'precio' => $coloracion->precio,
                    ];
                }

                foreach($det_ped->marcaciones as $marcacion){

                    $arrPedido['marcaciones'][$marcacion->id_detalle_pedido][]=[
                        'id_marcacion'=> $marcacion->id_marcacion,
                        'nombre'=> $marcacion->nombre,
                        'ramos'=> $marcacion->ramos,
                        'id_especificacion_empaque'=> $marcacion->id_especificacion_empaque,
                        'piezas'=> $marcacion->piezas,
                    ];

                    foreach($marcacion->marcaciones_coloraciones as $m_c){
                        $arrPedido['marcaciones_coloraciones'][$det_ped->id_detalle_pedido][$m_c->id_marcacion][]=[
                            //'id_marcacion_coloracion'=> $m_c->id_marcacion_coloracion,
                            'id_detalle_especificacionempaque'=> $m_c->id_detalle_especificacionempaque,
                            'cantidad'=> $m_c->cantidad,
                        ];

                        foreach ($m_c->distribuciones_coloraciones as $dist_col) {
                            $arrPedido['distribucion_coloracion'][$m_c->id_marcacion_coloracion][]=[
                                'id_distribucion'=>  $dist_col->id_distribucion,
                                'id_marcacion_coloracion'=>  $dist_col->id_marcacion_coloracion,
                                'cantidad'=>  $dist_col->cantidad
                            ];
                        }
                    }

                    foreach($marcacion->distribuciones as $distribucion){
                        $arrPedido['distribucion'][$det_ped->id_detalle_pedido][$distribucion->id_marcacion][]=[
                            'ramos'=> $distribucion->ramos,
                            'piezas'=> $distribucion->piezas,
                            'pos_pieza'=> $distribucion->pos_pieza,
                        ];

                        foreach ($distribucion->distribuciones_coloraciones as $dist_col) {
                            $arrPedido['distribucion_coloracion'][$distribucion->id_distribucion][]=[
                                'id_distribucion'=>  $dist_col->id_distribucion,
                                'id_marcacion_coloracion'=>  $dist_col->id_marcacion_coloracion,
                                'cantidad'=>  $dist_col->cantidad
                            ];
                        }
                    }

                }

            }
        }

        //dd($arrPedido);
        $objPedido = new Pedido;
        $objPedido->id_cliente = $arrPedido['pedido']['id_cliente'];
        $objPedido->fecha_pedido = $arrPedido['pedido']['fecha_pedido'];
        $objPedido->empaquetado = $arrPedido['pedido']['empaquetado'];
        $objPedido->variedad = $arrPedido['pedido']['variedad'];
        $objPedido->tipo_especificacion = $arrPedido['pedido']['tipo_especificacion'];
        $objPedido->confirmado = $arrPedido['pedido']['confirmado'];
        $objPedido->historico = $arrPedido['pedido']['historico'];
        $objPedido->id_configuracion_empresa = $arrPedido['pedido']['id_configuracion_empresa'];
        if($objPedido->save()){
            $modelPedido = Pedido::all()->last();
            $idsMarcacion=[];
            $idsColoracion=[];
            foreach($arrPedido['detalles'] as $det_ped){
                $objDetallePedido = new DetallePedido;
                $objDetallePedido->id_pedido = $modelPedido->id_pedido;
                $objDetallePedido->id_cliente_especificacion = $det_ped['id_cliente_especificacion'];
                $objDetallePedido->id_agencia_carga = $det_ped['id_agencia_carga'];
                $objDetallePedido->cantidad = $det_ped['cantidad'];
                $objDetallePedido->precio = $det_ped['precio'];
                $objDetallePedido->orden = $det_ped['orden'];
                if($objDetallePedido->save()){
                    if($arrPedido['pedido']['tipo_especificacion']==="T"){
                        $modelDetallePedido = DetallePedido::all()->last();

                        foreach ($arrPedido['marcaciones'][$det_ped['id_detalle_pedido']] as $marcacion){
                            $objMarcacion = new Marcacion;
                            $objMarcacion->id_detalle_pedido = $modelDetallePedido->id_detalle_pedido;
                            $objMarcacion->nombre = $marcacion['nombre'];
                            $objMarcacion->ramos = $marcacion['ramos'];
                            $objMarcacion->id_especificacion_empaque = $marcacion['id_especificacion_empaque'];
                            $objMarcacion->piezas = $marcacion['piezas'];
                            if($objMarcacion->save()){
                                $modelMarcacion = Marcacion::all()->last();
                                $idsMarcacion[$det_ped['id_detalle_pedido']][$marcacion['id_especificacion_empaque']][]=[
                                    'id_marcacion'=> $modelMarcacion->id_marcacion
                                ];
                            }
                        }

                        foreach ($arrPedido['coloraciones'][$det_ped['id_detalle_pedido']] as $coloracion) {
                            $objColoracion = new Coloracion;
                            $objColoracion->id_detalle_pedido = $modelDetallePedido->id_detalle_pedido;
                            $objColoracion->id_color = $coloracion['id_color'];
                            $objColoracion->id_especificacion_empaque = $coloracion['id_especificacion_empaque'];
                            $objColoracion->precio = $coloracion['precio'];
                            if($objColoracion->save()){
                                $modelColoracion = Coloracion::all()->last();
                                $idsColoracion[$det_ped['id_detalle_pedido']][$coloracion['id_especificacion_empaque']][]=[
                                    'id_coloracion'=> $modelColoracion->id_coloracion,
                                ];
                            }
                        }
                        $dataMarcacionColoracion =[];
                        foreach ($idsMarcacion[$det_ped['id_detalle_pedido']] as $idEspEmpMar => $maracion) {
                            foreach($maracion as  $m){
                                foreach ($idsColoracion[$det_ped['id_detalle_pedido']] as $idEspEmpCol => $coloracion) {
                                    foreach ($coloracion as  $c) {
                                        if($idEspEmpMar == $idEspEmpCol){
                                            $dataMarcacionColoracion[$det_ped['id_detalle_pedido']][]=[
                                                'id_marcacion'=> $m['id_marcacion'],
                                                'id_coloracion' => $c['id_coloracion']
                                            ];
                                        }
                                    }
                                }
                            }
                        }


                    }
                }else{

                }
            }
        }else{

        }
        //Info($idsMarcacion);
        //Info($idsColoracion);
        //Info($arrPedido['marcaciones_coloraciones']);
        //Info($dataMarcacionColoracion);
        //Info($marcacionColoracion);*/

    }
}
