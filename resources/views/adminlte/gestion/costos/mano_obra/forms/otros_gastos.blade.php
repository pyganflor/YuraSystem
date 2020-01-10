<table class="table-striped table-bordered table-hover" width="100%" style="border: 2px solid #9d9d9d;">
    <tr>
        <th colspan="2" class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Área: "{{$area->nombre}}"
        </th>
    </tr>
    <tr>
        <th class="text-center" style=" border-color: #9d9d9d; background-color: #e9ecef">
            Semana
        </th>
        <td class="text-center" style=" border-color: #9d9d9d">
            <input type="number" id="codigo_semana" name="codigo_semana"
                   value="{{isset($semana_actual) ? $semana_actual->codigo : ''}}" style="width: 100%" class="text-center"
                   onchange="buscar_otros_gastos()">
        </td>
    </tr>
    <tr>
        <th class="text-center" style=" border-color: #9d9d9d; background-color: #e9ecef">
            GIP
        </th>
        <td class="text-center" style=" border-color: #9d9d9d">
            <input type="text" id="gip" name="gip" value="{{isset($otros_gastos) ? $otros_gastos->gip : 0}}" style="width: 100%"
                   class="text-center input_search">
        </td>
    </tr>
    <tr>
        <th class="text-center" style=" border-color: #9d9d9d; background-color: #e9ecef">
            GA
        </th>
        <td class="text-center" style=" border-color: #9d9d9d">
            <input type="text" id="ga_" name="ga_" value="{{isset($otros_gastos) ? $otros_gastos->ga : 0}}" style="width: 100%"
                   class="text-center input_search">
        </td>
    </tr>
    <tr>
        <th colspan="2" class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-block btn-success" onclick="store_otros_gastos()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        </th>
    </tr>
</table>

<input type="hidden" id="id_area_otros_gasstos" name="id_area_otros_gasstos" value="{{$area->id_area}}">

<script>
    function store_otros_gastos() {
        datos = {
            _token: '{{csrf_token()}}',
            id_area: $('#id_area_otros_gasstos').val(),
            semana: $('#codigo_semana').val(),
            gip: $('#gip').val(),
            ga: $('#ga_').val(),
        };
        post_jquery('{{url('gestion_mano_obra/store_otros_gastos')}}', datos, function (retorno) {

        });
    }

    function buscar_otros_gastos() {
        datos = {
            _token: '{{csrf_token()}}',
            id_area: $('#id_area_otros_gasstos').val(),
            semana: $('#codigo_semana').val(),
        };
        $('.input_search').LoadingOverlay('show');
        $.post('{{url('gestion_mano_obra/buscar_otros_gastos')}}', datos, function (retorno) {
            $('#gip').val(retorno.gip);
            $('#ga_').val(retorno.ga);
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('.input_search').LoadingOverlay('hide');
        });
    }
</script>