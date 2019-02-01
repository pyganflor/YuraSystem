<div id="table_clasificaciones">
    @if(count($listado)>0)
        @foreach($listado as $item)
            <table width="100%" class="table table-responsive table-bordered sombra_estandar" style="font-size: 0.8em; border-color: #9d9d9d"
                   id="table_content_clasificaciones_{{$item->id_clasificacion_verde}}">
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
                    id="row_clasificaciones_{{$item->id_clasificacion_verde}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->semana}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{substr($item->fecha_ingreso,0,16)}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{getClasificacionVerde($item->id_clasificacion_verde)->total_tallos_recepcion()}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{getClasificacionVerde($item->id_clasificacion_verde)->total_tallos()}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{getClasificacionVerde($item->id_clasificacion_verde)->desecho()}}%
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{getClasificacionVerde($item->id_clasificacion_verde)->personal}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" onclick="ver_rendimiento('{{$item->id_clasificacion_verde}}')" title="Ver rendimiento">
                            {{getClasificacionVerde($item->id_clasificacion_verde)->getRendimiento()}}
                        </a>
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" class="btn btn-default btn-xs" title="Detalles"
                           onclick="ver_clasificacion('{{$item->id_clasificacion_verde}}')"
                           id="btn_view_clasificacion_{{$item->id_clasificacion_verde}}">
                            <i class="fa fa-fw fa-eye" style="color: black"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="border-color: #9d9d9d; padding: 0;">
                        <div style="overflow-y: scroll; height: 170px">
                            <table width="100%" class="table table-responsive table-bordered"
                                   style="border: 1px solid #9d9d9d; margin-bottom: 0">
                                <tr>
                                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                        Variedades
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                        {{getClasificacionVerde($item->id_clasificacion_verde)->total_ramos()}} Ramos /
                                        {{getClasificacionVerde($item->id_clasificacion_verde)->total_tallos()}} Tallos
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                        Desechos
                                    </th>
                                </tr>
                                @foreach(getClasificacionVerde($item->id_clasificacion_verde)->variedades() as $variedad)
                                    <tr>
                                        <th style="border-color: #9d9d9d" class="text-center">
                                            {{$variedad->planta->nombre}} - {{$variedad->siglas}}
                                            <br>
                                            @if(count(getClasificacionVerde($item->id_clasificacion_verde)->lotes_reByVariedad($variedad->id_variedad)) > 0)
                                                <button type="button" class="btn btn-xs btn-primary" style="margin-top: 10px; color: #0a0a0a"
                                                        title="Ver lotes"
                                                        onclick="ver_lotes('{{$variedad->id_variedad}}', '{{$item->id_clasificacion_verde}}')">
                                                    <i class="fa fa-fw fa-sitemap"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-xs btn-warning" style="margin-top: 10px"
                                                        title="Destinar lotes"
                                                        onclick="destinar_lotes('{{$variedad->id_variedad}}', '{{$item->id_clasificacion_verde}}')">
                                                    <i class="fa fa-fw fa-exchange"></i>
                                                </button>
                                                <script>
                                                    $('#table_content_clasificaciones_{{$item->id_clasificacion_verde}}').removeClass('sombra_estandar');
                                                    $('#table_content_clasificaciones_{{$item->id_clasificacion_verde}}').addClass('sombra_roja');
                                                </script>
                                            @endif
                                        </th>
                                        <td style="border-color: #9d9d9d; padding: 0" class="text-center">
                                            <table width="100%" class="table table-responsive table-bordered"
                                                   style="border: 1px solid #9d9d9d; margin-bottom: 0">
                                                <tr>
                                                    @foreach(getClasificacionVerde($item->id_clasificacion_verde)->unitarias() as $unitaria)
                                                        <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center"
                                                            width="{{100 / count(getClasificacionVerde($item->id_clasificacion_verde)->unitarias())}}%">
                                                            {{explode('|',$unitaria->nombre)[0]}}{{$unitaria->unidad_medida->siglas}}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    @foreach(getClasificacionVerde($item->id_clasificacion_verde)->unitarias() as $unitaria)
                                                        <td style="border-color: #9d9d9d;" class="text-center">
                                                        <span class="badge" title="Ramos ingresados">
                                                            {{getClasificacionVerde($item->id_clasificacion_verde)->getRamosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                                        </span>
                                                            <span class="badge" style="background-color: #357ca5" title="Tallos">
                                                            {{getClasificacionVerde($item->id_clasificacion_verde)->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                                        </span>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </td>
                                        <th style="border-color: #9d9d9d" class="text-center">
                                        <span class="badge" title="Tallos en recepción">
                                            {{getClasificacionVerde($item->id_clasificacion_verde)->total_tallos_recepcionByVariedad($variedad->id_variedad)}}
                                        </span>
                                            <span class="badge" title="Tallos clasificados" style="background-color: #357ca5">
                                            {{getClasificacionVerde($item->id_clasificacion_verde)->tallos_x_variedad($variedad->id_variedad)}}
                                        </span>
                                            <br>
                                            <br>
                                            <span class="badge" title="Desecho" style="background-color: #ce8483">
                                            {{round(100 - (
                                            (getClasificacionVerde($item->id_clasificacion_verde)->tallos_x_variedad($variedad->id_variedad) * 100) /
                                            getClasificacionVerde($item->id_clasificacion_verde)->total_tallos_recepcionByVariedad($variedad->id_variedad)),2)}}
                                                %
                                        </span>
                                        </th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        @endforeach
        <div id="pagination_listado_clasificaciones">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>