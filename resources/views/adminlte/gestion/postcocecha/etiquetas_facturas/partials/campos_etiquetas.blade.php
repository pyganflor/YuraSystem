@for ($x=1;$x<=$filas;$x++)
    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
        <td style="border-color: #9d9d9d;width: 100px;" class="text-center">
            <select id="empaque_{{$x}}" name="empaque_{{$x}}" style="width: 100%;border:none">
                <option value="">Seleccione</option>
                @foreach ($empaque as $e)
                    <option {{isset($pedido->etiqueta_factura->detalles[($x-1)]->empaque) ? ($pedido->etiqueta_factura->detalles[($x-1)]->empaque === $e->id_empaque ? "selected" : "" ) : "" }} value="{{$e->id_empaque}}">
                        {{explode("|",$e->nombre)[0]}}
                    </option>
                @endforeach
            </select>
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="number"  min="1" class="cajas" id="cajas_{{$x}}" name="cajas_{{$x}}"
                   value="{{isset($pedido->etiqueta_factura->detalles[($x-1)]->cantidad) ? $pedido->etiqueta_factura->detalles[($x-1)]->cantidad : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
        <td style="border-color: #9d9d9d;width: 400px;" class="text-center">
            <div class="input-group-btn" id="btn_presentaciones_{{$x}}">
                <button type="button" class="btn btn-dafault dropdown-toggle bg-default"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%">
                    <i class="fa fa-leaf"></i> Presentaciones
                    <span id="span_presentaciones_{{$x}}">
                        @php
                            $presentaciones  ="";
                            if(isset($pedido->etiqueta_factura->detalles[($x-1)])){
                                $id_det_esp_emp = explode("|",$pedido->etiqueta_factura->detalles[($x-1)]->id_detalle_especificacion_empaque);
                                foreach($id_det_esp_emp as $id){
                                    $det_esp_emp = getDetalleEspecificacionEmpaque($id);
                                    $variedad = getVariedad($det_esp_emp->id_variedad);
                                     $clasificacionRamo = getClasificacionRamo($det_esp_emp->id_clasificacion_ramo);
                                    if($det_esp_emp->longitud_ramo != null){
                                        foreach (getUnidadesMedida($det_esp_emp->id_unidad_medida) as $umLongitud)
                                            if($umLongitud->tipo == "L"){
                                                $umL = $umLongitud->siglas;
                                            }else{
                                                $umL ="";
                                            }
                                            $longitudRamo = $det_esp_emp->longitud_ramo != "" ? $det_esp_emp->longitud_ramo : "";
                                        foreach (getUnidadesMedida($clasificacionRamo->id_unidad_medida) as $umPeso)
                                            $umPeso->tipo == "P" ? $umPeso = $umPeso->siglas : $umPeso ="";
                                    }
                                    $det_ped_cantidad = 0;
                                    foreach ($pedido->detalles as $det_ped)
                                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                                            foreach ($esp_emp->detalles as $z=> $det_esp_emp)
                                                if($det_esp_emp->id_detalle_especificacionempaque == $id){
                                                    $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                                    $det_ped_cantidad = $det_ped->cantidad;
                                                    $det_esp_emp_cantidad = isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad;
                                                    break;
                                                }
                                    $presentaciones .= (substr($variedad->planta->nombre,0,3)." ". $variedad->siglas . " " . $clasificacionRamo->nombre.$umPeso. " ". $longitudRamo.$umL." Ramos por caja: ") ." ".($det_esp_emp_cantidad * $det_esp_emp->especificacion_empaque->cantidad)." <br />";
                                }
                            }
                        @endphp
                        @if(isset($pedido->etiqueta_factura->detalles[($x-1)]))
                            {!!"<br />". substr($presentaciones,0,-6) !!}
                        @endif
                    </span>
                    <span class="caret"></span>
                </button>
                <input type="hidden" id="ids_det_esp_emp_{{$x}}" name="ids_det_esp_emp_{{$x}}">
                <ul class="dropdown-menu" style="width: 100%;">
                    <li>
                        <table style="width: 100%">
                            @php $y = 0;  @endphp
                            @foreach ($pedido->detalles as $det_ped)
                                @foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                                    @foreach ($esp_emp->detalles as $z=> $det_esp_emp)
                                        @php
                                            $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
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
                                                    {{ substr($variedad->planta->nombre,0,3)." ". $variedad->siglas . " " . $clasificacionRamo->nombre.$umPeso. " ". $longitudRamo.$umL." Ramos por caja: " }} {{(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad}}
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
            <input type="text" id="siglas_{{$x}}" name="siglas_{{$x}}"
                   value="{{isset($pedido->etiqueta_factura->detalles[($x-1)]->siglas) ? $pedido->etiqueta_factura->detalles[($x-1)]->siglas : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="et_inicial_{{$x}}" name="et_inicial_{{$x}}"
                   value="{{isset($pedido->etiqueta_factura->detalles[($x-1)]->et_inicial) ? $pedido->etiqueta_factura->detalles[($x-1)]->et_inicial : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="et_final_{{$x}}" name="et_final_{{$x}}"
                   value="{{isset($pedido->etiqueta_factura->detalles[($x-1)]->et_final) ? $pedido->etiqueta_factura->detalles[($x-1)]->et_final : ""}}"
                   style="width: 100%;border:none;text-align: center">
        </td>
    </tr>
@endfor
