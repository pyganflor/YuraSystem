<script>

    function calcular_totales_tinturado(esp_emp, ini = false) {
        fil = $('#marcaciones_' + esp_emp).val();
        col = $('#coloraciones_' + esp_emp).val();
        ramos_x_caja = $('#ramos_x_caja_' + esp_emp).val();
        ids_det_esp = $('.id_det_esp_' + esp_emp);

        total = 0;
        for (f = 0; f < fil; f++) {
            total_fila = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (c = 0; c < col; c++) {
                    cant = $('#ramos_marcacion_' + f + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#parcial_marcacion_' + f + '_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
                total_fila += parcial;
            }
            $('#total_ramos_marcacion_' + f + '_' + esp_emp).val(total_fila);
            $('#total_piezas_marcacion_' + f + '_' + esp_emp).val(Math.round((total_fila / ramos_x_caja) * 100) / 100);
            total += total_fila;
        }
        $('#total_ramos_' + esp_emp).val(total);
        $('#total_piezas_' + esp_emp).val(Math.round((total / ramos_x_caja) * 100) / 100);

        for (c = 0; c < col; c++) {
            total_col = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (f = 0; f < fil; f++) {
                    cant = $('#ramos_marcacion_' + f + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#parcial_color_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
                total_col += parcial;
            }
        }

        for (det = 0; det < ids_det_esp.length; det++) {
            parcial = 0;
            for (c = 0; c < col; c++) {
                cant = $('#parcial_color_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                if (cant != '') {
                    parcial += parseInt(cant);
                }
            }
            $('#parcial_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
        }

        if (!ini)
            $('.elemento_distribuir').hide();
    }

</script>

@php
    $det_ped = $pedido->detalles[$pos_det_ped];
@endphp
<form id="form-update_orden_semanal">
    <input type="hidden" id="listar_resumen_pedido" value="{{$listar_resumen_pedido}}">
    <table class="table-bordered" width="100%" style="margin-bottom: 10px">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Fecha Pedido
            </th>
            <th class="text-center" style="border-color: #9d9d9d" width="85px">
                <input type="date" id="fecha_pedido" name="fecha_pedido" value="{{$pedido->fecha_pedido}}" required width="100%"
                       class="form-control">
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <i class="fa fa-fw fa-plane"></i> Fecha Envío
            </th>
            <th class="text-center" style="border-color: #9d9d9d" width="85px">
                <input type="date" id="fecha_envio" name="fecha_envio"
                       value="{{count($pedido->envios) == 1 ? substr($pedido->envios[0]->fecha_envio, 0, 10) : $pedido->fecha_pedido}}" required
                       width="100%"
                       class="form-control">
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Cliente
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{$pedido->cliente->detalle()->nombre}}
            </th>
            @foreach($pedido->cliente->cliente_datoexportacion as $cli_dat_exp)
                @php
                    $detped_datexp = getDatosExportacion($det_ped->id_detalle_pedido, $cli_dat_exp->id_dato_exportacion);
                @endphp
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$cli_dat_exp->datos_exportacion->nombre}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="text" id="dato_exportacion_{{$cli_dat_exp->id_dato_exportacion}}" class="form-control"
                           value="{{$detped_datexp != '' ? $detped_datexp->valor : ''}}" minlength="1"
                           style="text-transform: uppercase">
                </th>

                <input type="hidden" class="id_dato_exportacion" value="{{$cli_dat_exp->id_dato_exportacion}}">
            @endforeach
        </tr>
    </table>

    <legend style="font-size: 1.1em; margin-bottom: 0">
        <strong>Detalles de la especificación</strong>
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
                <th class="text-center" style="border-color: #9d9d9d">
                    AGENCIA CARGA
                </th>
            </tr>
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                @php
                    $ramos_x_caja = 0;
                @endphp
                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                    @php
                        $ramos_x_caja += $det_esp->cantidad;
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
                            {{$det_esp->cantidad}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->empaque_p->nombre}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->tallos_x_ramo}}
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
                            <input type="number" id="precio_det_esp_{{$det_esp->id_detalle_especificacionempaque}}"
                                   style="width: 50px; background-color: #e9ecef" min="0"
                                   name="precio_det_esp_{{$det_esp->id_detalle_especificacionempaque}}"
                                   value="{{getPrecioByDetEsp($det_ped->precio, $det_esp->id_detalle_especificacionempaque)}}"
                                   class="text-center">
                        </td>
                        @if($pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                <input type="number" id="marcaciones_{{$esp_emp->id_especificacion_empaque}}" onkeypress="return isNumber(event)"
                                       name="marcaciones_{{$esp_emp->id_especificacion_empaque}}" readonly
                                       value="{{count($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'])}}"
                                       required min="1" style="border: none" class="text-center"
                                       width="50px">
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                <input type="number" id="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                       onkeypress="return isNumber(event)" readonly
                                       name="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                       value="{{count($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'])}}"
                                       required min="1" style="border: none" class="text-center"
                                       width="50px">
                            </td>
                        @endif
                        @if($pos_esp_emp == 0 && $pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d"
                                rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                <select name="id_agencia_carga" id="id_agencia_carga" required style="width: 100%; border: none">
                                    @foreach($pedido->cliente->cliente_agencia_carga as $item)
                                        <option value="{{$item->id_agencia_carga}}" {{$item->id_agencia_carga == $det_ped->id_agencia_carga ? 'selected' : ''}}>
                                            {{$item->agencia_carga->nombre}}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        @endif
                    </tr>
                    <input type="hidden"
                           id="ramos_x_caja_det_esp_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                           value="{{$det_esp->cantidad}}">
                    <input type="hidden" class="id_det_esp_{{$esp_emp->id_especificacion_empaque}}"
                           value="{{$det_esp->id_detalle_especificacionempaque}}">
                @endforeach
                <input type="hidden" id="ramos_x_caja_{{$esp_emp->id_especificacion_empaque}}" value="{{$ramos_x_caja}}">
                <input type="hidden" class="id_esp_emp" value="{{$esp_emp->id_especificacion_empaque}}">
            @endforeach
        </table>
    </div>
</form>

<div id="div_tabla_distribucion">
    @include('adminlte.gestion.postcocecha.pedidos_ventas.forms._tabla')
</div>

<div class="text-center" style="margin-top: 10px">
    <button type="button" class="btn btn-xs btn-default" onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped}}',false)">
        <i class="fa fa-fw fa-refresh"></i> Refrescar
    </button>
    <button type="button" class="btn btn-xs btn-success" onclick="update_orden_tinturada('{{csrf_token()}}')" id="btn_update_orden_tinturada">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>
    <button type="button" class="btn btn-xs btn-success" onclick="guardar_distribucion('{{csrf_token()}}')" id="btn_guardar_distribucion" style="display: none;">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>
    @if($have_prev)
        <button type="button" class="btn btn-xs btn-primary"
                onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped - 1}}', false)">
            <i class="fa fa-fw fa-long-arrow-left"></i> Anterior
        </button>
    @endif
    @if($have_next)
        <button type="button" class="btn btn-xs btn-primary"
                onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped + 1}}', false)">
            <i class="fa fa-fw fa-long-arrow-right"></i> Siguiente
        </button>
    @else
        <button type="button" class="btn btn-xs btn-default" onclick="terminar_edicion()">
            <i class="fa fa-fw fa-times"></i> Terminar
        </button>
    @endif
    <button type="button" class="btn btn-xs btn-danger" onclick="eliminar_detalle_pedido('{{$det_ped->id_detalle_pedido}}','{{csrf_token()}}')">
        <i class="fa fa-fw fa-trash"></i> Eliminar
    </button>
</div>

<input type="hidden" id="id_detalle_pedido" value="{{$det_ped->id_detalle_pedido}}">
<input type="hidden" id="id_pedido" value="{{$pedido->id_pedido}}">
<input type="hidden" id="pos_det_ped" value="{{$pos_det_ped}}">
<input type="hidden" id="have_next" value="{{$have_next ? 1 : 0}}">
<input type="hidden" id="have_prev" value="{{$have_prev ? 1 : 0}}">

<script>





</script>
