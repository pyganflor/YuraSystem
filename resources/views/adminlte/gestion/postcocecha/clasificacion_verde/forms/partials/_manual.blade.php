<form id="form-add_clasificacion_verde_x_variedad_{{$variedad->id_variedad}}">
    <div style="overflow-x: scroll">
        <table class="table table-bordered table-responsive" width="100%" style="border: 1px solid #9d9d9d; font-size: 0.8em">
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" width="25%">
                    Calibre
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" width="25%">
                    Ramos
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" width="25%">
                    Tallos por Ramo
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" width="25%">
                    Total
                </th>
            </tr>
            @foreach($unitarias as $unitaria)
                <tr style="background-color: {{explode('|',$unitaria->color)[0]}}; color: {{explode('|',$unitaria->color)[1]}}">
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{explode('|',$unitaria->nombre)[0]}} {{$unitaria->unidad_medida->siglas}}

                        <input type="hidden" id="nombre_unitaria_{{explode('|',$unitaria->nombre)[0].$unitaria->unidad_medida->siglas}}"
                               value="{{$unitaria->id_clasificacion_unitaria}}">
                        <input type="hidden" id="unidad_medida_unitaria_{{$unitaria->id_clasificacion_unitaria}}"
                               value="{{$unitaria->unidad_medida->siglas}}">
                        <input type="hidden" id="fondo_unitaria_{{$unitaria->id_clasificacion_unitaria}}"
                               value="{{explode('|',$unitaria->color)[0]}}">
                        <input type="hidden" id="texto_unitaria_{{$unitaria->id_clasificacion_unitaria}}"
                               value="{{explode('|',$unitaria->color)[1]}}">
                    </th>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="cantidad_ramos_{{$unitaria->id_clasificacion_unitaria}}" min="0"
                               name="cantidad_ramos_{{$unitaria->id_clasificacion_unitaria}}" required
                               style="background-color: {{explode('|',$unitaria->color)[0]}}; color: {{explode('|',$unitaria->color)[1]}}"
                               class="text-center" value="0" onkeypress="return isNumber(event)" onchange="calcular_totales_verde()">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="tallos_x_ramos_{{$unitaria->id_clasificacion_unitaria}}" min="0"
                               name="tallos_x_ramos_{{$unitaria->id_clasificacion_unitaria}}" required
                               style="background-color: {{explode('|',$unitaria->color)[0]}}; color: {{explode('|',$unitaria->color)[1]}}"
                               class="text-center" value="{{$unitaria->tallos_x_ramo}}" onkeypress="return isNumber(event)"
                               onchange="calcular_totales_verde()">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <span class="badge" id="total_x_unitaria_{{$unitaria->id_clasificacion_unitaria}}">0</span>
                    </td>
                </tr>
                <input type="hidden" id="id_unitiaria_{{$unitaria->id_clasificacion_unitaria}}" class="ids_unitaria"
                       value="{{$unitaria->id_clasificacion_unitaria}}">
            @endforeach
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    Total
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <span class="badge" id="html_ramos_x_variedad_{{$variedad->id_variedad}}">0</span>
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    Total
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <span class="badge" id="html_tallos_x_variedad_{{$variedad->id_variedad}}">0</span>
                    <input type="hidden" id="input_tallos_x_variedad_{{$variedad->id_variedad}}" value="0">
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <small class="error" style="display: none;" id="msg_error">
                        <i class="fa fa-fw fa-exclamation-triangle"></i> La cantidad clasificada no puede ser mayor a la cantidad en
                        recepción
                    </small>
                </td>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #ce8483; color: white;">
                    Desecho por variedad
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <span class="badge" id="html_desecho_x_variedad_{{$variedad->id_variedad}}">100%</span>
                    <input type="hidden" id="desecho_x_variedad_{{$variedad->id_variedad}}" value="100">
                </th>
            </tr>
        </table>
    </div>
</form>

@if($clasificacion_verde == '')
    <div class="text-center" id="btn_store_verde">
        <button type="button" class="btn btn-success btn-sm" onclick="store_verde()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>
@elseif($clasificacion_verde->activo == 1)
    <div class="text-center" id="btn_store_verde">
        <button type="button" class="btn btn-success btn-sm" onclick="store_verde()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>
@endif