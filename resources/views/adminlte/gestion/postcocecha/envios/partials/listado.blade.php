<div id="table_envios" style="margin-top: 20px">
    @if(sizeof($envios)>0)
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
            @foreach($envios as $i => $envio)
            <form id="form_envios_{{$i+1}}">
                @php $facturado = getFacturado($envio->id_envio); @endphp
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title col-md-4" style="margin-top: 5px;"><b>Envío N#:</b> ENV{{str_pad($envio->id_envio,9,"0",STR_PAD_LEFT)}}</div>
                        <div class="box-title col-md-4" style="margin-top: 5px;"><b>Cliente:</b>
                            {{$envio->nombre}}
                        </div>
                        <div class="box-title col-md-4"><b>Fecha envío:</b>
                            <input type="date"  id="fecha_envio" name="fecha_envio" style="color:black" value="{{Carbon\Carbon::parse($envio->fecha_envio)->format('Y-m-d')}}">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white;margin-top:20px"
                               id="table_content_recepciones">
                            <thead>
                            <tr style="background-color: #dd4b39; color: white">
                                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d; color: white">
                                    MARACACIONES
                                </th>
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
                                 @php $anterior = '';  @endphp
                                 @foreach(getPedido($envio->id_pedido)->detalles as $x =>$det_ped)
                                 @php $b=1; $precio_x_especificacion = 0.00; @endphp
                                     @foreach(getEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
                                         @foreach($esp_emp->detalles as $z => $det_esp_emp)
                                             <tr style="border-top: {{$det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}" >
                                                 @if($z == 0 && $y == 0)
                                                     <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle" id="td_datos_exportacion_"
                                                         rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)}}">
                                                         @if(count(getDatosExportacionCliente($det_ped->id_detalle_pedido))>0)
                                                             <ul style="padding: 0;margin-bottom: 0">
                                                                 @foreach(getDatosExportacionCliente($det_ped->id_detalle_pedido) as $de)
                                                                     <li style="list-style: none"><b>{{strtoupper($de->nombre)}}:</b> {{$de->valor}} </li>
                                                                 @endforeach
                                                             </ul>
                                                         @endif
                                                     </td>
                                                 @endif
                                                 @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                     <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                                                         class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                         <input type="number" min="0" id="cantidad_piezas_{{$i+1}}_{{($x+1)}}" style="border: none" onchange="calcular_precio_envio()"
                                                                name="cantidad_piezas_{{$i+1}}_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" class="text-center form-control cantidad_{{$i+1}}_{{($x+1)}} input_cantidad_{{$i+1}}" value="{{$det_ped->cantidad}}">
                                                         @if($x ==0) <input type="hidden" id="cant_esp" value="">
                                                         <input type="hidden" id="cant_esp_fijas" value="">  @endif
                                                         <input type="hidden" id="id_cliente_pedido_especificacion_{{$i+1}}_{{($x+1)}}" value="{{$det_ped->cliente_especificacion->id_cliente_pedido_especificacion}}">
                                                     </td>
                                                 @endif

                                                 <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                                                     {{$det_esp_emp->variedad->siglas}}
                                                     <input type="hidden" class="input_variedad_{{$i+1}}_{{$x+1}}" id="id_variedad_{{$i+1}}_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                                                 </td>
                                                 <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 70px;" class="text-center">
                                                     {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                                     <input type="hidden" id="id_detalle_especificacion_empaque_{{$i+1}}_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
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
                                                     <input type="hidden" class="td_ramos_x_caja_{{$i+1}}_{{$x+1}} input_ramos_x_caja_{{$i+1}}_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->cantidad}}">
                                                 </td>
                                                 @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                     <td id="td_total_ramos_{{$i+1}}_{{$x+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
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
                                                 <td id="td_precio_variedad_{{$i+1}}_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" >
                                                     <input type="number" name="precio_{{$i+1}}_{{($x+1)}}" id="precio_{{$i+1}}_{{($x+1)}}_{{$b}}" class="form-control text-center precio_{{$i+1}}_{{($x+1)}} form-control"
                                                            style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_envio()" value="{{explode(";",explode('|',$det_ped->precio)[$b-1])[0]}}"  required>
                                                     <input type="hidden" class="id_detalle_esp_emp_{{$i+1}}_{{($x+1)}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                                 </td>
                                                 @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                     <td id="td_precio_especificacion_{{$i+1}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center"
                                                         rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                     </td>
                                                         <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                                             rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                             <select name="id_agencia_carga_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" id="id_agencia_carga_{{$i+1}}_{{$x+1}}"
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
                                            <div class="col-md-3">
                                                <label for="dae">DAE</label>
                                                @php
                                                    if(isset($envio->dae)){
                                                      $dae = $envio->dae;
                                                    }else{
                                                      $dae = isset(getCodigoDae(strtoupper($envio->codigo_pais),Carbon\Carbon::parse($envio->fecha_envio)->format('m'),Carbon\Carbon::parse($envio->fecha_envio)->format('Y'))->codigo_dae) ? getCodigoDae(strtoupper($envio->codigo_pais),Carbon\Carbon::parse($envio->fecha_envio)->format('m'),Carbon\Carbon::parse($envio->fecha_envio)->format('Y'))->codigo_dae : "";
                                                    }
                                                 @endphp
                                                <input type="text" placeholder="DAE" class="form-control"
                                                       {{$dae != "" ? "disabled='disabled'" : ""}}
                                                       id="dae" name="dae" value="{{$dae}}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="guia_madre">Guía madre</label>
                                                <input type="text" placeholder="Guía madre" class="form-control"
                                                       {{$facturado != 0 ? "disabled='disabled'" : ""}}
                                                       id="guia_madre" name="guia_madre" required value="{{$envio->guia_madre}}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="guia_hija">Guía hija</label>
                                                <input type="text" placeholder="Guía hija" class="form-control"
                                                       {{$facturado != 0 ? "disabled='disabled'" : ""}}
                                                       id="guia_hija" name="guia_hija" value="{{$envio->guia_hija}}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="pais">País</label>
                                                <select id="codigo_pais" name="codigo_pais" class="form-control" onchange="buscar_codigo_dae(this,'form_envios_{{$i+1}}')"
                                                    {{$facturado != 0 ? "disabled='disabled'" : ""}} required>
                                                    @foreach($paises as $pais)
                                                        <option {{ ($envio->codigo_pais == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row" >
                                            <div class="col-md-3">
                                                <label for="Email">Email</label>
                                                <input type="email" placeholder="Email" class="form-control"
                                                       {{$facturado != 0 ? "disabled='disabled'" : ""}}
                                                       id="email" name="email" value="{{$envio->email}}" required >
                                            </div>
                                            <div class="col-md-3">
                                                <label for="telefono">Teléfono</label>
                                                <input type="text" placeholder="Teléfono" class="form-control"
                                                       {{$facturado != 0 ? "disabled='disabled'" : ""}}
                                                       id="telefono" name="telefono_{{$i+1}}" value="{{$envio->telefono}}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="direccion">Dirección</label>
                                                <input type="text" placeholder="Dirección" class="form-control"
                                                       {{$facturado != 0 ? "disabled='disabled'" : ""}}
                                                       id="direccion" name="direccion" value="{{$envio->provincia.", ".$envio->direccion}}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: #d2d6de;">
                        <div class="box-title col-md-3" style="margin-top: 8px"><b>Totales</b></div>
                        <div class="box-title col-md-2" style="margin-top: 8px">Piezas: <span id="total_piezas_{{$i+1}}"></span></div>
                        <div class="box-title col-md-2" style="margin-top: 8px">Ramos: <span id="total_ramos_{{$i+1}}"></span></div>
                        <div class="box-title col-md-2" style="margin-top: 8px">Monto: $<span id="total_monto_{{$i+1}}"></span> </div>
                        <div class="box-title col-md-3 text-right">
                            <button type="button" class="btn btn-success" onclick="actualizar_envio('{{$envio->id_envio}}','form_envios_{{$i+1}}')">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>  Actualizar
                            </button>
                       {{-- @if(!empty(yura\Modelos\Usuario::where('id_usuario',session::get('id_usuario'))->first()->punto_acceso) && $envio->confirmado)
                            <button type="button" class="btn btn-success" onclick="genera_comprobante_cliente()">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                Generar factura
                            </button>
                        @endif--}}
                        </div>
                    </div>
                    <!-- box-footer -->
                </div>
                <!-- /.box -->
                <hr />
        </form>
            @endforeach

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
