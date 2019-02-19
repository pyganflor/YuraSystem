<div id="table_detalles_estandar">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_detalles_estandar">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Variedad
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Clasificación unitaria
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Ramos
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Total de tallos
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                @if(round($clasificacion->getTallosByvariedadUnitaria($item->id_variedad, $item->id_clasificacion_unitaria) /
                        explode('|',getUnitaria($item->id_clasificacion_unitaria)->nombre)[1],2) > 0)
                    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                        <td style="border-color: #9d9d9d" class="text-center">{{getVariedad($item->id_variedad)->nombre}}</td>
                        <td style="border-color: #9d9d9d" class="text-center">
                            {{explode('|',getUnitaria($item->id_clasificacion_unitaria)->nombre)[0].''.getUnitaria($item->id_clasificacion_unitaria)->unidad_medida->siglas}}
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center">
                            {{round($clasificacion->getTallosByvariedadUnitaria($item->id_variedad, $item->id_clasificacion_unitaria) /
                            explode('|',getUnitaria($item->id_clasificacion_unitaria)->nombre)[1],2)}}
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center">
                            {{$clasificacion->getTallosByvariedadUnitaria($item->id_variedad, $item->id_clasificacion_unitaria)}}
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                </td>
                <td class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Total
                </td>
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$clasificacion->getTotalRamosEstandar()}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$clasificacion->total_tallos()}}
                </td>
            </tr>
        </table>
        <div id="pagination_listado_detalles_estandar">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>