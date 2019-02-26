<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Ingresos por variedad
        </h3>
    </div>
    <div class="box-body" style="overflow-x: scroll">
        <table width="100%" class="table table-responsive table-bordered"
               style="border: 1px solid #9d9d9d; margin-bottom: 0; font-size: 0.8em">
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Variedades
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$clasificacion->total_ramos()}} Ramos /
                    {{$clasificacion->total_tallos()}} Tallos
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
            @foreach($clasificacion->variedades() as $variedad)
                <tr>
                    <th style="border-color: #9d9d9d" class="text-center">
                        {{$variedad->planta->nombre}} - {{$variedad->siglas}} -
                        <em>
                            {{$clasificacion->getPorcentajeByVariedad($variedad->id_variedad)}}
                            %
                        </em>
                        <br>
                        @if(count($clasificacion->lotes_reByVariedad($variedad->id_variedad)) > 0)
                            <button type="button" class="btn btn-xs btn-info" style="margin-top: 10px" title="Ver lotes"
                                    onclick="ver_lotes('{{$variedad->id_variedad}}', '{{$clasificacion->id_clasificacion_verde}}')">
                                <i class="fa fa-fw fa-sitemap"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-xs btn-warning" style="margin-top: 10px"
                                    title="Destinar lotes"
                                    onclick="destinar_lotes('{{$variedad->id_variedad}}', '{{$clasificacion->id_clasificacion_verde}}')">
                                <i class="fa fa-fw fa-exchange"></i>
                            </button>
                        @endif
                    </th>
                    <td style="border-color: #9d9d9d; padding: 0" class="text-center">
                        <table width="100%" class="table table-responsive table-bordered"
                               style="border: 1px solid #9d9d9d; margin-bottom: 0">
                            <tr>
                                @foreach($clasificacion->unitarias() as $unitaria)
                                    @if($clasificacion->getRamosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria) > 0)
                                        <th style="border-color: #9d9d9d; background-color: {{explode('|',$unitaria->color)[0]}}; color: {{explode('|',$unitaria->color)[1]}}"
                                            class="text-center">
                                            {{explode('|',$unitaria->nombre)[0]}}{{$unitaria->unidad_medida->siglas}} -
                                            <em>
                                                {{$clasificacion->getPorcentajeUnitariaByVariedad($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                                %
                                            </em>
                                        </th>
                                    @endif
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($clasificacion->unitarias() as $unitaria)
                                    @if($clasificacion->getRamosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria) > 0)
                                        <td style="border-color: #9d9d9d;" class="text-center">
                                        <span class="badge" title="Ramos ingresados">
                                            {{$clasificacion->getRamosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                        </span>
                                            <span class="badge" style="background-color: #357ca5" title="Tallos">
                                            {{$clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                        </span>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        </table>
                    </td>
                    <td style="border-color: #9d9d9d; padding: 10px; margin-bottom: 0" class="text-center" width="10%">
                        {{$clasificacion->getTotalRamosEstandarByVariedad($variedad->id_variedad)}}
                    </td>
                    <td style="border-color: #9d9d9d; padding: 10px; margin-bottom: 0" class="text-center" width="10%">
                        {{round($clasificacion->getTotalRamosEstandarByVariedad($variedad->id_variedad) /
                        getConfiguracionEmpresa()->ramos_x_caja, 2)}}
                    </td>
                    <td style="border-color: #9d9d9d; padding: 10px; margin-bottom: 0" class="text-center" width="5%">
                        {{round($clasificacion->tallos_x_variedad($variedad->id_variedad) /
                        $clasificacion->getTotalRamosEstandarByVariedad($variedad->id_variedad),2)}}
                    </td>
                    <th style="border-color: #9d9d9d" class="text-center">
                        <span class="badge" title="Tallos en recepción">
                            {{$clasificacion->total_tallos_recepcionByVariedad($variedad->id_variedad)}}
                        </span>
                        <span class="badge" title="Tallos clasificados" style="background-color: #357ca5">
                            {{$clasificacion->tallos_x_variedad($variedad->id_variedad)}}
                        </span>
                        <br>
                        <br>
                        <span class="badge" title="Desecho" style="background-color: #ce8483">
                            {{round(100 - (($clasificacion->tallos_x_variedad($variedad->id_variedad) * 100) /
                            $clasificacion->total_tallos_recepcionByVariedad($variedad->id_variedad)),2)}}%
                        </span>
                    </th>
                </tr>
            @endforeach
        </table>
    </div>
</div>