<ul class="nav nav-tabs">
    <li class="active text-center" style="width: 33.3%;"><a data-toggle="tab" href="#pedido_creado"><i class="fa fa-archive"></i> Pedido creado </a></li>
    <li class="text-center" style="width: 33.3%;"><a data-toggle="tab" href="#div_especificaciones_orden_semanal_update"><i class="fa fa-cubes"></i> Especificaciones </a></li>
    <li class="text-center" style="width: 33.3%;" onclick="ver_todas_distribuciones()">
        <a data-toggle="tab" href="#div_distribucion_orden_semanal"><i class="fa fa-th"></i> Distribución </a>
        {{--<buttom type="buttom" class="btn btn-primary btn-xs" style="position: absolute;bottom: 11px;right: 30px;"><i class="fa fa-refresh"></i></buttom>--}}
    </li>
</ul>
<div class="tab-content">
    <div id="pedido_creado" class="tab-pane fade in active">
        @foreach($pedido->detalles as $x=> $det_ped)
            <div class="well sombra_estandar well_detalle_pedido">
                <input type="hidden" class="id_det_ped" value="{{$det_ped->id_detalle_pedido}}">
                <legend style="font-size: 1.1em; margin-bottom: 0">
                    <strong>Detalle del pedido {{$x+1}}</strong>
                    <buttom type="buttom" class="btn btn-danger btn-xs pull-right" onclick="delete_detalle_pedido('{{$det_ped->id_detalle_pedido}}','{{$pedido->id_pedido}}','{{csrf_token()}}')">
                        <i class="fa fa-times"></i> Eliminar detalle
                    </buttom>
                </legend>
                <div style="overflow-x: scroll">
                    <table class="table-bordered" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                        <tr style="background-color: #e9ecef">
                            <th class="text-center" style="border-color: #9d9d9d" width="85px">
                                CANTIDAD
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                Nº Empaque
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                Nº Presentación
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                VARIEDAD
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                CALIBRE
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                CAJA
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                RAMOS x CAJA
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                PRESENTACIÓN
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                TALLOS x RAMO
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                LONGITUD
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                U. MEDIDA
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d" width="50px">
                                PRECIO
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d" width="50px">
                                MARCACIONES
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d" width="85px">
                                COLORACIONES
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d;width:100px">
                                AGENCIA CARGA
                            </th>
                            @foreach($pedido->cliente->cliente_datoexportacion as $cli_dat_exp)
                                @php
                                    $detped_datexp = getDatosExportacion($det_ped->id_detalle_pedido, $cli_dat_exp->id_dato_exportacion);
                                @endphp
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef;width:100px">
                                    {{$cli_dat_exp->datos_exportacion->nombre}}
                                </th>
                            @endforeach
                        </tr>
                        @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                            @php
                                $ramos_x_caja = 0;
                            @endphp
                            @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                @php
                                    $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp->id_detalle_especificacionempaque);
                                    $ramos_x_caja += isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad;
                                @endphp
                                <tr>
                                    @if($pos_esp_emp == 0 && $pos_det_esp == 0)
                                        <td class="text-center" style="border-color: #9d9d9d"
                                            rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                            <input type="number" id="cantidad_piezas" name="cantidad_piezas" value="{{$det_ped->cantidad}}" required
                                                   onkeypress="return isNumber(event)" style="border: none" class="text-center" min="1">
                                        </td>
                                    @endif
                                    @if($pos_det_esp == 0)
                                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                            EMP-{{$pos_esp_emp + 1}}
                                        </td>
                                    @endif
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        P-{{$pos_det_esp + 1}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp->variedad->nombre}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp->clasificacion_ramo->nombre}}
                                        {{$det_esp->clasificacion_ramo->unidad_medida->siglas}}
                                    </td>
                                    @if($pos_det_esp == 0)
                                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                            {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                        </td>
                                    @endif
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        <input type="number" style="width:55px;text-align:center" min="1" class="input_r_x_c_{{$esp_emp->id_especificacion_empaque}}"
                                               onchange="cambia_input_r_x_c(this,'{{$esp_emp->id_especificacion_empaque}}','{{$det_esp->id_detalle_especificacionempaque}}'), calcular_totales_tinturado('{{$esp_emp->id_especificacion_empaque}}')"
                                               onkeyup="cambia_input_r_x_c(this,'{{$esp_emp->id_especificacion_empaque}}','{{$det_esp->id_detalle_especificacionempaque}}'), calcular_totales_tinturado('{{$esp_emp->id_especificacion_empaque}}')"
                                               value="{{isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad}}">
                                        {{--isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad--}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp->empaque_p->nombre}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp->tallos_x_ramos}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp->longitud_ramo}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        @if($det_esp->longitud_ramo)
                                            {{$det_esp->unidad_medida->siglas}}
                                        @endif
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        <input id="precio_det_esp_{{$det_esp->id_detalle_especificacionempaque}}"
                                               style="width: 50px; background-color: #e9ecef" min="0" class="text-center" type="number"
                                               name="precio_det_esp_{{$det_esp->id_detalle_especificacionempaque}}"
                                               value="{{getPrecioByDetEsp($det_ped->precio, $det_esp->id_detalle_especificacionempaque)}}">
                                    </td>
                                    @if($pos_det_esp == 0)
                                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                            <input type="number" id="marcaciones_{{$esp_emp->id_especificacion_empaque}}" onkeypress="return isNumber(event)"
                                                   name="marcaciones_{{$esp_emp->id_especificacion_empaque}}" readonly
                                                   value="{{count($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'])}}"
                                                   required min="1" style="border: none" class="text-center" width="50px">
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                            <input type="number" id="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                                   onkeypress="return isNumber(event)" readonly
                                                   name="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                                   value="{{count($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'])}}"
                                                   required min="1" style="border: none" class="text-center" width="50px">
                                        </td>
                                    @endif
                                    @if($pos_esp_emp == 0 && $pos_det_esp == 0)
                                        <td class="text-center" style="border-color: #9d9d9d"
                                            rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                            <select name="id_agencia_carga" id="id_agencia_carga" required style="width: 100px; border: none">
                                                @foreach($pedido->cliente->cliente_agencia_carga as $item)
                                                    <option value="{{$item->id_agencia_carga}}" {{$item->id_agencia_carga == $det_ped->id_agencia_carga ? 'selected' : ''}}>
                                                        {{$item->agencia_carga->nombre}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        @foreach($pedido->cliente->cliente_datoexportacion as $cli_dat_exp)
                                            @php
                                                $detped_datexp = getDatosExportacion($det_ped->id_detalle_pedido, $cli_dat_exp->id_dato_exportacion);
                                            @endphp
                                            <td class="text-center dato_exportacion_{{$det_ped->id_detalle_pedido}}" style="border-color: #9d9d9d"
                                                rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                                <input type="text" id="dato_exportacion_{{$cli_dat_exp->id_dato_exportacion}}"
                                                       class="form-control dato_exportacion_{{$det_ped->id_detalle_pedido}}"
                                                       value="{{$detped_datexp != '' ? $detped_datexp->valor : ''}}" minlength="1"
                                                       style="text-transform: uppercase;width:100px">
                                                <input type="hidden" value="{{$cli_dat_exp->id_dato_exportacion}}" class="id_dato_exportacion_{{$det_ped->id_detalle_pedido}}">
                                            </td>
                                            <input type="hidden" class="id_dato_exportacion" value="{{$cli_dat_exp->id_dato_exportacion}}">
                                        @endforeach
                                        @if($pedido->cliente->cliente_datoexportacion->count()>0)
                                                <td class="text-center" style="border-color: #9d9d9d" rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                                    <button class="btn btn-success btn-sm" title="Guardar datos de exportación"
                                                        onclick="update_dato_exp_pedio_tinturado('{{$det_ped->id_detalle_pedido}}','{{@csrf_token()}}')">
                                                        <i class="fa fa-floppy-o"></i>
                                                    </button>
                                                </td>
                                        @endif
                                    @endif
                                </tr>
                                <input type="hidden"
                                       id="ramos_x_caja_det_esp_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                       value="{{isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad}}">
                                <input type="hidden" class="id_det_esp_{{$esp_emp->id_especificacion_empaque}}"
                                       value="{{$det_esp->id_detalle_especificacionempaque}}">
                            @endforeach
                            <input type="hidden" id="ramos_x_caja_{{$esp_emp->id_especificacion_empaque}}" value="{{$ramos_x_caja}}">
                            <input type="hidden" class="id_esp_emp" value="{{$esp_emp->id_especificacion_empaque}}">
                        @endforeach
                    </table>
                </div>
                @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                    <legend style="font-size: 1em; margin-bottom: 0;margin-top:10px" >
                        <strong>
                            Distribución EMP-{{$pos_esp_emp + 1}}
                            <button type="button" class="btn btn-xs btn-primary" onclick="add_marcacion('{{$esp_emp->id_especificacion_empaque}}')">
                                <i class="fa fa-fw fa-plus"></i> Marcación
                            </button>
                            <button type="button" class="btn btn-xs btn-primary" onclick="add_coloracion('{{$esp_emp->id_especificacion_empaque}}')">
                                <i class="fa fa-fw fa-plus"></i> Coloración
                            </button>

                            <button type="button" class="btn btn-xs btn-danger pull-right"
                                    onclick="delete_coloracion('{{$esp_emp->id_especificacion_empaque}}')">
                                <i class="fa fa-fw fa-times"></i> Coloración
                            </button>
                            <button type="button" class="btn btn-xs btn-danger pull-right" style="margin-right: 3px"
                                    onclick="delete_marcacion('{{$esp_emp->id_especificacion_empaque}}')">
                                <i class="fa fa-fw fa-times"></i> Marcación
                            </button>

                            <button type="button" id="distribuido_{{$esp_emp->id_especificacion_empaque}}"
                                    class="btn btn-xs btn-success pull-right hide">
                                <i class="fa fa-check"></i> Distribuido
                            </button>

                            @if($det_ped->haveDistribucionByEspEmp($esp_emp->id_especificacion_empaque))
                               {{--<button type="button" class="btn btn-xs btn-danger pull-right elemento_distribuir"
                                        onclick="quitar_distribuciones('{{$det_ped->id_pedido}}','{{csrf_token()}}')">
                                    <i class="fa fa-fw fa-times"></i> Quitar Distribuciones
                                </button>--}}
                                {{--<button type="button" class="btn btn-xs btn-primary pull-right elemento_distribuir"
                                        onclick="ver_distribucion('{{$det_ped->id_detalle_pedido}}')">
                                    <i class="fa fa-fw fa-eye"></i> Ver Distribución
                                </button>--}}
                            @else
                                <button type="button" class="btn btn-xs btn-primary pull-right elemento_distribuir"
                                        onclick="distribuir_pedido_tinturado('{{$det_ped->id_detalle_pedido}}')"
                                        id="distrubir_manual_{{$esp_emp->id_especificacion_empaque}}">
                                    <i class="fa fa-fw fa-exchange"></i> Distribuir
                                </button>
                                <button type="button" class="btn btn-xs btn-info pull-right elemento_distribuir"
                                        id="auto_distribuir_{{$esp_emp->id_especificacion_empaque}}"
                                    onclick="distribuir_pedido_tinturado('{{$det_ped->id_detalle_pedido}}', true, '{{$esp_emp->id_especificacion_empaque}}', '{{csrf_token()}}')">
                                    <i class="fa fa-fw fa-exchange"></i> Auto-Distribuir
                                </button>
                            @endif
                        </strong>
                    </legend>
                    <div style="overflow-x: scroll">
                        <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d"
                               id="tabla_marcacion_coloracion_{{$esp_emp->id_especificacion_empaque}}">
                            <tr>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef;vertical-align: middle" width="150px">
                                    Marcación/Color
                                </th>
                                @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                                    <th id="celda_col_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}" class="text-center col_coloracion col_coloracion_{{$esp_emp->id_especificacion_empaque}} th_col_coloracion col_coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}}"
                                        style="border-color: #9d9d9d" width="100px">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="border:none;background: transparent;padding: 5px;">
                                                <input type="checkbox" value="{{$pos_color}}"
                                                       class="check_coloracion_{{$esp_emp->id_especificacion_empaque}}
                                                           col_coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}}
                                                           coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}}">
                                            </span>
                                            <select name="color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}" style="width: 100px;font-size:11px"
                                                id="color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}"
                                                class="col_coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}} select_coloracion">
                                                @foreach(getColores() as $c)
                                                    <option value="{{$c->id_color}}" {{$c->id_color == $color->id_color ? 'selected' : ''}}>
                                                        {{$c->nombre}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" id="id_color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}"
                                               name="id_color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}" value="{{$color->id_color}}">
                                    </th>
                                @endforeach
                                @if(count($esp_emp->detalles) > 1)
                                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="100px">
                                        Parcial
                                    </th>
                                @endif
                                {{--<th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                                    Precio
                                </th>--}}
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                                    Total
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                                    Piezas
                                </th>
                                <th class="text-center elemento_distribuir" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                                    Distribución
                                </th>
                            </tr>
                            @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'] as $pos_marca => $marca)
                                <tr style="border: 2px solid #9d9d9d" class="tr_marcacion_{{$esp_emp->id_especificacion_empaque}}">
                                    <td class="text-center" style="border-color: #9d9d9d;text-align:middle">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="border:none;padding: 0;">
                                                <input type="checkbox" class="check_marcacion_{{$esp_emp->id_especificacion_empaque}} marcacion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_marca+1}}">
                                            </span>
                                            <input type="text" id="nombre_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                                   name="nombre_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                                   value="{{getMarcacion($marca->id_marcacion)->nombre}}" style="border: none;padding: 0;width: 110px;"
                                                   class="text-center form-control form-control-sm input_marcacion_{{$esp_emp->id_especificacion_empaque}}">
                                        </div>
                                        <input type="hidden" id="id_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                               name="id_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}" value="{{$marca->id_marcacion}}">
                                    </td>
                                    @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                                        <td class="text-center col_coloracion td_col_coloracion_{{$esp_emp->id_especificacion_empaque}} col_coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}} col_precio_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}}"
                                            style="border-color: #9d9d9d;" width="100px" >
                                            <ul class="list-unstyled">
                                                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                                    <li>
                                                        <div class="input-group" style="width: 100px">
                                                            <span class="input-group-caddon" style="background-color: #e9ecef">
                                                                P-{{$pos_det_esp + 1}}
                                                            </span>
                                                            @php
                                                                $marc_col = getMarcacion($marca->id_marcacion)->getMarcacionColoracionByDetEsp($color->id_coloracion, $det_esp->id_detalle_especificacionempaque);
                                                            @endphp
                                                            <input value="{{$marc_col != '' ? $marc_col->cantidad : 0}}"
                                                                   id="ramos_marcacion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                                   name="ramos_marcacion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                                   style="width: 100%; background-color: {{getColor($color->id_color)->fondo}};
                                                                       color: {{getColor($color->id_color)->texto}}" min="0" type="number"
                                                                   class="text-center elemento_color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}} col_coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}} ramos_marcacion_{{$esp_emp->id_especificacion_empaque}}"
                                                                   onchange="calcular_totales_tinturado('{{$esp_emp->id_especificacion_empaque}}')">
                                                            <input type="number" min="0" style="width: 100%;background-color: #e9ecef;text-align:center" value="{{$marc_col != '' ? $marc_col->precio : ''}}"
                                                                   class="col_precio_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}}"
                                                                   id="p_marcacion_coloracion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                                   name="p_marcacion_coloracion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}">
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    @endforeach
                                    @if(count($esp_emp->detalles) > 1)
                                        <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                                            <ul class="list-unstyled">
                                                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                                    <li>
                                                        <div class="input-group" style="width: 100px">
                                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                                P-{{$pos_det_esp + 1}}
                                                            </span>
                                                            <input type="number"
                                                                   id="parcial_marcacion_{{$pos_marca}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                                   name="parcial_marcacion_{{$pos_marca}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                                   style="width: 100%; background-color: #357ca5; color: white" readonly
                                                                   class="text-center">
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </th>
                                    @endif
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        <input type="text" id="total_ramos_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                               name="total_ramos_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}" readonly
                                               class="text-center ramos_marcacion_{{$esp_emp->id_especificacion_empaque}} total_ramos_marcacion_{{$esp_emp->id_especificacion_empaque}}"
                                               value="{{getMarcacion($marca->id_marcacion)->ramos}}"
                                               style="background-color: #357ca5; color: white; width: 85px">
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        <input type="text" id="total_piezas_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                               name="total_piezas_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}" readonly
                                               class="text-center piezas_marcacion_{{$esp_emp->id_especificacion_empaque}}"
                                               value="{{getMarcacion($marca->id_marcacion)->piezas}}"
                                               style="background-color: #357ca5; color: white; width: 85px">
                                    </td>
                                    <td class="text-center elemento_distribuir" style="border-color: #9d9d9d">
                                        <select name="distribucion_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                                id="distribucion_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                                style="background-color: #357ca5; color: white; width: 60px;" class="distribucion_m_{{$esp_emp->id_especificacion_empaque}}">
                                            @for($i = getMarcacion($marca->id_marcacion)->piezas; $i > 0; $i--)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="tr_parcial_{{$esp_emp->id_especificacion_empaque}}">
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                    Totales
                                </th>
                                @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                                    <th class="text-center th_parcial col_coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}}" style="border-color: #9d9d9d;" width="100px" >
                                        <ul class="list-unstyled">
                                            @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                                <li>
                                                    <div class="input-group" style="width: 100px">
                                                        <span class="input-group-addon" style="background-color: #e9ecef">
                                                            P-{{$pos_det_esp + 1}}
                                                        </span>
                                                        <input type="number" class="text-center valor_parcial" min="0" readonly
                                                               id="parcial_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                               name="parcial_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                               style="width: 100%; background-color: #357ca5; color: white">
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </th>
                                @endforeach
                                @if(count($esp_emp->detalles) > 1)
                                    <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                                        <ul class="list-unstyled">
                                            @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                                <li>
                                                    <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                                        <input type="number"
                                                               id="parcial_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                               name="parcial_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                               style="width: 100%; background-color: #357ca5; color: white" readonly class="text-center">
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </th>
                                @endif
                                {{--<th class="text-center" style="border-color: #9d9d9d"></th>--}}
                                <td class="text-center" style="border-color: #9d9d9d">
                                    <input type="text" id="total_ramos_{{$esp_emp->id_especificacion_empaque}}"
                                           name="total_ramos_{{$esp_emp->id_especificacion_empaque}}" readonly class="text-center"
                                           style="background-color: #357ca5; color: white; width: 85px">
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    <input type="text" id="total_piezas_{{$esp_emp->id_especificacion_empaque}}"
                                           name="total_piezas_{{$esp_emp->id_especificacion_empaque}}" readonly class="text-center"
                                           style="background-color: #357ca5; color: white; width: 85px">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef"
                                    rowspan="{{count($esp_emp->detalles) > 1 ? 2 : ''}}">
                                    Precios
                                </th>
                                @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                                    <th class="text-center precio_col_coloracion col_coloracion_{{$esp_emp->id_especificacion_empaque}}_{{$pos_color}} " style="border-color: #9d9d9d;" width="100px" >
                                        <ul class="list-unstyled">
                                            @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                                <li>
                                                    <div class="input-group" style="width: 100px">
                                                <span class="input-group-addon" style="background-color: #e9ecef">
                                                    P-{{$pos_det_esp + 1}}
                                                </span>
                                                        <input type="number"
                                                               id="precio_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                               name="precio_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                               style="width: 100%; background-color: #e9ecef" class="text-center" min="0"
                                                               value="{{$color->getPrecioByDetEsp($det_esp->id_detalle_especificacionempaque)}}"
                                                        >
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </th>
                                @endforeach
                                {{--<th class="text-center" style="border-color: #9d9d9d"></th>--}}
                                <th class="text-center" style="border-color: #9d9d9d">
                                    PRECIO TOTAL
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d">
                                    $$
                                </th>
                            </tr>
                        </table>
                    </div>
                    <script>
                        calcular_totales_tinturado('{{$esp_emp->id_especificacion_empaque}}', true);
                    </script>
                @endforeach
            </div>
        @endforeach
        <div class="form-row text-center">
            <button type="button" class="btn btn-success" onclick="update_orden_tinturada('{{csrf_token()}}')" id="btn_update_orden_tinturada">
                <i class="fa fa-refresh"></i> Actualizar todo el pedido
            </button>
        </div>
    </div>
    <div id="div_especificaciones_orden_semanal_update" class="tab-pane fade"></div>
    <div id="div_distribucion_orden_semanal" class="tab-pane fade"></div>
