@if(count($listado)>0)
    <legend>
        Pedidos
        <span class="pull-right badge" id="html_current_pedido" title="Convertidos al Estandar"></span>
        <span class="pull-right badge" id="html_current_sacar" title="Ramos seleccionados" style="margin-right: 5px"></span>
        <a href="javascript:void(0)" class="badge btn-success pull-right" id="btn_sacar" title="Sacar de apertura"
           style="display: none; margin-right: 5px" onclick="sacar_aperturas()">
            <i class="fa fa-fw fa-share-square-o"></i> Sacar
        </a>
    </legend>
    <input type="hidden" id="meta_sacar">
    <input type="hidden" id="pos_pedido" value="1">
    <div class="accordion" id="accordionExample">
        <ul class="list-group">
            @php
                $pos = 1;
                $count=1;
            @endphp
            @foreach($listado as $fecha)
                @if($count == 1)
                    <script>
                        cantidad_ramos_pedidos = 0;
                    </script>
                    <li class="list-group-item" style="border: 1px solid #9d9d9d"
                        onmouseover="$(this).css('background-color','#e9ecef')" onmouseleave="$(this).css('background-color','')">
                        <a href="javascript:void(0)" data-toggle="collapse" data-target="#div_content_fecha_pedido_{{$fecha->fecha_pedido}}"
                           aria-expanded="false" aria-controls="div_content_fecha_pedido_{{$fecha->fecha_pedido}}">
                            {{getDias()[transformDiaPhp(date('w', strtotime($fecha->fecha_pedido)))]}}
                            {{convertDateToText($fecha->fecha_pedido)}}
                        </a>

                        <span class="badge pull-right">
                            Destinados: {{getDestinadosToFrioByFecha($fecha->fecha_pedido, $variedad->id_variedad)}} -
                            Pedidos: <em id="html_cantidad_ramos_pedidos_{{$fecha->fecha_pedido}}">0</em>
                        </span>

                        <div id="div_content_fecha_pedido_{{$fecha->fecha_pedido}}" class="collapse"
                             aria-labelledby="div_header_fecha_pedido_{{$fecha->fecha_pedido}}" data-parent="#accordionExample">
                            <div class="well">
                                @if(count(getResumenPedidosByFecha($fecha->fecha_pedido, $variedad->id_variedad)) > 0 ||
                                    count(getResumenPedidosByFechaOfTallos($fecha->fecha_pedido, $variedad->id_variedad)))
                                    <ul style="padding: 0; list-style: none">
                                        @foreach(getResumenPedidosByFecha($fecha->fecha_pedido, $variedad->id_variedad) as $item)
                                            <li>
                                                <input type="checkbox" id="check_pedido_{{$pos}}" class="check_pedidos"
                                                       onchange="check_pedido('{{$pos}}')">
                                                <label for="check_pedido_{{$pos}}" class="mouse-hand">
                                                    {{$item->cantidad}} Ramos
                                                    de {{getCalibreRamoById($item->id_clasificacion_ramo)->nombre}}{{getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                                                    de {{$variedad->nombre}}
                                                </label>
                                                <input type="hidden" id="val_fecha_{{$pos}}" value="{{$fecha->fecha_pedido}}">
                                                <input type="hidden" id="val_ramo_{{$pos}}" value="{{$item->id_clasificacion_ramo}}">
                                                <input type="hidden" id="calibre_ramo_pedido_{{$pos}}"
                                                       value="{{getCalibreRamoById($item->id_clasificacion_ramo)->nombre}}">
                                                <input type="hidden" id="cantidad_ramos_pedidos_{{$pos}}"
                                                       value="{{$item->cantidad}}">
                                            </li>

                                            <script>
                                                factor = Math.round(($('#calibre_ramo_pedido_{{$pos}}').val() / $('#calibre_estandar').val()) * 100) / 100;
                                                conversion = $('#cantidad_ramos_pedidos_{{$pos}}').val() * factor;

                                                cantidad_ramos_pedidos += conversion;
                                            </script>
                                            @php
                                                $pos ++;
                                            @endphp
                                        @endforeach
                                        @foreach(getResumenPedidosByFechaOfTallos($fecha->fecha_pedido, $variedad->id_variedad) as $item)
                                            <li>
                                                <input type="checkbox" id="check_pedido_{{$pos}}" class="check_pedidos"
                                                       onchange="check_pedido('{{$pos}}')">
                                                <label for="check_pedido_{{$pos}}" class="mouse-hand">
                                                    {{$item->cantidad}} Ramos
                                                    de {{getCalibreRamoById($item->id_clasificacion_ramo)->nombre}}{{getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                                                    de {{$variedad->nombre}} de {{$item->tallos_x_ramos}} tallos c/u
                                                </label>
                                                <input type="hidden" id="val_fecha_{{$pos}}" value="{{$fecha->fecha_pedido}}">
                                                <input type="hidden" id="val_ramo_{{$pos}}" value="{{$item->id_clasificacion_ramo}}">
                                                <input type="hidden" id="calibre_ramo_pedido_{{$pos}}"
                                                       value="{{getCalibreRamoById($item->id_clasificacion_ramo)->nombre}}">
                                                <input type="hidden" id="cantidad_ramos_pedidos_{{$pos}}"
                                                       value="{{$item->cantidad}}">
                                            </li>

                                            <script>
                                                factor = Math.round(($('#calibre_ramo_pedido_{{$pos}}').val() / $('#calibre_estandar').val()) * 100) / 100;
                                                conversion = $('#cantidad_ramos_pedidos_{{$pos}}').val() * factor;

                                                cantidad_ramos_pedidos += conversion;
                                            </script>
                                            @php
                                                $pos ++;
                                            @endphp
                                        @endforeach
                                    </ul>
                                @else
                                    <small>
                                        No hay pedidos para {{$variedad->nombre}}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </li>
                    <script>
                        $('#html_cantidad_ramos_pedidos_{{$fecha->fecha_pedido}}').html(cantidad_ramos_pedidos);
                    </script>
                @endif
                @php
                    $count ++;
                @endphp
            @endforeach
        </ul>
    </div>

    <input type="hidden" id="calibre_estandar" value="{{getCalibreRamoEstandar()->nombre}}">
    <script>
        function check_pedido(pos) {
            $('.check_pedidos').prop('checked', false);
            $('#check_pedido_' + pos).prop('checked', true);

            factor = Math.round(($('#calibre_ramo_pedido_' + pos).val() / $('#calibre_estandar').val()) * 100) / 100;
            conversion = $('#cantidad_ramos_pedidos_' + pos).val() * factor;

            $('#html_current_pedido').html('Se necesitan: ' + conversion + ' Ramos');
            $('#meta_sacar').val(conversion);
            $('.input_sacar').prop('readonly', false);
            $('.checkbox_sacar').prop('disabled', false);

            $('#pos_pedido').val(pos);

            seleccionar_checkboxs('');
        }

        function sacar_aperturas() {
            listado = $('.checkbox_sacar');
            arreglo = [];

            for (i = 0; i < listado.length; i++) {
                if (listado[i].checked) {
                    id = listado[i].id.substr(15);
                    pos = $('#pos_pedido').val()
                    data = {
                        id_stock_apertura: id,
                        dias_maduracion: $('#dias_maduracion_' + id).val(),
                        cantidad_ramos_estandar: $('#sacar_' + id).val(),
                        fecha_pedido: $('#val_fecha_' + pos).val()
                    };
                    arreglo.push(data);
                }
            }

            datos = {
                _token: '{{csrf_token()}}',
                arreglo: arreglo
            };
            post_jquery('{{url('apertura/sacar')}}', datos, function () {
                buscar_listado();
                buscar_pedidos();
            });
        }
    </script>
@else
    <p class="text-center">
        No se han encontrado pedidos en el rango de tiempo especificado
    </p>
@endif