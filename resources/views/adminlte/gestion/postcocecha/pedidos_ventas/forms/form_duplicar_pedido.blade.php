<table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white"
       id="table_content_recepciones">
    <thead>
    <tr style="background-color: #dd4b39; color: white">
        {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
            style="border-color: #9d9d9d;width: 80px">
            ORDEN
        </th>--}}
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
            style="border-color: #9d9d9d;width: 80px">
            PIEZAS
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            VARIEDAD
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            PESO
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            CAJA
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            PRESENTACIÓN
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            RAMO X CAJA
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            TOTAL RAMOS
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            TALLOS X RAMO
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            LONGITUD
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
            style="border-color: #9d9d9d;width:100px">
            PRECIO X VARIEDAD
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
            style="border-color: #9d9d9d">
            AGENCIA DE CARGA
        </th>
        @foreach($datos_exportacion as $key => $de)
            <th class="th_datos_exportacion text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                id="th_datos_exportacion_{{$key+1}}" style="border-color: #9d9d9d;width: 80px;">
                {{strtoupper($de->nombre)}}
            </th>
        @endforeach

    </tr>
    </thead>
    <tbody id="tbody_inputs_pedidos">
    @php $anterior = ''; @endphp
    @foreach(getPedido($id_pedido)->detalles as $x =>$det_ped)
        @php $b=1; @endphp
        @foreach(getEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
            @foreach($esp_emp->detalles as $z => $det_esp_emp)
                <tr style="border-top: {{$det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}" >
                    @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                        {{--<td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                            class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                            <input disabled type="number" id="orden_{{($x+1)}}" style="border: none" value="{{$det_ped->orden}}"
                                   name="orden_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" class="text-center form-control orden_{{($x+1)}} input_orden">
                        </td>--}}
                        <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                            class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                            <input disabled type="number" min="0" id="cantidad_piezas_{{($x+1)}}" style="border: none" onchange="calcular_precio_pedido(this)"
                                   name="cantidad_piezas_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" class="text-center form-control cantidad_{{($x+1)}} input_cantidad" value="{{$det_ped->cantidad}}">
                            @if($x ==0)
                                <input type="hidden" id="cant_esp" value="">
                                <input type="hidden" id="cant_esp_fijas" value="">
                            @endif
                            <input type="hidden" id="id_cliente_pedido_especificacion_{{($x+1)}}" value="{{$det_ped->cliente_especificacion->id_cliente_pedido_especificacion}}">
                        </td>
                    @endif
                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                        {{$det_esp_emp->variedad->siglas}}
                        <input type="hidden" class="input_variedad_{{$x+1}}" id="id_variedad_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 70px;" class="text-center">
                        {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                        <input type="hidden" id="id_detalle_especificacion_empaque_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                    </td>
                    @if($z == 0)
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center"
                            rowspan="{{count($esp_emp->detalles)}}">
                            {{explode('|',$esp_emp->empaque->nombre)[0]}}
                        </td>
                    @endif
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        {{$det_esp_emp->empaque_p->nombre}}
                    </td>
                    <td  style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        @php
                            $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                            $ramos_x_caja =isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad;
                        @endphp
                        {{$ramos_x_caja}}
                        <input type="hidden" class="td_ramos_x_caja_{{$x+1}} input_ramos_x_caja_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->cantidad}}">
                    </td>
                    @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                        <td id="td_total_ramos_{{$x+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
                            class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                            {{$ramos_x_caja*$det_ped->cantidad}}
                        </td>
                    @endif
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        {{$det_esp_emp->tallos_x_ramos}}
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                            {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                        @endif
                    </td>
                    <td id="td_precio_variedad_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" >
                        <input type="number" name="precio_{{($x+1)}}" id="precio_{{($x+1)}}_{{$b}}" class="form-control text-center precio_{{($x+1)}} form-control"
                               style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" value="{{explode(";",explode('|',$det_ped->precio)[$b-1])[0]}}"  required>
                    </td>
                    @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                       {{-- <td id="td_precio_especificacion_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                        </td>--}}
                        <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                            rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                            <select name="id_agencia_carga_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" id="id_agencia_carga_{{$x+1}}"
                                    class="text-center form-control" style="border: none; width: 100%" required>
                                @foreach($agenciasCarga as $agencia)
                                    <option {!! ($det_ped->id_agencia_carga == $agencia->id_agencia_carga) ? "selected" : ""!!} value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                @endforeach
                            </select>
                        </td>
                        @foreach($datos_exportacion as $de)
                            <td rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}"
                                style="border-color: #9d9d9d; vertical-align: middle">
                                <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$x+1}}" id="input_{{strtoupper($de->nombre)}}_{{$x+1}}" class="form-control" style="border: none"
                                       value="{{isset(getDatosExportacion($det_ped->id_detalle_pedido,$de->id_dato_exportacion)->valor) ? getDatosExportacion($det_ped->id_detalle_pedido,$de->id_dato_exportacion)->valor : ""}}">
                                <input type="hidden" name="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" id="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" value="{{$de->id_dato_exportacion}}">
                            </td>
                        @endforeach
                    @endif
                </tr>
                @php
                    $anterior = $det_ped->cliente_especificacion->especificacion->id_especificacion;
                    $b++
                @endphp
            @endforeach
        @endforeach
        @php $anterior = ''; @endphp
    @endforeach
    </tbody>
</table>
<hr>
<div class="row" >
    <div class="col-md-12 text-right">
        <button class="btn btn-danger btn-xs" title="Eliminar fecha" onclick="eliminar_fecha()">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </button>
        <button class="btn btn-success btn-xs" title="Agregar fecha" onclick="agregar_fecha()">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
    </div>
</div>
<form id="form_fechas">
    <div class="row" id="fecha_pedido_duplicado"></div>
</form>
<div class="row"  style='margin-top: 30px'>
<div class="col-md-12 text-center" id="btn_store">
    <button type="button" onclick='store_duplicar_pedido()' class='btn btn-primary'>
        <i class='fa fa-floppy-o' aria-hidden='true'></i>
        Guardar
    </button>
</div>
</div>
<script>
    /*function agregar_fecha(){
        $.LoadingOverlay('show');
        datos = {
            id_pedido: id_pedido,
            id_cliente : id_cliente
        };
        $.get('pedidos/form_duplicar_pedido', datos, function (retorno) {
            modal_view('modal_duplicar_pedido',retorno,'<i class="fa fa-files-o" aria-hidden="true"></i> Duplicar pedido', true, false, '70%');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }*/

    function agregar_fecha() {
        cant_inputs = $(".fecha_pedio").length;
        $("#fecha_pedido_duplicado").append(
            "<div class='col-md-4 div_fecha div_fecha_dulplicada_"+(cant_inputs+1)+"' style='margin-top: 20px'>"+
            "<label for='fecha'>Fecha</label> "+
            " <input type='date' id='fecha_pedido_"+(cant_inputs+1)+"' name='fecha_pedido_"+(cant_inputs+1)+"' value='' required class='form-control fecha_pedio'>" +
            "</div>"
        );
    }
    
    function store_duplicar_pedido() {
        $.LoadingOverlay('show');
        if($("#form_fechas").valid()){
            arrFechas = [];
            $.each($(".fecha_pedio"),function (i,j) {
                arrFechas.push({
                    fecha : j.value
                });
            });
            if(arrFechas.length < 1){
                modal_view('modal_duplicar_pedido','<div class="alert alert-danger text-center"><p> Debe seleccionar al menos una fecha para duplicar el pedido </p> </div>','<i class="fa fa-times" aria-hidden="true"></i> Estado pedido', true, false, '40%');
                return false;
            }
            datos= {
                _token : '{{csrf_token()}}',
                arrFechas : arrFechas,
                id_pedido : '{{$id_pedido}}'
            };
            post_jquery('{{url('pedidos/store_duplicar_pedido')}}', datos, function () {
                listar_resumen_pedidos('{{now()->toDateString()}}', true);
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        }
    }
    
    function eliminar_fecha() {
        cant_div = $(".div_fecha").length;
        $(".div_fecha_dulplicada_"+cant_div).remove();
    }
</script>
