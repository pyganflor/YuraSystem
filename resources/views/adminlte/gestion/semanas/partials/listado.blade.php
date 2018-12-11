@if(sizeof($semanas)>0)
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_content_semanas">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                VARIEDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                SEMANA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                INICIO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                FIN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                width="7%">
                CURVA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                DESECHOS %
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                width="10%">
                INICIO COSECHA PODA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                width="10%">
                INICIO COSECHA SIEMBRA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                OPCIONES
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($semanas as $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                id="row_semanas_{{$item->id_semana}}">
                <td style="border-color: #9d9d9d" class="text-center">
                    @if($item->fecha_inicial > date('Y-m-d'))
                        <input type="checkbox" id="check_{{$item->id_semana}}" class="pull-left check_week">
                    @endif
                    {{$item->variedad->planta->nombre}} - {{$item->variedad->siglas}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">{{$item->codigo}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{$item->fecha_inicial}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{$item->fecha_final}}</td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <form id="form-semana_curva-{{$item->id_semana}}">
                        <input type="text" class="text-center" name="curva_{{$item->id_semana}}" id="curva_{{$item->id_semana}}"
                               value="{{$item->curva}}" maxlength="11" required pattern="^\d{2}-\d{2}-\d{2}-\d{2}$"
                               placeholder="10-20-40-30">
                    </form>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <form id="form-semana_desecho-{{$item->id_semana}}">
                        <input type="number" class="text-center" name="desecho_{{$item->id_semana}}" id="desecho_{{$item->id_semana}}"
                               required value="{{$item->desecho}}" maxlength="2" min="0" max="99">
                    </form>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <form id="form-semana_poda-{{$item->id_semana}}">
                        <input type="number" class="text-center" name="semana_poda_{{$item->id_semana}}"
                               id="semana_poda_{{$item->id_semana}}" required value="{{$item->semana_poda}}" maxlength="2" min="1"
                               max="{{count($semanas)}}">
                    </form>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <form id="form-semana_siembra-{{$item->id_semana}}">
                        <input type="number" class="text-center" name="semana_siembra_{{$item->id_semana}}"
                               id="semana_siembra_{{$item->id_semana}}" required value="{{$item->semana_siembra}}" maxlength="2" min="1"
                               max="{{count($semanas)}}">
                    </form>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    @if($item->fecha_inicial > date('Y-m-d'))
                        <button type="button" class="btn btn-primary btn-xs" title="Guardar semana {{$item->codigo}}"
                                onclick="save_semana('{{$item->id_semana}}')">
                            <i class="fa fa-fw fa-save"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-xs btn-default" disabled title="Semana antigua">
                            <i class="fa fa-fw fa-lock"></i>
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="input-group">
        <div class="input-group-btn">
            <button type="button" class="btn btn-default" onclick="select_all()">
                <i class="fa fa-fw fa-long-arrow-up"></i> Seleccionar todos
            </button>
        </div>
        <select name="all_options" id="all_options" class="form-control" onchange="select_all_options($(this).val())">
            <option value="">¿Qué desea hacer para los marcados?</option>
            <option value="1">Igualar todos los datos</option>
            <optgroup label="Igualar por separado"></optgroup>
            <option value="2">Igualar solamente la curva</option>
            <option value="3">Igualar solamente el porcentaje de desechos</option>
            <option value="4">Igualar solamente la semana de inicio de poda</option>
            <option value="5">Igualar solamente la semana de inicio de siembra</option>
        </select>
    </div>
@else
    <div class="alert alert-info text-center">No se han programado semanas para esta variedad en el año indicado</div>
@endif

<script>
    estructura_tabla();
</script>