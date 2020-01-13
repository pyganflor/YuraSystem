<legend class="text-center">Variedad: "{{$variedad->nombre}}"</legend>
<table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Clasificación
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">

        </th>
    </tr>
    @foreach($clasificaciones as $c)
        <tr id="tr_unitaria_{{$c->id_clasificacion_unitaria}}">
            <th class="text-center" style="border-color: #9d9d9d">
                {{explode('|', $c->nombre)[0]}} {{$c->unidad_medida->siglas}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="checkbox" id="check_unitaria_{{$c->id_clasificacion_unitaria}}"
                       onchange="store_vinculo('{{$c->id_clasificacion_unitaria}}')"
                        {{$variedad->getClasificacion($c->id_clasificacion_unitaria) != '' ? 'checked' : ''}}>
            </th>
        </tr>
    @endforeach
</table>

<input type="hidden" id="variedad_vinculo" value="{{$variedad->id_variedad}}">

<script>
    function store_vinculo(unitaria) {
        datos = {
            _token: '{{csrf_token()}}',
            variedad: $('#variedad_vinculo').val(),
            unitaria: unitaria,
        };
        $('#tr_unitaria_' + unitaria).LoadingOverlay('show');
        $.post('{{url('plantas_variedades/store_vinculo')}}', datos, function (retorno) {
            if (retorno.success == false) {
                alerta(retorno.mensaje);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_unitaria_' + unitaria).LoadingOverlay('hide');
        });
    }
</script>