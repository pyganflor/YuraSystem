{{-- COLORES SEMAFOROS --}}
@php
    $color_1 = $variedad == '' ? getColorByIndicador('D9') : getColorByIndicadorVariedad('D9', $variedad->id_variedad);   //  venta_m2_anno_mensual
    $color_1_1 = $variedad == '' ? getColorByIndicador('D10') : getColorByIndicadorVariedad('D10', $variedad->id_variedad);    //  venta_m2_anno_anual
    $color_2 = $variedad == '' ? getColorByIndicador('DA1') : getColorByIndicadorVariedad('DA1', $variedad->id_variedad);  //  ciclo
    $color_3 = $variedad == '' ? getColorByIndicador('D1') : getColorByIndicadorVariedad('D1', $variedad->id_variedad);   //  calibre
    $color_4 = $variedad == '' ? getColorByIndicador('D3') : getColorByIndicadorVariedad('D3', $variedad->id_variedad);   //  precio_x_ramo
    $color_5 = $variedad == '' ? getColorByIndicador('D12') : getColorByIndicadorVariedad('D12', $variedad->id_variedad);   //  tallos_m2
    $color_6 = $variedad == '' ? getColorByIndicador('D8') : getColorByIndicadorVariedad('D8', $variedad->id_variedad);   //  ramos_m2_anno
    $color_7 = $variedad == '' ? getColorByIndicador('D14') : getColorByIndicadorVariedad('D14', $variedad->id_variedad);   //  precio_x_tallo
    $color_8 = getColorByIndicador('C3');   //  costos_campo_semana
    $color_9 = getColorByIndicador('C4');   //  costos_cosecha_x_tallo
    $color_10 = getColorByIndicador('C5');   //  costos_postcosecha_x_tallo
    $color_11 = getColorByIndicador('C6');   //  costos_total_x_tallo
    $color_12 = getColorByIndicador('C9');   //  costos_m2_mensual
    $color_13 = getColorByIndicador('C10');   //  costos_m2_anual
    $color_14 = $variedad == '' ? getColorByIndicador('R1') : getColorByIndicadorVariedad('R1', $variedad->id_variedad);   //  rentabilidad_m2_mensual
    $color_15 = getColorByIndicador('R2');   //  rentabilidad_m2_anual
