<legend class="text-center" style="font-size: 1em">
    <i class="fa fa-fw fa-info-circle"></i> Ingrese los datos que desea modificar para las semanas|módulos seleccionados
</legend>
<table class="table-bordered" width="100%" style="border: 3px solid #9d9d9d">
    <tr id="tr_actualizar_curva">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas - Ciclos - Proyecciones">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Curva
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="text" id="curva" maxlength="25" style="width: 100%;" class="text-center input-yura_white" placeholder="30-40-30">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_curva">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_curva()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_actualizar_semana_cosecha">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas - Ciclos - Proyecciones">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Semana cosecha
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="semana_cosecha" max="52" style="width: 100%;" class="text-center input-yura_white" value="14">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_semana_cosecha">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_semana_cosecha()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_actualizar_plantas_iniciales">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid blue;
            border-bottom: 8px solid blue;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Ciclos - Proyecciones">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Plantas iniciales
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="plantas_iniciales" max="99999" style="width: 100%;" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_plantas_iniciales">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_plantas_iniciales()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_actualizar_desecho">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
                border-right: 8px solid green;
                border-top: 8px solid green;
                border-left: 8px solid red;
                border-bottom: 8px solid blue;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas - Ciclos - Proyecciones">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            % Desecho
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="desecho" max="100" style="width: 100%;" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_desecho">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_desecho()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_actualizar_tallos_planta">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas - Ciclos - Proyecciones">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Tallos x Planta
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="tallos_x_planta" max="100" min="0" style="width: 100%;" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_tallos_planta">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_tallos_planta()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_actualizar_tallos_ramo">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid blue;
            border-top: 8px solid red;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas - Proyecciones">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Tallos x Ramo
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="tallos_x_ramo" max="100" min="0" style="width: 100%;" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_tallos_ramo">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_tallos_ramo()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" colspan="4" style="border-color: #9d9d9d; background-color: #e9ecef">
            <input type="checkbox" class="mouse-hand" id="check_save_semana" checked>
            <label for="check_save_semana" class="mouse-hand">Guardar en semana(s)</label>
        </th>
    </tr>
    <tr id="tr_actualizar_semana_cosecha_siembra">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid red;
            border-top: 8px solid red;
            border-left: 8px solid red;
            border-bottom: 8px solid red;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Semana cosecha Siembra
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="semana_cosecha_siembra" max="100" min="0" style="width: 100%;" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_semana_cosecha_siembra">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_semana_cosecha_siembra()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_actualizar_tallos_planta_siembra">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid red;
            border-top: 8px solid red;
            border-left: 8px solid red;
            border-bottom: 8px solid red;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Tallos x Planta Siembra
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="tallos_planta_siembra" max="100" min="0" style="width: 100%;" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_tallos_planta_siembra">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_tallos_planta_siembra()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_actualizar_tallos_ramo_siembra">
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid red;
            border-top: 8px solid red;
            border-left: 8px solid red;
            border-bottom: 8px solid red;" data-toggle="tooltip" data-placement="top" data-html="true"
                 title="Semanas">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Tallos x Ramo Siembra
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="tallos_ramo_siembra" max="100" min="0" style="width: 100%;" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_tallos_ramo_siembra">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="actualizar_tallos_ramo_siembra()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
</table>

<div class="row">
    <div class="col-md-2">
        <div class="text-center" style="margin-top: 10px">
            <legend style="font-size: 1em; margin-bottom: 0">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseSemanas">
                    <strong style="color: black">Semanas <i class="fa fa-fw fa-caret-down"></i></strong>
                </a>
            </legend>
            <ul style="margin-top: 5px" class="list-unstyled panel-collapse collapse" id="collapseSemanas">
                @foreach($semanas as $sem)
                    <li>
                        <strong>{{$sem->codigo}}</strong>
                    </li>
                    <input type="checkbox" id="id_semana_{{$sem->id_semana}}" class="check_id_semana hidden" checked>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-2">
        <div class="text-center" style="margin-top: 10px">
            <legend style="font-size: 1em; margin-bottom: 0">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseModulos">
                    <strong style="color: black">Módulos <i class="fa fa-fw fa-caret-down"></i></strong>
                </a>
            </legend>
            <ul style="margin-top: 5px" class="list-unstyled panel-collapse collapse" id="collapseModulos">
                @foreach($modulos as $mod)
                    <li>
                        <strong>{{$mod->nombre}}</strong>
                    </li>
                    <input type="checkbox" id="id_modulo_{{$mod->id_modulo}}" class="check_id_modulo hidden" checked>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-8">
        <div class="text-right" style="margin-top: 10px">
            <legend style="font-size: 1em; margin-bottom: 0">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseLeyendaForm1">
                    <strong style="color: black">Leyenda <i class="fa fa-fw fa-caret-down"></i></strong>
                </a>
            </legend>
            <ul style="margin-top: 5px" class="list-unstyled panel-collapse collapse" id="collapseLeyendaForm1">
                <li>
                    <strong>Semanas <i class="fa fa-fw fa-circle" style="color: red"></i></strong>
                </li>
                <li>
                    <strong>Ciclos <i class="fa fa-fw fa-circle" style="color: green"></i></strong>
                </li>
                <li>
                    <strong>Proyecciones <i class="fa fa-fw fa-circle" style="color: blue"></i></strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>