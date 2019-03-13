@if($cliente != '')
    @foreach($cliente->cliente_pedido_especificaciones as $cli_ped_esp)
        @if($cli_ped_esp->especificacion->tipo == 'N')
            <table width="100%" class="table-responsive table-bordered table-striped"
                   style="font-size: 0.9em; border-color: white; margin-top: 10px; border: 3px solid #9d9d9d"
                   id="table_especificacion_orden_semanal_{{$cli_ped_esp->id_especificacion}}">
                <thead>
                <tr style="background-color: #e9ecef">
                    <th class="text-center" style="border-color: #9d9d9d" width="75px">
                        CANTIDAD
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        VARIEDAD
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d" width="150px">
                        CALIBRE
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        CAJA
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d" width="75px">
                        RAMOS X CAJA
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        PRESENTACIÓN
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d" width="75px">
                        TALLOS X RAMO
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d" width="75px">
                        LONGITUD
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        U. MEDIDA
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d" width="85px">
                        PRECIO
                    </th>
                </tr>
                </thead>
                @foreach($cli_ped_esp->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                    @foreach($esp_emp->detalles as $pos_det_esp_emp => $det_esp_emp)
                        <tr onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')">
                            @if($pos_esp_emp == 0 && $pos_det_esp_emp == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{getCantidadDetallesByEspecificacion($cli_ped_esp->id_especificacion)}}">
                                    <input type="number" id="cant_piezas_esp_{{$cli_ped_esp->id_especificacion}}" style="width: 100%"
                                           name="cant_piezas_esp_{{$cli_ped_esp->id_especificacion}}" class="form-control">
                                </td>
                            @endif
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$det_esp_emp->variedad->nombre}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{explode('|',$esp_emp->empaque->nombre)[0]}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$det_esp_emp->cantidad}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$det_esp_emp->empaque_p->nombre}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$det_esp_emp->tallos_x_ramo}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$det_esp_emp->longitud_ramo}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                @if($det_esp_emp->id_unidad_medida != '')
                                    {{$det_esp_emp->unidad_medida->siglas}}
                                @endif
                            </td>
                            @if($pos_esp_emp == 0 && $pos_det_esp_emp == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{getCantidadDetallesByEspecificacion($cli_ped_esp->id_especificacion)}}">
                                    <input type="number" id="precio_esp_{{$cli_ped_esp->id_especificacion}}" style="width: 100%"
                                           name="precio_esp_{{$cli_ped_esp->id_especificacion}}" class="form-control">
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </table>

            <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d">
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; padding: 0" id="th_menu">
                        <input type="number" id="num_marcaciones_{{$cli_ped_esp->id_especificacion}}"
                               name="num_marcaciones_{{$cli_ped_esp->id_especificacion}}" onkeypress="return isNumber(event)"
                               placeholder="Marcaciones" min="1" class="text-center">
                        <input type="number" id="num_colores_{{$cli_ped_esp->id_especificacion}}"
                               name="num_colores_{{$cli_ped_esp->id_especificacion}}" onkeypress="return isNumber(event)" placeholder="Colores"
                               min="1" class="text-center">
                        <button type="button" class="btn btn-xs btn-primary"
                                onclick="construir_tabla_especificacion_orden_semanal('{{$cli_ped_esp->id_especificacion}}')"
                                style="margin-top: 0">
                            <i class="fa fa-fw fa-check"></i> Siguiente
                        </button>
                    </th>
                </tr>
            </table>

            <div style="width: 100%; overflow-x: scroll; display: none" id="div_tabla_distribucion_pedido">
                <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d; margin-top: 10px">
                    <tr>
                        <td style="border-color: #9d9d9d; padding: 0;" width="100%">
                            <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d"
                                   id="table_marcaciones_x_colores"></table>
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    @endforeach
@endif
