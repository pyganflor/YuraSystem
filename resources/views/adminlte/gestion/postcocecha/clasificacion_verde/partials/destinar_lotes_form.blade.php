<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Producción de <em class="badge">{{$clasificacion->tallos_x_variedad($variedad->id_variedad)}}</em> tallos de
            <strong>{{$variedad->planta->nombre}} - {{$variedad->nombre}}</strong>
            <em class="badge">{{$clasificacion->fecha_ingreso}}</em>
        </h3>
    </div>
    <input type="hidden" id="id_variedad_{{$variedad->id_variedad}}" value="{{$variedad->id_variedad}}" class="id_variedad_form">
    <input type="hidden" id="fecha_ingreso_{{$variedad->id_variedad}}" value="{{$clasificacion->fecha_ingreso}}">
    <div class="box-body">
        <form id="form-add_lote_re_{{$variedad->id_variedad}}">
            <table class="table table-bordered table-responsive sombra_estandar" width="100%" style="font-size: 0.8em">
                <tr>
                    <th style="border-color: #9d9d9d" colspan="5"
                        class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        Clasificaciones
                    </th>
                    <th style="border-color: #9d9d9d"
                        class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        Disponibles
                    </th>
                    <th style="border-color: #9d9d9d"
                        class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        Stock
                    </th>
                </tr>
                @foreach($clasificacion->getUnitariasFechaByVariedad($variedad->id_variedad) as $pos => $unitaria)
                    @php
                        $getTallosByvariedadUnitariaFecha = $clasificacion->getTallosByvariedadUnitariaFecha($variedad->id_variedad, $unitaria['unitaria'], $unitaria['fecha']);
                    @endphp
                    @if($getTallosByvariedadUnitariaFecha > 0)
                        <tr>
                            <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center" width="7%">
                                {{$unitaria['fecha']}}
                            </th>
                            <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center" width="12%">
                                <span class="badge">
                                    {{$getTallosByvariedadUnitariaFecha}}
                                </span>
                                {{explode('|',getUnitaria($unitaria['unitaria'])->nombre)[0]}}{{getUnitaria($unitaria['unitaria'])->unidad_medida->siglas}}
                            </th>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center" width="12%">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Días</span>
                                    <input type="number" name="dias_{{$variedad->id_variedad}}_{{$pos}}"
                                           min="{{$variedad->minimo_apertura}}" max="{{$variedad->maximo_apertura}}"
                                           id="dias_{{$variedad->id_variedad}}_{{$pos}}"
                                           class="form-control" required value="{{$variedad->estandar_apertura}}"
                                           onkeypress="return isNumber(event)"
                                            {{--onchange="calcular_stock('{{$unitaria['unitaria']}}')"--}}>
                                </div>
                            </td>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Apertura</span>
                                    <input type="number" name="apertura_{{$variedad->id_variedad}}_{{$pos}}"
                                           min="0" max="{{$getTallosByvariedadUnitariaFecha}}"
                                           id="apertura_{{$variedad->id_variedad}}_{{$pos}}" class="form-control" required
                                           value="{{$getTallosByvariedadUnitariaFecha}}" onkeypress="return isNumber(event)">
                                </div>
                            </td>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Guarde</span>
                                    <input type="number" name="guarde_{{$variedad->id_variedad}}_{{$pos}}"
                                           min="0" max="{{$getTallosByvariedadUnitariaFecha}}"
                                           id="guarde_{{$variedad->id_variedad}}_{{$pos}}" class="form-control" required
                                           value="0" onkeypress="return isNumber(event)">
                                </div>
                            </td>
                            {{--<td style="border-color: #9d9d9d; padding: 0" class="text-center" width="20%">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                <span class="input-group-addon" style="background-color: #357ca5; color: white"
                                      id="fecha_disponible_{{$unitaria['unitaria']}}_{{$variedad->id_variedad}}_{{$pos}}">
                                </span>
                                    <input type="text" class="form-control"
                                           id="disponible_{{$unitaria['unitaria']}}_{{$variedad->id_variedad}}_{{$pos}}" readonly
                                           name="disponible_{{$unitaria['unitaria']}}_{{$variedad->id_variedad}}_{{$pos}}"
                                           value="{{getStockToFecha($variedad->id_variedad, $unitaria['unitaria'], $unitaria['fecha'], $variedad->estandar_apertura) +
                                       $clasificacion->getTallosByvariedadUnitariaFecha($variedad->id_variedad, $unitaria['unitaria'], $unitaria['fecha'])}}">
                                </div>
                            </td>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center" width="20%">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #357ca5; color: white">Stock</span>
                                    <input type="text" class="form-control"
                                           id="stock_{{$unitaria['unitaria']}}_{{$variedad->id_variedad}}_{{$pos}}" readonly
                                           name="stock_{{$unitaria['unitaria']}}_{{$variedad->id_variedad}}_{{$pos}}"
                                           value="{{getStock($variedad->id_variedad, $unitaria['unitaria'], $variedad->estandar_apertura) +
                                       $clasificacion->getTallosByvariedadUnitariaFecha($variedad->id_variedad, $unitaria['unitaria'], $unitaria['fecha'])}}">
                                </div>
                            </td>--}}
                        </tr>
                        <input type="hidden" id="fecha_unitaria_{{$variedad->id_variedad}}_{{$pos}}"
                               value="{{$unitaria['fecha']}}">
                        <input type="hidden" id="id_clasificacion_unitaria_{{$variedad->id_variedad}}_{{$pos}}"
                               value="{{$unitaria['unitaria']}}">
                        <input type="hidden" class="pos_lotes_re_{{$variedad->id_variedad}}" value="{{$pos}}">
                        <input type="hidden" id="tallos_x_unitaria_fecha_{{$variedad->id_variedad}}_{{$pos}}"
                               value="{{$getTallosByvariedadUnitariaFecha}}">
                        <script>
                            //calcular_stock('{{$unitaria['unitaria']}}');
                        </script>
                    @endif
                @endforeach
            </table>
        </form>
        <div class="text-center" style="margin-top: 10px">
            <button type="button" class="btn btn-sm btn-success" onclick="store_lote_re('{{$variedad->id_variedad}}')">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        </div>
    </div>
</div>
<input type="hidden" id="id_clasificacion_verde" value="{{$clasificacion->id_clasificacion_verde}}">