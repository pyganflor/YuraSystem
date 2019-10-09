<legend class="text-center" style="font-size: 1em">
    <i class="fa fa-fw fa-info-circle"></i> Ingrese los datos que desea modificar para las semanas|módulos seleccionados
</legend>
<table class="table-bordered" width="100%" style="border: 3px solid #9d9d9d">
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid blue;
            border-bottom: 8px solid blue;">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Tipo
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <select id="tipo" style="width: 100%">
                <option value="S">Siembra</option>
                <option value="P">Poda</option>
            </select>
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px">
            <button class="btn btn-xs btn-success" type="button">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Curva
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="text" id="curva" maxlength="25" style="width: 100%;" class="text-center" placeholder="30-40-30">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px">
            <button class="btn btn-xs btn-success" type="button">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Semana cosecha
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="semana_cosecha" max="52" style="width: 100%;" class="text-center" value="14">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px">
            <button class="btn btn-xs btn-success" type="button">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid blue;
            border-bottom: 8px solid blue;">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Plantas iniciales
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="plantas_iniciales" max="99999" style="width: 100%;" class="text-center">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px">
            <button class="btn btn-xs btn-success" type="button">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
                border-right: 8px solid green;
                border-top: 8px solid green;
                border-left: 8px solid red;
                border-bottom: 8px solid blue;">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            % Desecho
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="desecho" max="100" style="width: 100%;" class="text-center">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px">
            <button class="btn btn-xs btn-success" type="button">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid green;
            border-top: 8px solid green;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Tallos x Planta
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="tallos_x_planta" max="100" min="0" style="width: 100%;" class="text-center">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px">
            <button class="btn btn-xs btn-success" type="button">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" width="10px">
            <div style="width:0; height:0; -moz-border-radius: 100%; -webkit-border-radius: 100%; border-radius: 100%;
            border-right: 8px solid blue;
            border-top: 8px solid red;
            border-left: 8px solid red;
            border-bottom: 8px solid blue;">
            </div>
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="25%">
            Tallos x Ramo
        </th>
        <td class="text-cetner" style="border-color: #9d9d9d">
            <input type="number" id="tallos_x_planta" max="100" min="0" style="width: 100%;" class="text-center">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="50px">
            <button class="btn btn-xs btn-success" type="button">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </th>
    </tr>
</table>


<div class="text-right" style="margin-top: 10px">
    <legend style="font-size: 1em; margin-bottom: 0">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseLeyendaForm1">
            <strong style="color: black">Leyenda <i class="fa fa-fw fa-caret-down"></i></strong>
        </a>
    </legend>
    <ul style="margin-top: 5px" class="list-unstyled panel-collapse collapse" id="collapseLeyendaForm1">
        <li>Semanas <i class="fa fa-fw fa-circle" style="color: red"></i></li>
        <li>Ciclos <i class="fa fa-fw fa-circle" style="color: green"></i></li>
        <li>Proyecciones <i class="fa fa-fw fa-circle" style="color: blue"></i></li>
    </ul>
</div>