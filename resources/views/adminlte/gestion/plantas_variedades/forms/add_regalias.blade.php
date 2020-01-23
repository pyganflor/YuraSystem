<table class="table-striped table-bordered table-hover" width="100%" style="border: 2px solid #9d9d9d;">
    <tr>
        <th colspan="2" class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Área: "{{$variedad->nombre}}"
        </th>
    </tr>
    <tr>
        <th class="text-center" style=" border-color: #9d9d9d; background-color: #e9ecef">
            Semana
        </th>
        <td class="text-center" style=" border-color: #9d9d9d">
            <input type="number" id="codigo_semana" name="codigo_semana"
                   value="{{isset($semana_actual) ? $semana_actual->codigo : ''}}" style="width: 100%" class="text-center"
                   onchange="buscar_regalias()">
        </td>
    </tr>
    <tr>
        <th class="text-center" style=" border-color: #9d9d9d; background-color: #e9ecef">
            GIP
        </th>
        <td class="text-center" style=" border-color: #9d9d9d">
            <input type="text" id="valor" name="valor" value="{{isset($regalias) ? $regalias->valor : 0}}" style="width: 100%"
                   class="text-center input_search">
        </td>
    </tr>
    <tr>
        <th colspan="2" class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-block btn-success" onclick="store_regalias()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        </th>
    </tr>
</table>

<input type="hidden" id="id_variedad_regalias" name="id_variedad_regalias" value="{{$variedad->id_variedad}}">

<script>
    function store_regalias() {
        datos = {
            _token: '{{csrf_token()}}',
            id_variedad: $('#id_variedad_regalias').val(),
            semana: $('#codigo_semana').val(),
            valor: $('#valor').val(),
        };
        post_jquery('{{url('plantas_variedades/store_regalias')}}', datos, function (retorno) {

        });
    }

    function buscar_regalias() {
        datos = {
            _token: '{{csrf_token()}}',
            id_variedad: $('#id_variedad_regalias').val(),
            semana: $('#codigo_semana').val(),
        };
        $('.input_search').LoadingOverlay('show');
        $.post('{{url('plantas_variedades/buscar_regalias')}}', datos, function (retorno) {
            $('#valor').val(retorno.valor);
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('.input_search').LoadingOverlay('hide');
        });
    }
</script>