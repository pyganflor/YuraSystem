<div id="table_lotes">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_lotes">
            <thead>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Variedad
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Calibre
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Fecha ingreso
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Semana
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Ramos
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Tallos
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Etapa
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Estancia
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Opciones
                </th>
            </tr>
            </thead>
            @foreach($listado as $lote)
                <tr onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')">
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getLoteREById($lote->id_lote_re)->variedad->nombre}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{explode('|',getLoteREById($lote->id_lote_re)->clasificacion_unitaria->nombre)[0]}}
                        {{getLoteREById($lote->id_lote_re)->variedad->unidad_de_medida}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getLoteREById($lote->id_lote_re)->getCurrentFecha()}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getSemanaByDateVariedad(getLoteREById($lote->id_lote_re)->getCurrentFecha(),getLoteREById($lote->id_lote_re)->id_variedad)->codigo}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        @if($calibre == '')
                            {{round(getLoteREById($lote->id_lote_re)->cantidad_tallos /
                            explode('|',getLoteREById($lote->id_lote_re)->clasificacion_unitaria->nombre)[1],2)}}
                        @else
                            @php
                                $f = round(($calibre->nombre / explode('|',getLoteREById($lote->id_lote_re)->clasificacion_unitaria->nombre)[0]),2);
                                echo round(getLoteREById($lote->id_lote_re)->cantidad_tallos / $f,2);
                            @endphp
                        @endif
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getLoteREById($lote->id_lote_re)->cantidad_tallos}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getLoteREById($lote->id_lote_re)->getCurrentEtapa()}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <span class="badge" title="Días estimados">
                            {{getLoteREById($lote->id_lote_re)->getCurrentDiasEstimados()}}
                        </span>
                        <span class="badge"
                              style="background-color: {{getLoteREById($lote->id_lote_re)->getCurrentEstancia() >= getVariedad(getLoteREById($lote->id_lote_re)->id_variedad)->maximo_apertura
                              ? '#ce8483' : '#357ca5'}}"
                              title="Días reales">
                            {{getLoteREById($lote->id_lote_re)->getCurrentEstancia()}}
                        </span>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default" title="Detalles" onclick="ver_lote('{{$lote->id_lote_re}}')">
                                <i class="fa fa-fw fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_lotes">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>