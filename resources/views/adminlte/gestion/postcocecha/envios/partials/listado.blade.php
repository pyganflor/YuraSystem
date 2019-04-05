<div id="table_envios" style="margin-top: 20px">
    @if(sizeof($envios)>0)
            @foreach($envios as $i => $envio)
                <form id="form_envios_{{$i+1}}">
                    @php $firmado = getFacturado($envio->id_envio,1); $facturado = getFacturado($envio->id_envio,5); @endphp
                    <div class="box box-solid box-primary">
                        <div class="box-header with-border">
                            <div class="box-title col-md-3" style="margin-top: 5px;"><b>Envío N#:</b>
                                ENV{{str_pad($envio->id_envio,9,"0",STR_PAD_LEFT)}}</div>
                            <div class="box-title col-md-5" style="margin-top: 5px;"><b>Cliente:</b>
                                {{$envio->nombre}}
                            </div>
                            <div class="box-title col-md-4 text-right"><b>Fecha envío:</b>
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
                                                             <input  {{($facturado) ? "disabled='disabled'" : ""}} type="number" min="0" id="cantidad_piezas_{{$i+1}}_{{($x+1)}}" style="border: none" onchange="calcular_precio_envio()"
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
                                                                style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_envio()" value="{{explode(";",explode('|',$det_ped->precio)[$b-1])[0]}}"   {{($facturado) ? "disabled='disabled'" : ""}} required>
                                                         <input type="hidden" class="id_detalle_esp_emp_{{$i+1}}_{{($x+1)}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                                     </td>
                                                     @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                                         <td id="td_precio_especificacion_{{$i+1}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center"
                                                             rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                                         </td>
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
                                            <div class="col-md-6">
                                                <h3 class="box-title">DATOS DE EXPORTACIÓN</h3>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="button" class="btn btn-primary" title="Facturar a otra persona">
                                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="dae">DAE</label>
                                                    @php
                                                        $envio->codigo_pais == ""
                                                            ? $p = $envio->pais_cliente
                                                            : $p = $envio->codigo_pais;

                                                        if(isset($envio->dae)){
                                                            $dae = $envio->dae;
                                                        }else{
                                                            $d = getCodigoDae(strtoupper($p),Carbon\Carbon::parse($envio->fecha_envio)->format('m'),Carbon\Carbon::parse($envio->fecha_envio)->format('Y'));
                                                            $dae = isset($d->codigo_dae) ? $d->codigo_dae : "";
                                                        }
                                                     @endphp
                                                    <input type="text" placeholder="DAE" class="form-control"
                                                           {{($facturado) ? "disabled='disabled'" : ""}}
                                                           {{$dae != "" ? "disabled='disabled'" : ""}}
                                                           id="dae" name="dae" value="{{$dae}}" {{(strtoupper($p) != getConfiguracionEmpresa()->codigo_pais) ? "required" : "" }} >
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="guia_madre">Guía madre</label>
                                                    <input type="text" placeholder="Guía madre" class="form-control"
                                                           {{($facturado) ? "disabled='disabled'" : ""}}
                                                           id="guia_madre" name="guia_madre" required value="{{$envio->guia_madre}}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="guia_hija">Guía hija</label>
                                                    <input type="text" placeholder="Guía hija" class="form-control"
                                                           {{($facturado) ? "disabled='disabled'" : ""}}
                                                           id="guia_hija" name="guia_hija" value="{{$envio->guia_hija}}">
                                                </div>
                                                <div class="col-md-3">

                                                    <label for="pais">País</label>
                                                    <select id="codigo_pais" name="codigo_pais" class="form-control" onchange="buscar_codigo_dae(this,'form_envios_{{$i+1}}')"
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
                                            </div>
                                            <div class="row" >
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
                                                    <input type="email" placeholder="Email" class="form-control"
                                                           {{($facturado) ? "disabled='disabled'" : ""}}
                                                           id="email" name="email" value="{{$email}}" required >
                                                </div>
                                                <div class="col-md-3">
                                                    @php
                                                        if(isset($envio->telefono)){
                                                            $telefono = $envio->telefono;
                                                        }else{
                                                            $telefono = $envio->telefono_cliente;
                                                        }
                                                    @endphp
                                                    <label for="telefono">Teléfono</label>
                                                    <input type="text" placeholder="Teléfono" class="form-control"
                                                           {{($facturado) ? "disabled='disabled'" : ""}}
                                                           id="telefono" name="telefono_{{$i+1}}" value="{{$telefono}}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="agencia_de_transporte">Agencia de transporte</label>
                                                    <select id="agencia_transporte" name="agencia_transporte" class="form-control"
                                                            {{($facturado) ? "disabled='disabled'" : ""}} required>
                                                        @if(getEnvio($envio->id_envio)->detalles[0]->id_aerolinea == null)
                                                            <option selected disabled value="">Seleccione</option>
                                                        @endif
                                                        @foreach($aerolineas as $a)
                                                            <option {!! getEnvio($envio->id_envio)->detalles[0]->id_aerolinea ==  $a->id_aerolinea ? "selected" : ""!!} value="{{$a->id_aerolinea}}">{{$a->nombre}}</option>
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
                                                    <label for="almacen_{{$i+1}}">Almacen</label>
                                                    <input type="text" placeholder="Almacen" class="form-control"
                                                           {{($facturado) ? "disabled='disabled'" : ""}}
                                                           id="almacen" name="almacen_{{$i+1}}" value="{{$almacen}}">
                                                </div>
                                            </div>
                                            <div class="row" >
                                                <div class="col-md-12">
                                                        @php
                                                            if(isset($envio->direccion)){
                                                              $direccion = $envio->direccion;
                                                            }else{
                                                                $direccion = $envio->provincia.", ".$envio->direccion_cliente;
                                                            }
                                                        @endphp
                                                        <label for="direccion">Destino</label>
                                                        <input type="text" placeholder="Dirección" class="form-control"
                                                               {{($facturado) ? "disabled='disabled'" : ""}}
                                                               id="direccion" name="direccion" value="{{$direccion}}" required>
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
                            @if($facturado == null)
                                <button type="button" class="btn btn-success" onclick="actualizar_envio('{{$envio->id_envio}}','form_envios_{{$i+1}}')">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>  Guardar
                                </button>
                            @endif
                            @if(!empty(getUsuario(session::get('id_usuario'))->punto_acceso))
                                @if($envio->confirmado)
                                    @if($facturado == null)
                                        <button type="button" class="btn btn-success" onclick="genera_comprobante_cliente('{{$envio->id_envio}}','form_envios_{{$i+1}}','{!! $firmado ? "update" : "" !!}')">
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
                            </div>
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
</script>
