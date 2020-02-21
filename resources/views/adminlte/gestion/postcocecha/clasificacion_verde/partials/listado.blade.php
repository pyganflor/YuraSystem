<div id="table_clasificaciones">
    @if($verde != '')
        <div style="overflow-x: scroll; width: 100%">
            <table width="100%" class="table table-responsive table-bordered sombra_estandar" style="font-size: 0.8em; border-color: #9d9d9d"
                   id="table_content_clasificaciones_{{$verde->id_clasificacion_verde}}">
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
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        OPCIONES
                    </th>
                </tr>
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    id="row_clasificaciones_{{$verde->id_clasificacion_verde}}">
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
                        <a href="javascript:void(0)" onclick="ver_rendimiento('{{$verde->id_clasificacion_verde}}')" title="Ver rendimiento">
                            {{$verde->getRendimiento()}}
                        </a>
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" class="btn btn-default btn-xs" title="Detalles"
                           onclick="ver_clasificacion('{{$verde->id_clasificacion_verde}}')"
                           id="btn_view_clasificacion_{{$verde->id_clasificacion_verde}}">
                            <i class="fa fa-fw fa-eye" style="color: black"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td colspan="11" style="border-color: #9d9d9d; padding: 0;">
                        <div style="overflow-y: scroll; height: 250px">
                            <table width="100%" class="table table-responsive table-bordered"
                                   style="border: 1px solid #9d9d9d; margin-bottom: 0">
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
                                        <th style="border-color: #9d9d9d; margin-bottom: 0" class="text-center">
                                            {{$variedad->planta->nombre}} - {{$variedad->siglas}} -
                                            <em>
                                                {{$verde->getPorcentajeByVariedad($variedad->id_variedad)}}
                                                %
                                            </em>
                                            <br>
                                            @if(count($verde->lotes_reByVariedad($variedad->id_variedad)) > 0)
                                                <button type="button" class="btn btn-xs btn-primary" style="margin-top: 10px; color: #0a0a0a"
                                                        title="Ver lotes"
                                                        onclick="ver_lotes('{{$variedad->id_variedad}}', '{{$verde->id_clasificacion_verde}}')">
                                                    <i class="fa fa-fw fa-sitemap"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-xs btn-warning" style="margin-top: 10px"
                                                        title="Destinar lotes"
                                                        onclick="destinar_lotes('{{$variedad->id_variedad}}', '{{$verde->id_clasificacion_verde}}')">
                                                    <i class="fa fa-fw fa-exchange"></i>
                                                </button>
                                                <script>
                                                    $('#table_content_clasificaciones_{{$verde->id_clasificacion_verde}}').removeClass('sombra_estandar');
                                                    $('#table_content_clasificaciones_{{$verde->id_clasificacion_verde}}').addClass('sombra_roja');
                                                </script>
                                            @endif
                                        </th>
                                        <td style="border-color: #9d9d9d; padding: 0; margin-bottom: 0" class="text-center">
                                            <table width="100%" class="table table-responsive table-bordered"
                                                   style="border: 1px solid #9d9d9d; margin-bottom: 0">
                                                <tr>
                                                    @foreach($verde->unitarias() as $unitaria)
                                                        <th style="border-color: #9d9d9d; background-color: {{explode('|',$unitaria->color)[0]}}; color: {{explode('|',$unitaria->color)[1]}}"
                                                            class="text-center"
                                                            width="{{100 / count($verde->unitarias())}}%">
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
                                            <br>
                                            <span class="badge" title="Desecho" style="background-color: #ce8483">
                                            {{$verde->desechoByVariedad($variedad->id_variedad)}}%
                                        </span>
                                        </th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado resultados para la fecha</div>
    @endif
</div>