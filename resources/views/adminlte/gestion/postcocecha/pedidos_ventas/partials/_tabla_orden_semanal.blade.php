{{--<form id="form_marcas_colores">
    <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; padding: 0" id="th_menu">
                <input type="number" id="num_marcaciones" name="num_marcaciones" onkeypress="return isNumber(event)" placeholder="Marcaciones"
                       min="1" class="text-center" required>
                <input type="number" id="num_colores" name="num_colores" onkeypress="return isNumber(event)" placeholder="Colores"
                       min="1" class="text-center" required>
                <button type="button" class="btn btn-xs btn-primary" onclick="construir_tabla()" style="margin-top: 0">
                    <i class="fa fa-fw fa-check"></i> Siguiente
                </button>
            </th>
        </tr>
    </table>
</form>--}}
<div style="width: 100%; overflow-x: scroll;" id="div_tabla_distribucion_pedido">
    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d; margin-top: 10px">
        <tr>
            <td style="border-color: #9d9d9d; padding: 0;" width="100%">
                <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d"
                       id="table_marcaciones_x_colores">
                    <tr>
                        <td id="msj_busqueda_especificacion" class="alert alert-info text-center">Seleccione un cliente</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<div id="div_especificaciones_orden_semanal" style="margin-top: 10px"></div>
