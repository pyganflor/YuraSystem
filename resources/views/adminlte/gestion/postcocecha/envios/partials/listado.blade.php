<div id="table_envios" style="margin-top: 20px">
    @if(sizeof($envios)>0)
        <form id="form_envios">
            {{--<table width="100%" class="table table-responsive table-bordered"
                   style="font-size: 0.8em; border-color: #9d9d9d"
                   id="table_content_envios">
                <thead>
                <tr style="background-color: #dd4b39; color: white">
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        ENVÍO N#
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        CLIENTE
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        FECHA DE ENVíO
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        CANTIDAD x ESPECIFICIACIONES
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        AGENCIA DE TRANSPORTE
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        TIPO AGENCIA
                    </th>
                    @if(!empty(yura\Modelos\Usuario::where('id_usuario',session::get('id_usuario'))->first()->punto_acceso))
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d">
                            DESCUENTO $
                        </th>

                    @endif
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        OPCIONES
                    </th>
                </tr>
                </thead>
                @php $x =1; @endphp
                @foreach($listado as $key => $item)
                    <tr onmouseover="$(this).css('background-color','#add8e6')"
                        onmouseleave="$(this).css('background-color','')" class="" id="row_pedidos_">
                        <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center">
                            ENV{{str_pad($item[0]->id_envio,9,"0",STR_PAD_LEFT)}}
                        </td>
                        <td style="border-color: #9d9d9d;vertical-align: middle;" class="text-center">
                            {{$item[0]->c_nombre}}
                        </td>
                        <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand" id="popover_pedidos">
                            {{\Carbon\Carbon::parse($item[0]->fecha_envio)->format('Y-m-d')}}
                        </td>
                        <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand">
                            <ul style="padding: 0;">
                                @foreach($item as $especificacion)
                                    <li style="list-style: none">
                                        {{$especificacion->cantidad}} x
                                        . {{$especificacion->nombre}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand" id="popover_pedidos">
                            {{$item[0]->at_nombre}}
                        </td>
                        <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand" id="popover_pedidos">
                            @if($item[0]->tipo_agencia == 'A')
                                AÉREA
                            @elseif($item[0]->tipo_agencia == 'T')
                                TERRESTRE
                            @elseif($item[0]->tipo_agencia == 'M')
                                MARíTIMA
                            @endif
                        </td>
                        @if(!empty(yura\Modelos\Usuario::where('id_usuario',session::get('id_usuario'))->first()->punto_acceso))
                            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                                <label>Descuento</label><br/>
                                <input type="number" onkeypress="return isNumber(event)" id="descuento_{{$x}}"
                                       {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}} name="descuento_{{$x}}"
                                       ondblclick="activar(this)" value="0.00" readonly><br/>
                                <input type="number" style="margin-top:5px" placeholder="Guía madre"
                                       {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}
                                       id="guia_madre_{{$x}}" name="guia_madre_{{$x}}"><br/>
                                <input type="number" placeholder="Guía hija" id="guia_hija_{{$x}}" name="guia_hija_{{$x}}"
                                        {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}><br/>
                                <select id="codigo_pais_{{$x}}" name="codigo_pais_{{$x}}" style="margin-left: 1px;width: 128px;"
                                        {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}>
                                    @foreach($paises as $pais)
                                        <option {{ ($item[0]->codigo_pais == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                                    @endforeach
                                </select><br/>
                                <input type="text" placeholder="Destino" id="destino_{{$x}}" name="destino_{{$x}}"
                                       value="{{$item[0]->provincia.", ".$item[0]->direccion}}"
                                        {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                                @if($item[0]->empaquetado == 1 )
                                    @if(getFacturado($item[0]->id_envio) == 0 )
                                        <button type="button" class="btn btn-default btn-xs">
                                            <input type="checkbox" id="{{$x}}" name="check_envio" onclick="input_required(this)"
                                                   value="{{$item[0]->id_envio}}"
                                                   style="margin: 0;position: relative;top: 3px;" title="Generar documento electrónico">
                                        </button>
                                    @else

                                        Factura generada
                                    @endif
                                @else
                                    <button class="btn  btn-default btn-xs" type="button" title="Editar envío" id="edit_envio"
                                            onclick="editar_envio('{{$item[0]->id_envio}}','{{$item[0]->id_detalle_envio}}','{{$item[0]->id_pedido}}','{{@csrf_token()}}')">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-default btn-xs"
                                            title="Se debe confirmar el pedido para poder generar el comprobante electrónico de este envío">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </td>
                        @else
                            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                                No tiene permitido la facturación
                            </td>
                        @endif
                        <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                            @if($item[0]->empaquetado == 0)
                                <button class="btn  btn-default btn-xs" type="button" title="Editar envío" id="edit_envio"
                                        onclick="editar_envio('{{$item[0]->id_envio}}','{{$item[0]->id_detalle_envio}}','{{$item[0]->id_pedido}}','{{@csrf_token()}}')">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @php $x++; @endphp
                @endforeach
            </table>--}}
            @foreach($envios as $envio)
                @php $facturado = getFacturado($envio->id_envio); @endphp
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title col-md-4"><b>Envío N#:</b> ENV{{str_pad($envio->id_envio,9,"0",STR_PAD_LEFT)}}</div>
                        <div class="box-title col-md-5"><b>Cliente:</b> {{$envio->nombre}}</div>
                        <div class="box-title col-md-3"><b>Fecha:</b>  {{Carbon\Carbon::parse($envio->fecha_envio)->format('m-d-Y')}} </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white;margin-top:20px"
                               id="table_content_recepciones">
                            <thead>
                            <tr style="background-color: #dd4b39; color: white">
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
                                    style="border-color: #9d9d9d;width:100px">
                                    PRECIO X ESPECIFICACIÓN
                                </th>
                                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                    style="border-color: #9d9d9d">
                                    AGENCIA DE CARGA
                                </th>
                            </tr>
                            </thead>
                            <tbody id="tbody_inputs_pedidos">
                                 @php $anterior = ''; @endphp
                                 @foreach(getPedido($envio->id_pedido)->detalles as $x =>$det_ped)
                                 @php $b=1; @endphp
                                     @foreach(getEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
                                         @foreach($esp_emp->detalles as $z => $det_esp_emp)
                                             <tr style="border-top: {{$det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}" >
                                                 @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                     <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                                                         class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                         <input type="number" min="0" id="cantidad_piezas_{{($x+1)}}" style="border: none" onchange="calcular_precio_pedido(this)"
                                                                name="cantidad_piezas_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" class="text-center form-control cantidad_{{($x+1)}} input_cantidad" value="{{$det_ped->cantidad}}">
                                                         @if($x ==0) <input type="hidden" id="cant_esp" value="">
                                                         <input type="hidden" id="cant_esp_fijas" value="">  @endif
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
                                                     {{$det_esp_emp->cantidad}}
                                                     <input type="hidden" class="td_ramos_x_caja_{{$x+1}} input_ramos_x_caja_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->cantidad}}">
                                                 </td>
                                                 @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                     <td id="td_total_ramos_{{$x+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
                                                         class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
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
                                                     <td id="td_precio_especificacion_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center"
                                                         rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                     </td>
                                                         <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                                             rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                             <select name="id_agencia_carga_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" id="id_agencia_carga_{{$x+1}}"
                                                                     class="text-center form-control" style="border: none; width: 100%">
                                                                 @foreach(getAgenciaCargaCliente($det_ped->cliente_especificacion->id_cliente) as $agencia)
                                                                     <option {!! ($det_ped->id_agencia_carga == $agencia->id_agencia_carga) ? "selected" : ""!!} value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                                                 @endforeach
                                                             </select>
                                                         </td>
                                                 @endif
                                             </tr>
                                             @php
                                                 $anterior = $det_ped->cliente_especificacion->especificacion->id_especificacion;
                                             @endphp
                                             @php $b++ @endphp
                                         @endforeach
                                     @endforeach
                                    @php $anterior = ''; @endphp
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row" style="margin-top:30px">
                            <div class="col-md-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">DATOS DE EXPORTACIÓN</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                        <div class="col-md-4">
                                            <label for="guia_madre">Guía madre</label>
                                            <input type="number" style="margin-top:5px" placeholder="Guía madre" class="form-control"
                                                   {{$facturado != 0 ? "disabled='disabled'" : ""}}
                                                   id="guia_madre_" name="guia_madre_">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="guia_hija">Guía hija</label>
                                            <input type="number" style="margin-top:5px" placeholder="Guía hija" class="form-control"
                                                   {{$facturado != 0 ? "disabled='disabled'" : ""}}
                                                   id="guia_hija_" name="guia_hija_">
                                        </div>
                                        <div class="col-md-4">
                                            <select id="codigo_pais_" name="codigo_pais_" style="margin-left: 1px;width: 128px;"
                                                {{$facturado != 0 ? "disabled='disabled'" : ""}}>
                                                @foreach($paises as $pais)
                                                    <option {{ ($item[0]->codigo_pais == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty(yura\Modelos\Usuario::where('id_usuario',session::get('id_usuario'))->first()->punto_acceso) && $envio->confirmado)
                            <div class="text-center" style="margin-top: 30px">
                                <button type="button" class="btn btn-success" onclick="genera_comprobante_cliente()">
                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                    Generar factura
                                </button>
                            </div>
                        @endif
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: #d2d6de;">
                        <div class="box-title col-md-3"><b>Totales</b></div>
                        <div class="box-title col-md-3">Piezas:<span id="total_piezas"></span></div>
                        <div class="box-title col-md-3">Ramos:<span id="total_ramos"></span></div>
                        <div class="box-title col-md-3">Monto: $<span id="total_monto"></span> </div>
                    </div>
                    <!-- box-footer -->
                </div>
                <!-- /.box -->
                <hr />
            @endforeach

        </form>
        <div id="pagination_listado_envios">
            {!! str_replace('/?','?',$envios->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado conincidencias</div>
    @endif
</div>

<script>
    $(function () {
        $('[data-toggle="popover"]').popover()
    });
</script>
