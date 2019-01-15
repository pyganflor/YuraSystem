<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Cantidades de ramos armados por cada variedad
        </h3>
    </div>
    <div class="box-body">
        <small>
            Estos datos corresponden a los pedidos del
            día <strong>{{getDias()[transformDiaPhp(date('w',strtotime($fecha)))]}} {{convertDateToText($fecha)}}</strong>
        </small>
        <table class="table table-responsive table-bordered" style="border: 1px solid #9d9d9d; margin-bottom: 0; font-size: 0.8em"
               id="table_empaquetar" onmouseover="$(this).css('border-color','#ADD8E6')"
               onmouseleave="$(this).css('border-color','#9d9d9d')">
            <tr>
                <th style="border-color: #9d9d9d"></th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Sacados
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Armados
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Diferencia
                </th>
            </tr>
            @php
                $empaquetados = 0;
            @endphp
            @foreach($stock_frio as $frio)
                <tr>
                    <th style="border-color: #9d9d9d; background-color: #e9ecef" width="50%">
                        {{getVariedad($frio->id_variedad)->nombre}}
                    </th>
                    <td class="text-center" style="border-color: #9d9d9d"
                        title="Ramos sacados de aperturas para el empaquetado de los pedidos de esta fecha">
                        {{round($frio->cantidad_ingresada,2)}}
                    </td>
                    <td style="border-color: #9d9d9d; background-color: {{$frio->empaquetado == 1 ? '#B9FFB4' : ''}}" class="text-center">
                        @if($frio->empaquetado == 0)
                            {{getVariedad($frio->id_variedad)->getPedidosToFecha($fecha)}}
                        @else
                            {{$frio->cantidad_empaquetada}}
                        @endif
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{round($frio->cantidad_ingresada,2) - getVariedad($frio->id_variedad)->getPedidosToFecha($fecha)}}
                    </td>
                </tr>
                @if($frio->empaquetado == 0)
                    <input type="hidden" class="id_stock_empaquetado" value="{{$frio->id_stock_empaquetado}}">
                    <input type="hidden" id="cantidad_empaquetada_{{$frio->id_stock_empaquetado}}"
                           value="{{getVariedad($frio->id_variedad)->getPedidosToFecha($fecha)}}">
                @endif
                @php
                    if($frio->empaquetado == 1)
                        $empaquetados++;
                @endphp
            @endforeach
        </table>
        @if(count($stock_frio) != $empaquetados)
            <div class="text-center" style="margin-top: 5px">
                <button type="button" class="btn btn-xs btn-success" onclick="update_stock_empaquetado()">
                    <i class="fa fa-fw fa-save"></i> Guardar
                </button>
            </div>
        @endif
    </div>
</div>

<input type="hidden" id="fecha_armado" value="{{$fecha}}">

<script>
    function update_stock_empaquetado() {
        listado = $('.id_stock_empaquetado');
        arreglo = [];
        for (i = 0; i < listado.length; i++) {
            data = {
                id: listado[i].value,
                cantidad: $('#cantidad_empaquetada_' + listado[i].value).val()
            };
            arreglo.push(data);
        }
        datos = {
            _token: '{{csrf_token()}}',
            arreglo: arreglo,
            fecha: $('#fecha_armado').val()
        };
        modal_quest('modal_quest_empaquetado', '<div class="alert alert-info text-center">' +
            'Está a punto de guardar la información en el sistema. Todos los pedidos de la fecha indicada quedarán como armados. ¿Confirmar?' +
            '</div>', '<i class="fa fa-fw fa-exclamation-triangle"></i> Confirmar armado', true, false, '{{isPC() ? '35%' : ''}}', function () {
            post_jquery('{{url('clasificacion_blanco/update_stock_empaquetado')}}', datos, function () {
                cerrar_modals();
                empaquetar(datos['fecha']);
            });
        });
    }
</script>