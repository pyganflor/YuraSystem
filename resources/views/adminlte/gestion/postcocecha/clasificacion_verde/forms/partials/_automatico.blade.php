<div class="row">
    <div class="col-md-4">
        <input type="text" placeholder="Listo para escanear" id="input_escanear" style="width: 100%" class="text-center form-control"
               onchange="scan()" autocomplete="off">
    </div>
    <div class="col-md-8 text-center">
        <p style="display: none; margin-top: 10px" id="html_info_scan" class="text-center" onclick="$(this).html('')"></p>
    </div>
</div>
<form id="form-add_clasificacion_verde_x_variedad_auto_{{$variedad->id_variedad}}">
    <div style="overflow-x: scroll">
        <table class="table table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d; font-size: 0.8em"
               id="table_automatico">
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                    Calibre
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                    Ramos
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                    Tallos x Ramo
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;"
                    id="th_total_x_variedad">
                    Total
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;"
                    id="th_total_x_variedad">
                    Opciones
                </th>
            </tr>
        </table>
        <input type="hidden" id="input_tallos_x_variedad_auto_{{$variedad->id_variedad}}" value="0">
    </div>
</form>

@if($clasificacion_verde == '')
    <div class="text-center" id="btn_store_verde">
        <button type="button" class="btn btn-success btn-sm" onclick="scan_guardar()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>
@elseif($clasificacion_verde->activo == 1)
    <div class="text-center" id="btn_store_verde">
        <button type="button" class="btn btn-success btn-sm" onclick="scan_guardar()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>
@endif

<input type="hidden" id="ramos_x_defecto">
<input type="hidden" id="cant_filas" value="0">