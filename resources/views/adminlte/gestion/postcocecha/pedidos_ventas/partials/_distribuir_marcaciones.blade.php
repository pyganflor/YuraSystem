<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Distribuir marcaciones
        </h3>
    </div>
    <div class="box-body">
        <div style="width: 100%; overflow-x: scroll" id="div_tabla_distribucion_pedido">
            <table class="table-bordered table-striped table-responsive" width="100%" style="border: 2px solid #9d9d9d;">
                {{-- FILA CABECERA --}}
                <tr>
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d; background-color: #9d9d9d; color: white">
                        <p style="width: 150px;">Índices</p>
                    </th>
                    @foreach($coloraciones as $color)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: {{$color->fondo}}; color: {{$color->texto}};
                                border-bottom: 3px solid #9d9d9d">
                            <p style="width: 150px;">{{$color->nombre}}</p>
                        </th>
                    @endforeach
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d; background-color: #9d9d9d; color: white">
                        <p style="padding: 15px;">Piezas</p>
                    </th>
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d; background-color: #9d9d9d; color: white">
                        <p style="padding: 10px">Ramos</p>
                    </th>
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d; background-color: #9d9d9d; color: white">
                        <p style="padding: 10px">Total</p>
                    </th>
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d; background-color: #9d9d9d; color: white">
                        <p style="width: 150px;">Índices</p>
                    </th>
                </tr>
                {{-- FILAS DE LA TABLA--}}
                @foreach($marcas as $marca)
                    @for($m = 1; $m <= $marca['cant_distribuciones']; $m++)
                        <tr>
                            @if($m == 1)
                                <th class="text-center" style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d"
                                    rowspan="{{$marca['cant_distribuciones']}}">
                                    {{$marca['marcacion']->nombre}}
                                </th>
                            @endif
                            @for($c = 0; $c < count($coloraciones); $c++)
                                <th class="text-center"
                                    style="border-color: #9d9d9d; border-bottom: {{$m == $marca['cant_distribuciones'] ? '3px solid #9d9d9d' : ''}}">
                                    <input type="number" min="0" onkeypress="return isNumber(event)" class="text-center"
                                           style="background-color: {{$coloraciones[$c]->fondo}}; color: {{$coloraciones[$c]->texto}}"
                                           id="cant_distrib_marca_{{$marca['marcacion']->id_marcacion}}_color_{{$coloraciones[$c]->nombre}}_distr_{{$m}}"
                                           name="cant_distrib_marca_{{$marca['marcacion']->id_marcacion}}_color_{{$coloraciones[$c]->nombre}}_distr_{{$m}}">
                                </th>
                            @endfor
                            <th class="text-center" style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d">
                                ok
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d">
                                <form id="form-piezas_distrib_marca_{{$marca['marcacion']->id_marcacion}}_distr_{{$m}}">
                                    <input type="number" min="1" onkeypress="return isNumber(event)"
                                           class="text-center piezas_{{$marca['marcacion']->id_marcacion}}" required
                                           id="piezas_distrib_marca_{{$marca['marcacion']->id_marcacion}}_distr_{{$m}}"
                                           max="{{round($marca['marcacion']->getTotalRamos() / $det_esp->cantidad, 2)}}" style="width: 100%"
                                           name="piezas_distrib_marca_{{$marca['marcacion']->id_marcacion}}_distr_{{$m}}">
                                </form>
                                <input type="hidden" class="num_distribucion_{{$marca['marcacion']->id_marcacion}}"
                                       value="{{$m}}">
                            </th>
                            @if($m == 1)
                                <th class="text-center" style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d"
                                    rowspan="{{$marca['cant_distribuciones']}}">
                                    <span class="badge">{{$marca['marcacion']->getTotalRamos()}}</span>
                                </th>
                                <th class="text-center" style="border-color: #9d9d9d; border-bottom: 3px solid #9d9d9d"
                                    rowspan="{{$marca['cant_distribuciones']}}">
                                    {{$marca['marcacion']->nombre}}
                                    <br>
                                    <button type="button" class="btn btn-xs btn-default"
                                            onclick="calcular_distribucion('{{$marca['marcacion']->id_marcacion}}', '{{$det_esp->cantidad}}')">
                                        <i class="fa fa-fw fa-check"></i>
                                    </button>
                                </th>
                            @endif
                        </tr>
                    @endfor
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="text-center">
    <button type="button" class="btn btn-success" onclick="store_distribucion_pedido('{{$pedido->id_pedido}}')">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>
</div>

<script>
    function calcular_distribucion(marca, ramos) {
        num_distribucion = $('.num_distribucion_' + marca);
        arreglo_piezas = [];
        flag = 1;
        for (i = 0; i < num_distribucion.length; i++) {
            distr = num_distribucion[i].value;
            if ($('#form-piezas_distrib_marca_' + marca + '_distr_' + distr).valid()) {
                arreglo_piezas.push($('#piezas_distrib_marca_' + marca + '_distr_' + distr).val());
            } else {
                flag = 0;
            }
        }
        if (flag == 1) {
            datos = {
                _token: '{{csrf_token()}}',
                id_marcacion: marca,
                arreglo_piezas: arreglo_piezas,
                ramos: ramos,
            };
            $.LoadingOverlay('show');
            $.post('{{url('pedidos/calcular_distribucion')}}', datos, function (retorno) {
                if (retorno.success) {
                    for (x = 0; x < retorno.matriz.length; x++) {   // recorrer las distribuciones
                        for (y = 0; y < retorno.matriz[x].length; y++) {    // recorrer las cantidades
                            if (retorno.matriz[x][y]['cantidad'] > 0)
                                $('#cant_distrib_marca_' + marca + '_color_' + retorno.matriz[x][y]['color'] + '_distr_' + (x + 1)).val(retorno.matriz[x][y]['cantidad']);
                        }
                    }
                } else {
                    alerta(retorno.mensaje);
                }
                $.LoadingOverlay('hide');
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
                alerta('Ha ocurrido un problema al enviar la información');
            }).always(function () {
                $.LoadingOverlay('hide');
            });
        }
    }

    function store_distribucion_pedido(pedido) {

        datos = {
            _token: '{{csrf_token()}}',
            id_pedido: pedido
        };
    }
</script>