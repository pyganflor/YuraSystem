<div class="box box-solid box-primary">
    <div class="box-header with-border">
        <div class="box-title col-md-12 text-center" >
            <b>DESPACHO DE FINCA</b>
        </div>

    </div>
    <!-- /.box-header -->
    <div class="box-body" style="width: 100%;overflow-x: auto">
        <div class="col md-12 text-center" style="font-weight: bold;margin-top: 20px">
            DESPACHO COMPLETO
        </div>
        <table width="100%" class="table-responsive table-bordered" style=" border-color: white">
            <thead>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Pedido</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Cliente</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Agencia</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Cajas Full</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Piezas</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Full Boxes </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Half Boxes</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">1/4 Boxes</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">1/6 Boxes </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">1/8 Boxes</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Guía</th>
            </thead>
            <tbody>
                @php
                    $total_caja_full = 0;
                    $piezas_totales = 0;
                    $full = 0;
                    $half = 0;
                    $cuarto = 0;
                    $sexto = 0;
                    $octavo = 0;
                @endphp
                @foreach($pedidos as $x => $pedido)
                    @php
                        $full_det_esp_emp = 0;
                        $half_det_esp_emp = 0;
                        $cuarto_det_esp_emp = 0;
                        $sexto_det_esp_emp = 0;
                        $octavo_det_esp_emp = 0;
                        foreach($pedido->detalles as $det_ped){
                            foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                                //foreach ($esp_emp->detalles as $n => $det_esp_emp){
                                    switch (explode("|",$esp_emp->empaque->nombre)[1]) {
                                        case '1':
                                            $full += $det_ped->cantidad;
                                            $full_det_esp_emp += $det_ped->cantidad;
                                            break;
                                        case '0.5':
                                            $half += $det_ped->cantidad;
                                            $half_det_esp_emp += $det_ped->cantidad;
                                            break;
                                        case '0.25':
                                            $cuarto +=$det_ped->cantidad;
                                            $cuarto_det_esp_emp +=$det_ped->cantidad;
                                            break;
                                        case '0.17':
                                            $sexto +=$det_ped->cantidad;
                                            $sexto_det_esp_emp+=$det_ped->cantidad;
                                            break;
                                        case '0.125':
                                            $octavo +=$det_ped->cantidad;
                                            $octavo_det_esp_emp+=$det_ped->cantidad;
                                            break;
                                    }
                                //}
                            }
                        }
                    @endphp
                    <tr id="tr_despachos">
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                           #{{$x+1}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                            @foreach(getPedido($pedido->id_pedido)->cliente->detalles as $det_cliente)
                                {{$det_cliente->estado == 1 ? $det_cliente->nombre : "" }}
                            @endforeach
                            <input type="hidden" class="id_pedido id_pedido_{{$x+1}}" name="id_pedido" value="{{$pedido->id_pedido}}">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                            {{getPedido($pedido->id_pedido)->detalles[0]->agencia_carga->nombre}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                            @php $caja_full = 0; @endphp
                            @foreach(getPedido($pedido->id_pedido)->detalles as $det_ped)
                                @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                                    @php $caja_full += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1] @endphp
                                @endforeach
                            @endforeach
                            @php $total_caja_full +=  $caja_full@endphp
                            {{$caja_full}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                            @php $piezas = 0; @endphp
                            @foreach(getPedido($pedido->id_pedido)->detalles as $det_ped)
                                @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                                    {{--@foreach($esp_emp->detalles as $det_esp)--}}
                                        @php  $piezas += ($esp_emp->cantidad * $det_ped->cantidad); @endphp
                                {{--@endforeach--}}
                            @endforeach
                        @endforeach
                        {{$piezas}}
                        @php $piezas_totales += $piezas; @endphp
                    </td>
                    <td class="text-center full full_{{$x+1}}"  style="border-color: #9d9d9d;vertical-align: middle">
                        {{$full_det_esp_emp}}
                        <input type="hidden" class="full " value="{{$full_det_esp_emp}}">
                    </td>
                    <td class="text-center half_{{$x+1}}"  style="border-color: #9d9d9d;vertical-align: middle">
                        {{$half_det_esp_emp}}
                        <input type="hidden" class="half" value="{{$half_det_esp_emp}}">
                    </td>
                    <td class="text-center cuarto cuarto_{{$x+1}}"  style="border-color: #9d9d9d;vertical-align: middle">
                        {{$cuarto_det_esp_emp}}
                        <input type="hidden" class="cuarto" value="{{$cuarto_det_esp_emp}}">
                    </td>
                    <td class="text-center sexto sexto_{{$x+1}}"  style="border-color: #9d9d9d;vertical-align: middle">
                        {{$sexto_det_esp_emp}}
                        <input type="hidden" class="sexto" value="{{$sexto_det_esp_emp}}">
                    </td>
                    <td class="text-center octavo octavo_{{$x+1}}"  style="border-color: #9d9d9d;vertical-align: middle">
                        {{$octavo_det_esp_emp}}
                        <input type="hidden" class="octavo" value="{{$octavo_det_esp_emp}}">
                    </td>
                    <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                        @if(getPedido($pedido->id_pedido)->envios->count() >0)
                            {!! getPedido($pedido->id_pedido)->envios[0]->detalles[0]->id_aerolinea ==  ""
                                 ? "<span style='color:red'>No se ha asignado aerolínea</span>"
                                 : getPedido($pedido->id_pedido)->envios[0]->detalles[0]->aerolinea->codigo !!}
                        @else
                            <span style='color:red'>{{"No se ha configurado el envío del pedido"}}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        <tr>
            <td></td>
            <td></td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">Total:</td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"> {{$total_caja_full}}</td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"> <span id="piezas_totales">{{$piezas_totales}}</span></td>
            <td colspan="6" id="msg" style="border: none"></td>
        </tr>
        <tr>
        </tr>
        </tbody>
    </table>
    <div class="col md-12 text-center" style="font-weight: bold;margin-top:20px">
        DATOS GENERALES
    </div>
    <table width="100%" class="table-responsive table-bordered" style=" border-color: white;">
        <tr>
            <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle;" >
                <b> Cajas Totales</b>
            </td>
            <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                <b>FULL BOXES : [<span id="full_box">{{number_format($full,2,".","")}}</span>]</b>
            </td>
            <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                <b> HALF BOXES : [<span id="half_box">{{number_format($half,2,".","")}}</span>]</b>
            </td>
            <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                <b> 1/4 BOXES : [<span id="cuarto_box">{{number_format($cuarto,2,".","")}}</span>]</b>
            </td>
            <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                <b> 1/6 BOXES : [<span id="sexto_box">{{number_format($sexto,2,".","")}}</span>]</b>
            </td>
            <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                <b> 1/8 BOXES : [<span id="octavo_box">{{number_format($octavo,2,".","")}}</span>]</b>
            </td>
            <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                <button type="button" class="btn btn-primary btn-xs" title="Agregar Camión" onclick="distribucion_despacho()">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger btn-xs" title="Quitar Camión" onclick="delete_distribucion()">
                    <i class="fa fa-minus" aria-hidden="true"></i>
                </button>
            </td>
        </tr>
    </table>
    <div id="despachos"></div>
    <div class="col md-12 text-center" style="font-weight: bold;margin-top: 40px">
        RESPONSABLES
    </div>
    <table width="100%" class="table-responsive table-bordered" style=" border-color: white;">
        <tr>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <label>Oficina de despacho  | Correo</label><br/>
                <input type="text" id="nombre_oficina_despacho" style="text-align: center;width:50%;float: left;" class="form-control input-sm" name="nombre_oficina_despacho"
                       value="{{isset($datos_responsables->resp_ofi_despacho) ? $datos_responsables->resp_ofi_despacho : ""}}" required>
                <input type="text" id="correo_oficina_despacho" style="text-align: center;width:50%" class="form-control input-sm" name="correo_oficina_despacho"
                       value="{{isset($datos_responsables->mail_resp_ofi_despacho) ? $datos_responsables->mail_resp_ofi_despacho : ""}}" required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <label>Aux. Cuarto frio</label>
                <input type="text" id="nombre_cuarto_frio" style="text-align: center" class="form-control input-sm" name="nombre_cuarto_frio"
                       value="{{isset($datos_responsables->aux_cuarto_fri) ? $datos_responsables->aux_cuarto_fri : ""}}" required>
            </td>
            {{--<td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <label>Transportista</label>
                <input type="text" id="nombre_transportista" style="text-align: center" class="form-control input-sm" name="nombre_transportista"
                       value="{{isset($datos_responsables->resp_transporte) ? $datos_responsables->resp_transporte : ""}}" required>
            </td>--}}
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Guardia de turno</label>
                    <input type="text" id="nombre_guardia_turno" style="text-align: center" class="form-control input-sm" name="nombre_guardia_turno"
                           value="{{isset($datos_responsables->guardia_turno) ? $datos_responsables->guardia_turno : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Jefe de ventas</label>
                    <input type="text" id="nombre_asist_comercial" style="text-align: center" class="form-control input-sm" name="nombre_asist_comercial"
                           value="{{isset($datos_responsables->asist_comercial_ext) ? $datos_responsables->asist_comercial_ext : ""}}" required>
                </td>
            </tr>
            <tr>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_oficina_despacho" style="text-align: center" class="form-control input-sm" name="id_oficina_despacho"
                           value="{{isset($datos_responsables->id_resp_ofi_despacho) ? $datos_responsables->id_resp_ofi_despacho : ""}}"  required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_cuarto_frio" style="text-align: center" class="form-control input-sm" name="id_cuarto_frio"
                           value="{{isset($datos_responsables->id_aux_cuarto_fri) ? $datos_responsables->id_aux_cuarto_fri : ""}}" required>
                </td>
                {{--<td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="firma_id_transportista" style="text-align: center" class="form-control input-sm" name="firma_id_transportista"
                           value="{{isset($datos_responsables->id_resp_transporte) ? $datos_responsables->id_resp_transporte : ""}}"  required>
                </td>--}}
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_guardia_turno" style="text-align: center" class="form-control input-sm" name="id_guardia_turno"
                           value="{{isset($datos_responsables->id_guardia_turno) ? $datos_responsables->id_guardia_turno : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_asist_comercial" style="text-align: center" class="form-control input-sm" name="id_asist_comercial"
                           value="{{isset($datos_responsables->id_asist_comrecial_ext) ? $datos_responsables->id_asist_comrecial_ext : ""}}" required>
                </td>
            </tr>
        </table>
    </div>
</div>
<script>
    distribucion_despacho();
    function distribucion_despacho(){
        cant_form = $("div#despachos form").length;

        $.LoadingOverlay('show');
        datos = {
            cant_form : cant_form,
        };
        $.get('{{url('despachos/distribuir_despacho')}}', datos, function (retorno) {
            $("#despachos").append(retorno);
        });
        $.LoadingOverlay('hide');
    }

    function delete_distribucion(){
        cant_form = $("div#despachos form").length;
        if(cant_form > 1) $("div#despachos form#form_despacho_"+cant_form).remove();
        if(cant_form <= 2) $("select.pedido").removeAttr('required');
    }

</script>
