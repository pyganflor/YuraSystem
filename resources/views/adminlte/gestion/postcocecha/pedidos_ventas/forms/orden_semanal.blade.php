<script>

    function calcular_totales_tinturado(esp_emp, ini = false) {
        fil = $('#marcaciones_' + esp_emp).val();
        col = $('#coloraciones_' + esp_emp).val();
        ramos_x_caja = $('#ramos_x_caja_' + esp_emp).val();
        ids_det_esp = $('div#pedido_creado input.id_det_esp_' + esp_emp);

        total = 0;
        for (f = 0; f < fil; f++) {
            total_fila = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (c = 0; c < col; c++) {
                    cant = $('#ramos_marcacion_' + f + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();

                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#parcial_marcacion_' + f + '_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
                total_fila += parcial;
            }

            $('#total_ramos_marcacion_' + f + '_' + esp_emp).val(total_fila);
            $('#total_piezas_marcacion_' + f + '_' + esp_emp).val(Math.round((total_fila / ramos_x_caja) * 100) / 100);
            total += total_fila;
        }
        $('#total_ramos_' + esp_emp).val(total);
        $('#total_piezas_' + esp_emp).val(Math.round((total / ramos_x_caja) * 100) / 100);

        for (c = 0; c < col; c++) {
            total_col = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (f = 0; f < fil; f++) {
                    cant = $('#ramos_marcacion_' + f + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#parcial_color_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
                total_col += parcial;
            }
        }

        for (det = 0; det < ids_det_esp.length; det++) {
            parcial = 0;
            for (c = 0; c < col; c++) {
                cant = $('#parcial_color_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                if (cant != '') {
                    parcial += parseInt(cant);
                }
            }
            $('#parcial_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
        }

        if (!ini)
            $('.elemento_distribuir').hide();
    }

</script>

{{--@php
    $det_ped = $pedido->detalles[$pos_det_ped];
@endphp--}}
<form id="form-update_orden_semanal">
    <input type="hidden" id="listar_resumen_pedido" value="{{$listar_resumen_pedido}}">
    <table class="table-bordered" width="100%" style="margin-bottom: 10px">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Fecha Pedido
            </th>
            <th class="text-center" style="border-color: #9d9d9d" width="85px">
                <input type="date" id="fecha_pedido" name="fecha_pedido" value="{{isset($pedido) ? $pedido->fecha_pedido :  now()->toDateString()}}" required width="100%"
                       class="form-control">
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <i class="fa fa-building-o"></i> Facturar pedido con:
            </th>
            <th class="text-center" style="border-color: #9d9d9d" width="85px">
                <select class="form-control" style="width:180px" id="id_configuracion_empresa" name="id_configuracion_empresa" title="Seleccione un empresa para facturar los pedidos">
                    @foreach(getConfiguracionEmpresa(null,true) as $empresa)
                        @php $lastPedido = getLastPedido(); @endphp
                        <option {{isset($lastPedido) ? (($lastPedido->id_configuracion_empresa === $empresa->id_configuracion_empresa) ? "selected" : "") : ""}}
                                style=" color: black" value="{{$empresa->id_configuracion_empresa}}">{{$empresa->nombre}}</option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Cliente
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{$pedido->cliente->detalle()->nombre}}
            </th>

        </tr>
    </table>

</form>

<div id="div_tabla_distribucion">
    @include('adminlte.gestion.postcocecha.pedidos_ventas.forms._tabla')
</div>

<div class="text-center" style="margin-top: 10px">
    {{--<button type="button" class="btn btn-xs btn-default" onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped}}',false)">
        <i class="fa fa-fw fa-refresh"></i> Refrescar
    </button>--}}

    {{--<button type="button" class="btn btn-xs btn-success" onclick="guardar_distribucion('{{csrf_token()}}')" id="btn_guardar_distribucion"
            style="display: none;">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>--}}
    {{--@if($have_prev)
        <button type="button" class="btn btn-xs btn-primary"
                onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped - 1}}', false)">
            <i class="fa fa-fw fa-long-arrow-left"></i> Anterior
        </button>
    @endif
    @if($have_next)
        <button type="button" class="btn btn-xs btn-primary"
                onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped + 1}}', false)">
            <i class="fa fa-fw fa-long-arrow-right"></i> Siguiente
        </button>
    @else
        <button type="button" class="btn btn-xs btn-default" onclick="terminar_edicion()">
            <i class="fa fa-fw fa-times"></i> Terminar
        </button>
    @endif
    <button type="button" class="btn btn-xs btn-danger" onclick="eliminar_detalle_pedido('{{$det_ped->id_detalle_pedido}}','{{csrf_token()}}')">
        <i class="fa fa-fw fa-trash"></i> Eliminar
    </button>--}}
</div>
<input type="hidden" id="id_pedido" value="{{$pedido->id_pedido}}">
<input type="hidden" id="id_cliente_update" value="{{$pedido->id_cliente}}">
{{--<input type="hidden" id="id_detalle_pedido" value="{{$det_ped->id_detalle_pedido}}">
<input type="hidden" id="pos_det_ped" value="{{$pos_det_ped}}">
<input type="hidden" id="have_next" value="{{$have_next ? 1 : 0}}">
<input type="hidden" id="have_prev" value="{{$have_prev ? 1 : 0}}">--}}

<script>


</script>
