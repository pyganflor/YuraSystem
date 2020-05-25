<legend class="text-center" style="font-size: 1em">
    <i class="fa fa-fw fa-info-circle"></i> Indique con (+),(-) el número de semanas que desea mover
</legend>
<table class="table-bordered" width="100%" style="border: 3px solid #9d9d9d">
    <tr id="tr_mover_cosecha">
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Cosecha
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="cosecha" min="-14" max="14" style="width: 100%" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_mover_cosecha">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="mover_cosecha()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" colspan="4" style="border-color: #9d9d9d; background-color: #e9ecef">
            <input type="checkbox" class="mouse-hand" id="check_save_proyeccion" checked>
            <label for="check_save_proyeccion" class="mouse-hand">Guardar en proyecciones</label>
        </th>
    </tr>
    <tr id="tr_mover_inicio_proy">
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Inicio de programación
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="ini_proy" min="-14" max="14" style="width: 100%" class="text-center input-yura_white">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px" id="celda_button_mover_inicio_proy">
            <button class="btn btn-xs btn-yura_primary" type="button" onclick="mover_inicio_proy()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
</table>

<div class="row">
    <div class="col-md-6">
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
    <div class="col-md-6">
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
</div>