@endphp
<table style="width: 100%;" align="center" class="table-borsdered">
    <tr>
        @for($i = 1; $i <= 16; $i++)
            <td style="vertical-align: inherit; width: 6.25%"></td>
        @endfor
    </tr>
    <tr>
        <td colspan="6">
            <select name="filtro_variedad" id="filtro_variedad" class="pull-left select-yura_default" onchange="select_filtro_variedad()"
                    style="margin-top: 0; width: 126px; height: 31px;">
                <option value="" id="option_acumulado_var">Acumulado</option>
                @foreach(getVariedades() as $var)
                    <option value="{{$var->id_variedad}}" {{$variedad->id_variedad == $var->id_variedad ? 'selected' : ''}}>{{$var->siglas}}</option>
                @endforeach
            </select>
        </td>
        <td colspan="4" class="text-center">
            <div style="" class="td-org">
                <div class="row">
                    <div class="col-md-12 text-center" style="margin-top: 10px">
                        <strong>Rentabilidad / m<sup>2</sup> / año</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <img src="{{url('images/negocio.png')}}" alt="$" class="icon_td-org" aria-hidden="true">
                        <ul class="list-unstyled text-center" style="margin-top: 5px">
                            <li>
                                <strong style="color:{{$color_14}}">
                                    $
                                    <span id="span_rentabilidad_m2_mensual">{{number_format($rentabilidad_m2_mensual, 2)}}</span>
                                    <sup>(4 meses)</sup>
                                </strong>
                            </li>
                            <li>
                                <strong style="color:{{$color_15}}">
                                    $
                                    <span id="span_rentabilidad_m2_mensual">{{number_format($rentabilidad_m2_anual, 2)}}</span>
                                    <sup>(1 año)</sup>
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-xs btn-block btn-yura_default"
                                onclick="mostrar_indicadores_claves(4, '{{$variedad->id_variedad}}')">
                            Rentabilidad/m<sup>2</sup>/año
                        </button>
                    </div>
                </div>
            </div>
        </td>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <td colspan="4" style="border-bottom: 1px solid #00B388"></td>
        <td colspan="4" style="border-left: 1px solid #00B388; height: 15px"></td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <td colspan="8" style="border-left: 1px solid #00B388; height: 15px"></td>
        <td colspan="4" style="border-left: 1px solid #00B388"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="4" class="text-center">
            <div style="" class="td-org">
                <div class="row">
                    <div class="col-md-12 text-center" style="margin-top: 10px">
                        <strong>Ventas / m<sup>2</sup> / año</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <img src="{{url('images/devaluacion.png')}}" alt="$" class="icon_td-org" aria-hidden="true">
                        <ul class="list-unstyled text-center" style="margin-top: 5px">
                            <li>
                                <strong style="color:{{$color_1}}">
                                    $
                                    <span id="span_rentabilidad_m2_mensual">{{number_format($venta_m2_anno_mensual, 2)}}</span>
                                    <sup>(4 meses)</sup>
                                </strong>
                            </li>
                            <li>
                                <strong style="color:{{$color_1_1}}">
                                    $
                                    <span id="span_rentabilidad_m2_mensual">{{number_format($venta_m2_anno_anual, 2)}}</span>
                                    <sup>(1 año)</sup>
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-xs btn-block btn-yura_default"
                                onclick="mostrar_indicadores_claves(0, '{{$variedad->id_variedad}}')">
                            Ventas/m<sup>2</sup>/año
                        </button>
                    </div>
                </div>
            </div>
        </td>
        <td colspan="4"></td>
        <td colspan="4">
            <div style="" class="td-org">
                <div class="row">
                    <div class="col-md-12 text-center" style="margin-top: 10px">
                        <strong>Costos / m<sup>2</sup> / año</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <img src="{{url('images/costos.png')}}" alt="$" class="icon_td-org" aria-hidden="true">
                        <ul class="list-unstyled text-center" style="margin-top: 5px">
                            <li>
                                <strong style="color:{{$color_12}}">
                                    $
                                    <span id="span_rentabilidad_m2_mensual">{{number_format($costos_m2_mensual, 2)}}</span>
                                    <sup>(4 meses)</sup>
                                </strong>
                            </li>
                            <li>
                                <strong style="color:{{$color_13}}">
                                    $
                                    <span id="span_rentabilidad_m2_mensual">{{number_format($costos_m2_anual, 2)}}</span>
                                    <sup>(1 año)</sup>
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-xs btn-block btn-yura_default"
                                onclick="mostrar_indicadores_claves(3)">
                            Costos/m<sup>2</sup>/año
                        </button>
                    </div>
                </div>
            </div>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="2" style="border-bottom: 1px solid #00B388"></td>
        <td colspan="6" style="border-left: 1px solid #00B388; height: 15px"></td>
        <td colspan="2" style="border-bottom: 1px solid #00B388; height: 15px"></td>
        <td colspan="4" style="border-left: 1px solid #00B388; height: 15px"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="4" style="border-left: 1px solid #00B388; height: 15px"></td>
        <td colspan="4" style="border-left: 1px solid #00B388; height: 15px"></td>
        <td colspan="4" style="border-left: 1px solid #00B388; height: 15px"></td>
        <td colspan="2" style="border-left: 1px solid #00B388; height: 15px"></td>
    </tr>
    <tr>
        <td colspan="3" class="text-center">
            <div style="" class="td-org">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 10px">
                        <ul class="list-unstyled text-center">
                            <li>
                                <strong style="color:{{$color_14}}">
                                    <strong style="color: {{$color_4}}">
                                        <small>Precio: $</small>
                                        <span id="span_precio_x_ramo"
                                              title="Ramo">{{number_format($precio_x_ramo, 2)}}</span>
                                    </strong>
                                    -
                                    <span title="Tallo" style="color:{{$color_7}}"><small>$</small>{{$precio_x_tallo}}</span>
                                </strong>
                            </li>
                            <li>
                                <strong style="color:{{$color_6}}">
                                    <small>Productividad:</small>
                                    <span id="span_ramos_m2_anno">{{number_format($ramos_m2_anno, 2)}}</span></strong>
                            </li>
                            <li>
                                <strong style="color: {{$color_3}}">
                                    <small>Calibre:</small>
                                    <span id="span_calibre">{{$calibre}}</span></strong>
                            </li>
                            <li>
                                <strong style="color: {{$color_5}}">
                                    <small>Tallos x m<sup>2</sup>:</small>
                                    <span id="span_tallos_m2">{{number_format($tallos_m2, 2)}}</span></strong>
                            </li>
                            <li>
                                <strong style="color: {{$color_2}}">
                                    <small>Ciclo:</small>
                                    <span id="span_ciclo">{{number_format($ciclo, 2)}}</span></strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-xs btn-block btn-yura_default"
                                onclick="mostrar_indicadores_claves(1, '{{$variedad->id_variedad}}')">
                            Indicadores claves
                        </button>
                    </div>
                </div>
            </div>
        </td>
        <td></td>
        <td colspan="3" class="text-center">
            <div style="" class="td-org">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 10px">
                        <ul class="list-unstyled text-center">
                            <li>
                                <strong>
                                    <small>Área:</small>
                                    <span id="span_area_produccion">{{number_format(round($area_produccion / 10000, 2), 2)}}</span></strong>
                            </li>
                            <li>
                                <strong>
                                    <small>Venta:</small>
                                    $<span id="span_valor">{{number_format($valor, 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Tallos cosechados">
                                    <small>T/cosechados:</small>
                                    <span id="span_tallos_cosechados">{{number_format($tallos_cosechados)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Tallos clasificados" onclick="detallar_indicador({{'"D2"'}})"
                                        style="color: #333333" class="mouse-hand">
                                    <small>T/clasificados:</small>
                                    <span id="span_tallos">{{number_format($tallos)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Cajas exportadas">
                                    <small>Cajas exp:</small>{{number_format($cajas_exportadas, 2)}}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-xs btn-block btn-yura_default" disabled>
                            Datos importantes
                        </button>
                    </div>
                </div>
            </div>
        </td>
        <td colspan="1"></td>
        <td colspan="4" class="text-center">
            <div style="" class="td-org">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 10px">
                        <ul class="list-unstyled text-center">
                            <li>
                                <strong title="Costo x planta" style="color:{{$color_8}}">
                                    <small>Costo x planta:</small>
                                    <span id="span_costos_x_planta">$</span></strong>
                            </li>
                            <li>
                                <strong title="Campo/ha/Semana" style="color:{{$color_8}}">
                                    <small>Campo/<sup>ha</sup>/Semana:</small>
                                    <span id="span_costos_campo_semana">${{number_format(explode('|', $costos_campo_semana)[0] , 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Cosecha x Tallo" style="color:{{$color_9}}">
                                    <small>Cosecha x Tallo:</small>
                                    <span id="span_costos_cosecha_tallo">¢{{number_format($costos_cosecha_x_tallo, 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Postcosecha x Tallo" style="color:{{$color_10}}">
                                    <small>Postcosecha x Tallo:</small>
                                    <span id="span_costos_postcosecha_tallo">¢{{number_format($costos_postcosecha_x_tallo, 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Total x Tallo" style="color:{{$color_11}}">
                                    <small>Total x Tallo:</small>
                                    <span id="span_costos_total_tallo">¢{{number_format($costos_total_x_tallo, 2)}}</span></strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-xs btn-block btn-yura_default"
                                onclick="mostrar_indicadores_claves(2)">
                            Indicadores claves
                        </button>
                    </div>
                </div>
            </div>
        </td>
        <td></td>
        <td colspan="3" class="text-center">
            <div style="" class="td-org">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 10px">
                        <ul class="list-unstyled text-center">
                            <li>
                                <strong title="Total">
                                    <small>Total:</small>
                                    <span id="span_costos_total">${{number_format(explode(':', $costos_mano_obra)[1] + explode(':', $costos_insumos)[1] + explode(':', $costos_fijos)[1] + explode(':', $costos_regalias)[1] , 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Mano de Obra, Semana: {{explode(':', $costos_mano_obra)[0]}}">
                                    <small>MO:</small>
                                    <span id="span_costos_mano_obra">${{number_format(explode(':', $costos_mano_obra)[1] , 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="MP, Semana: {{explode(':', $costos_insumos)[0]}}">
                                    <small>MP:</small>
                                    <span id="span_costos_insumos">${{number_format(explode(':', $costos_insumos)[1] , 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Fijos, Semana: {{explode(':', $costos_fijos)[0]}}">
                                    <small>Fijos:</small>
                                    <span id="span_costos_fijos">${{number_format(explode(':', $costos_fijos)[1] , 2)}}</span></strong>
                            </li>
                            <li>
                                <strong title="Regalías, Semana: {{explode(':', $costos_regalias)[0]}}">
                                    <small>Regalías:</small>
                                    <span id="span_costos_regalias">${{number_format(explode(':', $costos_regalias)[1] , 2)}}</span></strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-xs btn-block btn-yura_default" disabled>
                            Datos importantes
                        </button>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

<div id="div_indicadores_claves" style="margin-top: 10px"></div>