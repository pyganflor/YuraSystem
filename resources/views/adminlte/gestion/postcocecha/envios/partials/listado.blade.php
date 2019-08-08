<div id="table_envios" style="margin-top: 20px">
    @if(sizeof($envios)>0)
        @foreach($envios as $i => $envio)
            @php
                $facturaClienTeTercero = getFacturaClienteTercero($envio->id_envio);
                $dataCliente = yura\Modelos\DetalleCliente::where([
                    ['id_cliente' , $envio->pedido->cliente->id_cliente],
                    ['estado', 1]
                ])->first();
                if($facturaClienTeTercero != null ){
                    $codigo_impuesto = $facturaClienTeTercero->codigo_impuesto;
                    $codigo_porcentaje_impuesto =  $facturaClienTeTercero->codigo_impuesto_porcentaje;
                }else{
                    $codigo_impuesto = $dataCliente->codigo_impuesto;
                    $codigo_porcentaje_impuesto = $dataCliente->codigo_porcentaje_impuesto;
                }
                $tipoImpuestoCliente = getTipoImpuesto($codigo_impuesto,$codigo_porcentaje_impuesto);
            @endphp
            <form id="form_envios_{{$i+1}}">
                <input type="hidden" id="porcentaje_impuesto_{{$i+1}}" value="{{$tipoImpuestoCliente->porcentaje}}">
                @php

                    //$firmado = getFacturado($envio->id_envio,1); COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
                       $exist_comprobante=null;
                       if(isset($envio->pedido->id_comprobante_temporal) && $envio->pedido->id_comprobante_temporal != "")
                            $exist_comprobante = getComprobante($envio->pedido->id_comprobante_temporal);

                       if($envio->comprobante != null)
                           $exist_comprobante = $envio->comprobante;

                    ($exist_comprobante !="" && $exist_comprobante !=null)
                        ? $firmado = true
                        : $firmado = false;
                    $facturado = getFacturado($envio->id_envio,5);
                    $factura_tercero = getFacturaClienteTercero($envio->id_envio) != "" ? true : false;
                @endphp
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title col-md-3" style="margin-top: 5px;"><b>Factura# {{isset($envio->comprobante) ? $envio->comprobante->secuencial : ""}}</b>
                           </div>
                        <div class="box-title col-md-5" style="margin-top: 5px;"><b>Cliente:</b>
                            {{$envio->nombre}}
                        </div>
                        <div class="box-title col-md-4 text-right"><b>Fecha:</b>
                            <input type="date"  id="fecha_envio" name="fecha_envio" style="color:black"
                                   {{($facturado) ? "disabled='disabled'" : ""}}
                                   value="{{Carbon\Carbon::parse($envio->fecha_envio)->format('Y-m-d')}}">
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
                                        @if($envio->pedido->tipo_especificacion=="N")
                                            PRECIO X VARIEDAD
                                        @elseif($envio->pedido->tipo_especificacion=="T")
                                            TINTURADO
                                        @endif
                                    </th>
                                    @if($envio->pedido->tipo_especificacion=="N")
                                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                            style="border-color: #9d9d9d;width:100px">
                                            PRECIO X ESPECIFICACIÓN
                                        </th>
                                    @endif
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
                                                    <input  {{($facturado) ? "disabled='disabled'" : ""}} type="number" min="0" id="cantidad_piezas_{{$i+1}}_{{($x+1)}}" style="border: none" onchange="calcular_precio_envio()"
                                                            name="cantidad_piezas_{{$i+1}}_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" class="text-center form-control cantidad_{{$i+1}}_{{($x+1)}} input_cantidad_{{$i+1}}" value="{{$det_ped->cantidad}}">
                                                    @if($x ==0)
                                                        <input type="hidden" id="cant_esp" value="">
                                                        <input type="hidden" id="cant_esp_fijas" value="">
                                                    @endif
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
                                                @if($envio->pedido->tipo_especificacion=="N")
                                                    <td id="td_precio_variedad_{{$i+1}}_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;text-align: center;" >
                                                        <input type="number" name="precio_{{$i+1}}_{{($x+1)}}" id="precio_{{$i+1}}_{{($x+1)}}_{{$b}}" class="form-control text-center precio_{{$i+1}}_{{($x+1)}} form-control"
                                                               style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_envio()" value="{{explode(";",explode('|',$det_ped->precio)[$b-1])[0]}}"   {{($facturado) ? "disabled='disabled'" : ""}} required>
                                                        <input type="hidden" class="id_detalle_esp_emp_{{$i+1}}_{{($x+1)}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                                    </td>
                                                @elseif($envio->pedido->tipo_especificacion=="T")
                                                    @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                        <td rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;text-align: center;" >
                                                            <button type="button" class="btn btn-xs btn-success" title="Ver tinturado" onclick="editar_pedido_tinturado('{{$envio->id_pedido}}','{{$x}}',true,false)">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    @endif
                                                @endif
                                                @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                    @if($envio->pedido->tipo_especificacion=="N")
                                                        <td id="td_precio_especificacion_{{$i+1}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center"
                                                            rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                        </td>
                                                    @endif
                                                    <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                                        rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                        <select name="id_agencia_carga_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" id="id_agencia_carga_{{$i+1}}_{{$x+1}}"
                                                                class="text-center form-control" style="border: none; width: 100%"  {{($facturado) ? "disabled='disabled'" : ""}}>
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
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 class="box-title">DATOS DE EXPORTACIÓN</h3>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="button" class="btn btn-{{$factura_tercero ?  "primary" : "default"}} btn-xs" title="Facturar a terceros" onclick="factura_tercero('{{$envio->id_envio}}','{{csrf_token()}}','{{$envio->id_pedido}}','{{$vista}}')">
                                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                                </button>
                                                @if($facturado == null)
                                                    @if($factura_tercero)
                                                        <button type="button" class="btn btn-danger btn-xs" title="Eliminar factura a tercero" onclick="delete_factura_tercero('{{$envio->id_envio}}','{{csrf_token()}}','{{$envio->id_pedido}}','{{$vista}}')">
                                                            <i class="fa fa-user-times" aria-hidden="true"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            @php

                                                   $envio->codigo_pais == ""
                                                       ? $p = $envio->pais_cliente
                                                       : $p = $envio->codigo_pais;
                                                   if(isset($envio->dae)){
                                                       $dae = $envio->dae;
                                                       $codgio_dae =  $envio->codigo_dae;
                                                   }else{
                                                       $mes =Carbon\Carbon::parse($envio->fecha_envio)->format('m');
                                                       $anno = Carbon\Carbon::parse($envio->fecha_envio)->format('Y');
                                                       $ultimo_dia_mes = Carbon\Carbon::parse($envio->fecha_envio)->endOfMonth()->toDateString();
                                                       if($ultimo_dia_mes == Carbon\Carbon::parse($envio->fecha_envio)->toDateString()){
                                                            $mes = Carbon\Carbon::parse($envio->fecha_envio)->addMonth()->format('m');
                                                            $anno = Carbon\Carbon::parse($envio->fecha_envio)->addMonth()->format('Y');
                                                       }
                                                       $d = getCodigoDae(strtoupper($p),$mes,$anno,isset($envio->id_configuracion_empresa) ? $envio->id_configuracion_empresa : getConfiguracionEmpresa()->id_configuracion_empresa);
                                                       $dae = isset($d->codigo_dae) ? $d->codigo_dae : "";
                                                       $codigo_dae = isset($d->dae) ? $d->dae : "";
                                                   }
                                            @endphp
                                            <div class="col-md-3">
                                                <label for="empresa">Empresa</label>
                                                <select class="form-control" name="id_empresa" id="id_empresa" onchange="buscar_codigo_dae(this,'form_envios_{{$i+1}}')">
                                                    @foreach($empresas as $e)
                                                        <option {{isset($envio->id_configuracion_empresa) ? ($envio->id_configuracion_empresa == $e->id_configuracion_empresa ? "selected" : "") : ""}}
                                                                value="{{$e->id_configuracion_empresa}}">{{$e->razon_social}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="dae">CÓDIGO DAE</label>
                                                <input type="text" placeholder="CODÍGO DAE" class="form-control" {{($facturado) ? "disabled='disabled'" : ""}}
                                                {{$factura_tercero ?  "disabled" : ""}} {{-- {{$dae != "" ? "disabled='disabled'" : ""}}--}} id="codigo_dae" name="codigo_dae" value="{{isset($envio->codigo_dae) ? $envio->codigo_dae : $codigo_dae}}"
                                                    {{(strtoupper($p) != getConfiguracionEmpresa(isset($envio->comprobante) ? $envio->comprobante->empresa->id_configuracion_empresa : null)->codigo_pais) ? "required" : "" }} >
                                            </div>
                                            <div class="col-md-3">
                                                <label for="dae">DAE</label>
                                                <input type="text" placeholder="DAE" class="form-control" {{($facturado) ? "disabled='disabled'" : ""}}
                                                {{$factura_tercero ?  "disabled" : ""}} {{-- {{$dae != "" ? "disabled='disabled'" : ""}}--}} id="dae" name="dae" value="{{$dae}}"
                                                    {{(strtoupper($p) != getConfiguracionEmpresa(isset($envio->comprobante) ? $envio->comprobante->empresa->id_configuracion_empresa : null)->codigo_pais) ? "required" : "" }} >
                                            </div>
                                            <div class="col-md-3">
                                                <label for="guia_madre">Guía madre</label>
                                                <input type="text" placeholder="Guía madre" class="form-control" {{($facturado) ? "disabled='disabled'" : ""}}
                                                id="guia_madre" name="guia_madre" value="{{$envio->guia_madre}}">
                                            </div>
                                        </div>
                                        <div class="row" >
                                            <div class="col-md-3">
                                                <label for="guia_hija">Guía hija</label>
                                                <input type="text" placeholder="Guía hija" class="form-control" {{($facturado) ? "disabled='disabled'" : ""}}
                                                id="guia_hija" name="guia_hija" value="{{$envio->guia_hija}}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="aerolinea">Aerolínea</label>
                                                <select id="aerolinea" name="aerolinea" class="form-control"
                                                        {{($facturado) ? "disabled='disabled'" : ""}}required>
                                                    @if(getEnvio($envio->id_envio)->detalles[0]->id_aerolinea == null)
                                                        <option selected disabled value="">Seleccione</option>
                                                    @endif
                                                    @foreach($aerolineas as $a)
                                                        <option {!! getEnvio($envio->id_envio)->detalles[0]->id_aerolinea ==  $a->id_aerolinea ? "selected" : ""!!} value="{{$a->id_aerolinea}}">{{$a->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="Email">Cliente</label>
                                                @php
                                                    if(isset($envio->email)){
                                                        $nombre = $envio->nombre;
                                                    }else{
                                                        foreach(getCliente($envio->id_cliente)->detalles as $detCliente){
                                                            if($detCliente->estado==1)
                                                                 $nombre = $detCliente->nombre;
                                                        }
                                                    }
                                                @endphp
                                                <input type="text" placeholder="Nombre" {{$factura_tercero ?  "disabled" : ""}} class="form-control" {{($facturado) ? "disabled='disabled'" : ""}}
                                                id="nombre" name="nombre" value="{{$nombre}}" required >
                                            </div>
                                            <div class="col-md-3">
                                                <label for="Email">Email</label>
                                                @php
                                                    if(isset($envio->email)){
                                                        $email = $envio->email;
                                                    }else{
                                                        foreach(getCliente($envio->id_cliente)->detalles as $detCliente){
                                                            if($detCliente->estado==1)
                                                                $email = $detCliente->correo;
                                                        }
                                                    }
                                                @endphp
                                                <input type="email" placeholder="Email" {{$factura_tercero ?  "disabled" : ""}} class="form-control" {{($facturado) ? "disabled='disabled'" : ""}}
                                                id="email" name="email" value="{{$email}}" required >
                                            </div>

                                        </div>
                                        <div class="row" >
                                            <div class="col-md-3">
                                                @php
                                                    if(isset($envio->telefono)){
                                                        $telefono = $envio->telefono;
                                                    }else{
                                                        $telefono = $envio->telefono_cliente;
                                                    }
                                                @endphp
                                                <label for="telefono">Teléfono</label>
                                                <input type="text" placeholder="Teléfono" {{$factura_tercero ?  "disabled" : ""}}
                                                class="form-control" {{($facturado) ? "disabled='disabled'" : ""}} id="telefono" name="telefono_{{$i+1}}"
                                                       value="{{$telefono}}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="pais">País</label>
                                                <select id="codigo_pais" name="codigo_pais" class="form-control" {{$factura_tercero ?  "disabled" : ""}} onchange="buscar_codigo_dae(this,'form_envios_{{$i+1}}')"
                                                        {{($facturado) ? "disabled='disabled'" : ""}} required>
                                                    @php
                                                        $envio->codigo_pais == ""
                                                            ? $p = $envio->pais_cliente
                                                            : $p = $envio->codigo_pais;
                                                    @endphp
                                                    @foreach($paises as $pais)
                                                        <option {{ ($p == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                @php
                                                    if(isset($envio->almacen)){
                                                        $almacen = $envio->almacen;
                                                    }else{
                                                         $almacen = $envio->almacen_cliente;
                                                    }
                                                @endphp
                                                <label for="almacen_{{$i+1}}">Anden</label>
                                                <input type="text" placeholder="Anden" class="form-control" {{$factura_tercero ?  "disabled" : ""}} {{($facturado) ? "disabled='disabled'" : ""}}
                                                id="almacen" name="almacen_{{$i+1}}" value="{{$almacen}}">
                                            </div>
                                            <div class="col-md-3">
                                                @php
                                                    if(isset($envio->direccion)){
                                                        $direccion = $envio->direccion;
                                                    }else{
                                                        $direccion = $envio->provincia.", ".$envio->direccion_cliente;
                                                    }
                                                @endphp
                                                <label for="direccion">Destino</label>
                                                <input type="text" placeholder="Dirección" {{$factura_tercero ?  "disabled" : ""}} class="form-control" {{($facturado) ? "disabled='disabled'" : ""}}
                                                id="direccion" name="direccion" value="{{$direccion}}" required>
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="">
                                            <div class="box">
                                                <div class="box-header with-border">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h3 class="box-title">ENVÍO DE CORREOS</h3>
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <button  type="button" class="btn btn-primary btn-xs"  title="Agregar correo" onclick="agregar_correo('form_envios_{{$i+1}}')">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </button>
                                                            <button  type="button" class="btn btn-danger btn-xs"  title="Eliminar correo" onclick="eliminar_correo('form_envios_{{$i+1}}')">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="text-left" id="correos_extras"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: #d2d6de;">
                        <table width="100%" class="table-responsive" style="font-size: 1em;">
                            <tr>
                                <td class="text-center" style="border: none; vertical-align: middle">Piezas: <span id="total_piezas_{{$i+1}}"></span></td>
                                <td class="text-center" style="border: none; vertical-align: middle">Ramos: <span id="total_ramos_{{$i+1}}"></span></td>
                                @php
                                    $precio_total_sin_impuestos = null;
                                    $precio_total_con_impuestos = null;
                                    $tipoImpuesto = null;
                                    $pedido = getPedido($envio->id_pedido);
                                    if($pedido->tipo_especificacion == "T"){
                                        $precio_total_sin_impuestos = 0.00;
                                        $precio_total_con_impuestos = 0.00;
                                        foreach($pedido->detalles as $dp){
                                            foreach($dp->coloraciones as $y => $coloracion){
                                                $cant_esp_emp = $coloracion->especificacion_empaque->cantidad;
                                                foreach($coloracion->marcaciones_coloraciones as $m_c){ //4
                                                    if(empty($coloracion->precio) || $coloracion->precio == null){
                                                        foreach (explode("|", $dp->precio) as $p)
                                                            if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                                                $precio = explode(";",$p)[0];
                                                    }else{
                                                        foreach(explode("|",$coloracion->precio) as $p)
                                                            if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                                                 $precio = explode(";",$p)[0];
                                                    }
                                                    $precio_x_variedad = $m_c->cantidad * $precio  * $cant_esp_emp;
                                                    $precio_total_sin_impuestos += $precio_x_variedad;
                                                }
                                            }
                                        }

                                        $facturaClienTeTercero = getFacturaClienteTercero($envio->id_envio);
                                        $dataCliente = yura\Modelos\DetalleCliente::where([
                                            ['id_cliente' , $pedido->cliente->id_cliente],
                                            ['estado', 1]
                                        ])->first();
                                        if($facturaClienTeTercero != null ){
                                            $codigo_impuesto = $facturaClienTeTercero->codigo_impuesto;
                                            $codigo_porcentaje_impuesto =  $facturaClienTeTercero->codigo_impuesto_porcentaje;
                                        }else{
                                            $codigo_impuesto = $dataCliente->codigo_impuesto;
                                            $codigo_porcentaje_impuesto = $dataCliente->codigo_porcentaje_impuesto;
                                        }
                                        $tipoImpuesto = getTipoImpuesto($codigo_impuesto,$codigo_porcentaje_impuesto);
                                        $precio_total_con_impuestos = is_numeric($tipoImpuesto->porcentaje) ? ($precio_total_sin_impuestos + number_format($precio_total_sin_impuestos * ($tipoImpuesto->porcentaje / 100), 2, ".", "")) : $precio_total_sin_impuestos;
                                    }
                                @endphp
                                @if(!isset($precio_total_sin_impuestos))
                                    <td class="text-center" style="border: none; vertical-align: middle">
                                        Sub Total: $ <span id="sub_total_{{$i+1}}"></span>
                                    </td>
                                @else
                                    <td class="text-center" style="border: none; vertical-align: middle">
                                        Sub Total: ${{number_format($precio_total_sin_impuestos,2,".","")}}
                                    </td>
                                @endif
                                @if($pedido->tipo_especificacion == "T")
                                    <td class="text-center" style="border: none; vertical-align: middle">
                                        <span>{{$envio->nombre_impuesto}}: </span>{{$tipoImpuesto->porcentaje}}%
                                    </td>
                                @else
                                    <td class="text-center" style="border: none; vertical-align: middle">
                                        <span>{{$envio->nombre_impuesto}}: </span> <span id="iva_{{$i+1}}"> {{$tipoImpuestoCliente->porcentaje}}%</span>
                                    </td>
                                @endif
                                @if(!isset($precio_total_con_impuestos))
                                    <td class="text-center" style="border: none; vertical-align: middle">
                                        Total: $<span id="total_{{$i+1}}"></span>
                                    </td>
                                @else
                                    <td class="text-center" style="border: none; vertical-align: middle">
                                        Total: ${{isset($precio_total_con_impuestos) ? number_format($precio_total_con_impuestos,2,".","") : "" }}
                                    </td>
                                @endif
                                <td class="text-right" style="border: none; vertical-align: middle;width:26%">
                                    @if($facturado == null)
                                        <button type="button" class="btn btn-success" onclick="actualizar_envio('{{$envio->id_envio}}','form_envios_{{$i+1}}','{{getPedido($envio->id_pedido)->tipo_especificacion}}','{{csrf_token()}}','{{$envio->id_pedido}}','{{$vista}}')">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>  Guardar
                                        </button>
                                    @endif
                                    @if(!empty(getUsuario(session::get('id_usuario'))->punto_acceso))
                                        @if($envio->confirmado)
                                            @if($facturado == null)
                                                <button type="button" class="btn btn-success" onclick="genera_comprobante_cliente('{{$envio->id_envio}}','form_envios_{{$i+1}}','{!! $firmado ? "update" : "" !!}','{{csrf_token()}}','{!! $firmado ? $envio->pedido->id_comprobante_temporal : "" !!}')">
                                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                    {!! $firmado ? "Actualizar" : "Generar" !!} factura
                                                </button>
                                            @else
                                                <span class="badge bg-green" style="margin-top:8px;font-size: 13px;">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true"></i> Facturado
                                                </span>
                                            @endif
                                        @endif
                                    @else
                                        Sin permisos para facturación
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- box-footer -->
                </div>
                <!-- /.box -->
            </form>
            <hr />
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
    calcular_precio_envio();
</script>
