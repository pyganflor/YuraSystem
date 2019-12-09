<div class="text-right" style="margin-bottom: 10px">
    <button class="btn btn-primary btn-xs" title="Agregar intervalo de rango" onclick="add_row('rango')">
        <i class="fa fa-plus"></i>
    </button>
    <button class="btn btn-success btn-xs" title="Agregar intervalo de condición" onclick="add_row('condicion')">
        <i class="fa fa-plus"></i>
    </button>
</div>
<form id="form_add_intervalo" class="form_rows_intervalos">
    <input type="hidden" id="id_indicador" value="{{$indicador}}">
    <div id="alert_intervalo" class="alert alert-info text-center">Ingrese al menos un intervalo</div>
</form>
