<table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d; margin-top: 10px">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; padding: 0" id="th_menu">
            <input type="number" id="num_marcaciones" name="num_marcaciones" onkeypress="return isNumber(event)" placeholder="Marcaciones"
                   min="1">
            <input type="number" id="num_colores" name="num_colores" onkeypress="return isNumber(event)" placeholder="Colores"
                   min="1">
            <button type="button" class="btn btn-xs btn-primary" onclick="construir_tabla()">
                <i class="fa fa-fw fa-check"></i> Siguiente
            </button>
        </th>
    </tr>
</table>

<div style="width: 100%; overflow-x: scroll; display: none" id="div_tabla_distribucion_pedido">
    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d; margin-top: 10px">
        <tr>
            <td style="border-color: #9d9d9d; padding: 0;" width="100%">
                <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d; margin-top: 10px"
                       id="table_marcaciones_x_colores"></table>
            </td>
        </tr>
    </table>
</div>