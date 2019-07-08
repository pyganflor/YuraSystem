@for ($x=1;$x<=$filas;$x++)
    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
        <td style="border-color: #9d9d9d;width: 100px;" class="text-center">
            <select id="empaque_{{$x}}" name="empaque_{{$x}}" style="width: 100%;border:none">
                <option value="">Seleccione</option>
                @foreach ($empaque as $e)
                    <option {{isset($comprobante->etiqueta_factura->detalles[($x-1)]->empaque) ? ($comprobante->etiqueta_factura->detalles[($x-1)]->empaque === $e->id_empaque ? "selected" : "" ) : "" }} value="{{$e->id_empaque}}">
                        {{explode("|",$e->nombre)[0]}}
                    </option>
                @endforeach
            </select>
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="number"  min="1" class="cajas" id="cajas_{{$x}}" name="cajas_{{$x}}" value="{{isset($comprobante->etiqueta_factura->detalles[($x-1)]->cantidad) ? $comprobante->etiqueta_factura->detalles[($x-1)]->cantidad : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
        <td style="border-color: #9d9d9d;width: 400px;" class="text-center">
            <div class="input-group-btn" id="btn_presentaciones_{{$x}}">
                <button type="button" class="btn btn-dafault dropdown-toggle bg-default"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%">
                    <i class="fa fa-leaf"></i> Presentaciones
                    <span id="span_presentaciones_{{$x}}"></span>
                    <span class="caret"></span>
                </button>
                <input type="hidden" id="ids_det_esp_emp_{{$x}}" name="ids_det_esp_emp_{{$x}}">
                <ul class="dropdown-menu" style="width: 100%;">
                    <li>
                        <table style="width: 100%">
                            @php $y = 0; @endphp
                            @foreach ($comprobante->envio->pedido->detalles as $det_ped)
                                @foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                                    @foreach ($esp_emp->detalles as $z=> $det_esp_emp)
                                        @php
                                            $clasificacionRamo = getClasificacionRamo($det_esp_emp->id_clasificacion_ramo);
                                            $variedad = getVariedad($det_esp_emp->id_variedad);
                                            if($det_esp_emp->longitud_ramo != null){
                                            foreach (getUnidadesMedida($det_esp_emp->id_unidad_medida) as $umLongitud)
                                                if($umLongitud->tipo == "L")
                                                    $umL = $umLongitud->siglas;
                                                }else{
                                                    $umL ="";
                                                }
                                            $longitudRamo = $det_esp_emp->longitud_ramo != "" ? $det_esp_emp->longitud_ramo : "";
                                            foreach (getUnidadesMedida($clasificacionRamo->id_unidad_medida) as $umPeso)
                                                $umPeso->tipo == "P" ? $umPeso = $umPeso->siglas : $umPeso ="";
                                         @endphp
                                        <tr>
                                            @if($z == 0)
                                                <td style="border: 1px solid black;text-align: center;vertical-align: middle"
                                                        rowspan="{{count($esp_emp->detalles)}}">
                                                    <input type="checkbox" id="esp_{{$x}}_{{$y+1}}" name="esp_{{$x}}_{{$y+1}}" onclick="select_check_etiqueta_factura(this)">
                                                </td>
                                                @php $y++; @endphp
                                            @endif
                                            <td style="border: 1px solid black"  class="td_{{$x}}_{{$y}}">
                                                <label style="font-weight: 600" id="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                                    {{ substr($variedad->planta->nombre,0,3)." ". $variedad->siglas . " " . $clasificacionRamo->nombre.$umPeso. " ". $longitudRamo.$umL." Ramos por caja: " }}    {{$det_esp_emp->cantidad * $esp_emp->cantidad * $det_ped->cantidad}}
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </table>
                    </li>
                </ul>
            </div>
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="siglas_{{$x}}" name="siglas_{{$x}}" value="{{isset($comprobante->etiqueta_factura->detalles[($x-1)]->siglas) ? $comprobante->etiqueta_factura->detalles[($x-1)]->siglas : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="et_inicial_{{$x}}" name="et_inicial_{{$x}}" value="{{isset($comprobante->etiqueta_factura->detalles[($x-1)]->et_inicial) ? $comprobante->etiqueta_factura->detalles[($x-1)]->et_inicial : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="et_final_{{$x}}" name="et_final_{{$x}}" value="{{isset($comprobante->etiqueta_factura->detalles[($x-1)]->et_final) ? $comprobante->etiqueta_factura->detalles[($x-1)]->et_final : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
    </tr>
@endfor