</div>
<script>
    add_especificaciones_x_cliente_pedido_tinturado();

    function add_especificaciones_x_cliente_pedido_tinturado() {
        datos = {
            id_cliente: $('#id_cliente_update').val(),
            add_especificaciones:true,
            id_pedido : '{{$pedido->id_pedido}}'
        };
        get_jquery('{{url('pedidos/orden_semanal/listar_especificaciones_x_cliente')}}', datos, function (retorno) {
            $('#div_especificaciones_orden_semanal_update').html(retorno);
        });
    }

    function ver_todas_distribuciones() {
        datos = {
            id_cliente: $('#id_cliente_update').val(),
            add_especificaciones:true,
            id_pedido : '{{$pedido->id_pedido}}'
        };
        if($("#div_distribucion_orden_semanal legend").length<1){
            get_jquery('{{url('pedidos/orden_semanal/listar_distribuciones_pedido')}}', datos, function (retorno) {
                $('#div_distribucion_orden_semanal').html(retorno);
            });
        }
    }

    function actualizar_distribucion(){
        /*id_marcaciones = $("#id_marcaciones").val().substring(0,$("#id_marcaciones").val().length-1);
        arr_marcaciones = id_marcaciones.split("|");
        distribuciones=[];
        $.each(arr_marcaciones,function(i,j){
            distribuciones.push({
                id_distribucion : $("input.id_marcacion_"+j).val(),
                pos_pieza :
            });
        });*/
        $.LoadingOverlay('show');
        id_distribuciones = $("#id_distribuciones").val().substring(0,$("#id_distribuciones").val().length-1);
        arr_distribuciones =id_distribuciones.split("|");
        distribuciones=[];
        $.each(arr_distribuciones,function(i,j){
            distribuciones.push({
                id_distribucion : j,
                pos_pieza : $("input.distribucion_"+j).val()
            });
        })

        datos={
            _token : '{{csrf_token()}}',
            distribuciones : distribuciones
        };
        post_jquery('pedidos/orden_semanal/store_distribuciones_pos_pieza', datos, function () {
            $("#div_distribucion_orden_semanal").empty();
            ver_todas_distribuciones();
        });
        $.LoadingOverlay('hide');
    }

    $(".select_coloracion").change(function($this){
        console.log($this,$($this));
        arrId = $this.target.name.split("_");

        fondo = $('#fondo_color_' +  $("select#"+$this.target.name).val()).val();
        texto = $('#texto_color_' +  $("select#"+$this.target.name).val()).val();

        $('.elemento_color_' + arrId[1] + '_' + arrId[2]).css('background-color', fondo);
        $('.elemento_color_' + arrId[1] + '_' + arrId[2]).css('color', texto);
    });

    function cambia_input_r_x_c(input,idEspEmp,idDetEspEmp){
        r_x_c_esp_emp = 0;
        $.each($("input.input_r_x_c_"+idEspEmp),function (i,j) {
           if(!isNaN(parseInt($(j).val())))
               r_x_c_esp_emp+=parseInt($(j).val())
        });
        $("#ramos_x_caja_"+idEspEmp).val(r_x_c_esp_emp)
        $("#ramos_x_caja_det_esp_"+idDetEspEmp+'_'+idEspEmp).val($(input).val())
    }


</script>
