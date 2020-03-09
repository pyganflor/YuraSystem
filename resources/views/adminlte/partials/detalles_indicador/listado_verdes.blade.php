@if(count($verdes) > 0)
    <div style="overflow-x: scroll; width: 100%">
        <table width="100%" class="table table-striped table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    SEMANA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TALLOS RECPCIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TALLOS CLASIFICADOS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    RAMOS ESTANDAR
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CAJAS EQ.
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CALIBRE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    DESECHO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    PERSONAL
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    RENDIMIENTO
                </th>
            </tr>
            @foreach($verdes as $pos_verde => $verde)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    id="row_clasificaciones_{{$verde->id_clasificacion_verde}}" class="mouse-hand"
                    onclick="$('.tr_{{$verde->id_clasificacion_verde}}').toggleClass('hide')">
                    <td style="border-color: #9d9d9d" class="text-center">{{$verde->semana->codigo}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{substr($verde->fecha_ingreso,0,16)}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$verde->total_tallos_recepcion()}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$verde->total_tallos()}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$verde->getTotalRamosEstandar()}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2)}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @if($verde->getTotalRamosEstandar() > 0)
                            {{round($verde->total_tallos() /
                            $verde->getTotalRamosEstandar(),2)}}
                        @else
                            0
                        @endif
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$verde->desecho()}}%
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$verde->personal}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$verde->getRendimiento()}}
                    </td>
                </tr>
                <tr class="tr_{{$verde->id_clasificacion_verde}} hide">
                    <td colspan="10" style="border-color: #9d9d9d; padding: 0;">
                        <table width="100%" class="table-responsive table-bordered" style="margin-bottom: 0; border-bottom: 3px solid #9d9d9d">
                            <tr>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                    Variedades
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                    {{$verde->total_ramos()}} Ramos /
                                    {{$verde->total_tallos()}} Tallos
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                    Ramos estandar
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                    Cajas eq.
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                    Calibre
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                    Desechos
                                </th>
                            </tr>
                            @foreach($verde->variedades() as $variedad)
                                <tr>
                                    <th style="border-color: #9d9d9d; margin-bottom: 0; width: 150px" class="text-center">
                                        {{$variedad->planta->nombre}} - {{$variedad->siglas}}
                                        <em>
                                            {{$verde->getPorcentajeByVariedad($variedad->id_variedad)}} %
                                        </em>
                                    </th>
                                    <td style="border-color: #9d9d9d; padding: 0; margin-bottom: 0; width: 50%" class="text-center">
                                        <table width="100%" class="table-responsive table-bordered"
                                               style="border: 1px solid #9d9d9d; margin-bottom: 0">
                                            <tr>
                                                @foreach($verde->unitarias() as $unitaria)
                                                    <th style="border-color: #9d9d9d; background-color: {{explode('|',$unitaria->color)[0]}}; color: {{explode('|',$unitaria->color)[1]}}"
                                                        class="text-center" width="{{100 / count($verde->unitarias())}}%">
                                                        {{explode('|',$unitaria->nombre)[0]}}{{$unitaria->unidad_medida->siglas}} -
                                                        <em>
                                                            {{$verde->getPorcentajeUnitariaByVariedad($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                                            %
                                                        </em>
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($verde->unitarias() as $unitaria)
                                                    <td style="border-color: #9d9d9d;" class="text-center">
                                                            <span class="badge" title="Ramos ingresados">
                                                                {{$verde->getRamosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                                            </span>
                                                        <span class="badge" style="background-color: #357ca5" title="Tallos">
                                                                {{$verde->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                                            </span>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border-color: #9d9d9d; padding: 10px; margin-bottom: 0" class="text-center" width="10%">
                                        {{$verde->getTotalRamosEstandarByVariedad($variedad->id_variedad)}}
                                    </td>
                                    <td style="border-color: #9d9d9d; padding: 10px; margin-bottom: 0" class="text-center" width="10%">
                                        {{round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) /
                                        getConfiguracionEmpresa()->ramos_x_caja, 2)}}
                                    </td>
                                    <td style="border-color: #9d9d9d; padding: 10px; margin-bottom: 0" class="text-center" width="5%">
                                        {{$verde->calibreByVariedad($variedad->id_variedad)}}
                                    </td>
                                    <th style="border-color: #9d9d9d; margin-bottom: 0" class="text-center">
                                        <span class="badge" title="Tallos en recepción">
                                            {{$verde->total_tallos_recepcionByVariedad($variedad->id_variedad)}}
                                        </span>
                                        <span class="badge" title="Tallos clasificados" style="background-color: #357ca5">
                                            {{$verde->tallos_x_variedad($variedad->id_variedad)}}
                                        </span>
                                        <br>
                                        <span class="badge" title="Desecho" style="background-color: #ce8483">
                                            {{$verde->desechoByVariedad($variedad->id_variedad)}}%
                                        </span>
                                    </th>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@else
    <div class="alert alert-info text-center">No se han encontrado resultados para la fecha</div>
@endif
