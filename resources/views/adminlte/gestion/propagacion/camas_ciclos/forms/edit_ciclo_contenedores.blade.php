<div class="text-center">
    <table class="table-bordered" style="width: 100%; border-radius: 18px 18px 0 0">
        <tr>
            <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">
                Nombre
            </th>
            <th class="text-center th_yura_green" style="border-color: white; width: 100px;">
                Cantidad x Unidad
            </th>
            <th class="text-center th_yura_green" style="border-color: white; width: 100px;">
                Cantidad contenedores
            </th>
            <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 18px 0 0">
                Total
            </th>
        </tr>
        @foreach($contenedores as $c)
            @php
                $ciclo_cont = $ciclo->getCicloContenedorByContenedor($ciclo_contenedores, $c->id_contenedor_propag);
            @endphp
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$c->nombre}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$c->cantidad}}
                    <input type="hidden" id="cantidad_x_unidad_{{$c->id_contenedor_propag}}" value="{{$c->cantidad}}">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" id="cantidad_{{$c->id_contenedor_propag}}" style="width: 100%" class="text-center"
                           onchange="calcular_totales_ciclo()" min="0" value="{{$ciclo_cont != '' ? $ciclo_cont->cantidad : ''}}">
                    <input type="hidden" id="id_contenedor_{{$c->id_contenedor_propag}}" class="ids_contenedor"
                           value="{{$c->id_contenedor_propag}}">
                </td>
                <td class="text-center" style="border-color: #9d9d9d" id="td_total_{{$c->id_contenedor_propag}}">
                </td>
            </tr>
        @endforeach
        <tr>
            <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 0 0 18px" colspan="2">
                Total
            </th>
            <th class="text-center th_yura_green" style="border-color: white;" id="th_total_contenedores">
            </th>
            <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 0 18px 0" id="th_total_plantas">
            </th>
        </tr>
    </table>
    <button type="button" class="btn btn-yura_primary text-white" style="margin-top: 5px" onclick="update_ciclo_contenedores()">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>
    <input type="hidden" id="id_ciclo" value="{{$ciclo->id_ciclo_cama}}">
</div>
<script>
    calcular_totales_ciclo();

    function update_ciclo_contenedores() {
        ids_contenedor = $('.ids_contenedor');
        contenedores = [];
        for (i = 0; i < ids_contenedor.length; i++) {
            id = ids_contenedor[i].value;
            cantidad = parseInt($('#cantidad_' + id).val());
            if (cantidad > 0)
                contenedores.push({
                    id: id,
                    cantidad: cantidad,
                });
        }
        datos = {
            _token: '{{csrf_token()}}',
            ciclo: $('#id_ciclo').val(),
            contenedores: contenedores,
        };
        $.LoadingOverlay('show');
        $.post('{{url('camas_ciclos/update_ciclo_contenedores')}}', datos, function (retorno) {
            alerta_accion(retorno.mensaje, function () {
                cerrar_modals();
            });
            if (retorno.success) {
                //$('#activo_ciclos').val(1)
                listar_ciclos();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }
</script>