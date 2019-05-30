<legend class="text-center" style="font-size: 1.1em">
    <span class="badge" id="badge_cantidad_seleccionada"></span> {{$texto}}
</legend>
@if(count($listado)>0)
    <div id="div_listado_maduracion">
        <table class="table-striped table-responsive table-bordered" width="100%"
               style="border: 2px solid #9d9d9d; font-size: 0.8em; margin-bottom: 0" id="tabla_maduracion_inventarios">
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Fecha
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Días
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Cantidad
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2" width="30%">
                    Opciones
                </th>
            </tr>
            @foreach($listado as $pos_item => $item)
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$item->fecha_ingreso}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{difFechas(date('Y-m-d'),$item->fecha_ingreso)->days}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$item->cantidad}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="editar_inventario_{{$item->fecha_ingreso}}" style="width: 100%" value="0"
                               min="0" max="{{$item->cantidad}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <button type="button" class="btn btn-xs btn-primary" onclick="editar_inventario('{{$item->fecha_ingreso}}')">
                            <i class="fa fa-fw fa-edit"></i> Editar
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
        <div style="border-top: 1px dotted #9d9d9d; padding: 5px; margin-top: 2px" class="well" title="Opciones">
            <input type="checkbox" id="estrechar_tabla_maduracion" checked
                   onchange="estrechar_tabla('tabla_maduracion_inventarios', $(this).prop('checked'))">
            <label for="estrechar_tabla_maduracion" class="mouse-hand">Estrechar tabla</label>
        </div>
    </div>

    <div class="box-body" id="div_resto_inventarios" style="display: none;">
        <legend style="font-size: 1.1em; margin-bottom: 5px" class="text-center">
            <a href="javascript:void(0)" onclick="ocultar_mostrar_maduracion()"><i class="fa fa-fw fa-arrow-left"></i></a>
            Ingrese hacia donde desea destinar los ramos seleccionados
        </legend>
        <form id="form-update_inventario">
            <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                <tr>
                    <th style="border-color: #9d9d9d" class="text-center">Calibre</th>
                    <th style="border-color: #9d9d9d" class="text-center">Presentación</th>
                    <th style="border-color: #9d9d9d" class="text-center">Tallos</th>
                    <th style="border-color: #9d9d9d" class="text-center">Longitud</th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        Por Armar
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        Inventario
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        Cantidad
                    </th>
                </tr>
                @php
                    $pos_resto = 1;
                @endphp

                @foreach($resto as $item)
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{getCalibreRamoById($item['clasificacion_ramo'])->nombre.' '.getCalibreRamoById($item['clasificacion_ramo'])->unidad_medida->siglas}}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{getEmpaque($item['id_empaque_p'])->nombre}}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{$item['tallos_x_ramo']}}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            @if($item['longitud_ramo'] != '')
                                {{$item['longitud_ramo']}} {{getUnidadMedida($item['id_unidad_medida'])->siglas}}
                            @endif
                        </th>
                        @php
                            $por_armar = 0;
                            foreach($fechas as $fecha){
                                    $por_armar += getCantidadRamosPedidosForCB($fecha->fecha_pedido,$id_variedad,$item['clasificacion_ramo'],$item['id_empaque_p'],
                                    $item['tallos_x_ramo'],$item['longitud_ramo'],$item['id_unidad_medida']);
                            }
                        $saldo = $item['inventario'] - $por_armar;
                        @endphp
                        <td class="text-center" style="border-color: #9d9d9d">
                            @if($saldo >= 0)
                                <span class="badge bg-green" title="Armados">{{$saldo}}</span>
                            @else
                                <span class="badge bg-red" title="Por armar">{{substr($saldo,1)}}</span>
                            @endif
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$item['inventario']}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="number" id="destinar_inventario_{{$pos_resto}}" style="width: 100%" value="0"
                                   min="0" class="text-center">

                            <input type="hidden" id="texto_resto_{{$pos_resto}}" value="{{$item['texto']}}">
                            <input type="hidden" id="clasificacion_ramo_resto_{{$pos_resto}}" value="{{$item['clasificacion_ramo']}}">
                            <input type="hidden" id="tallos_x_ramo_resto_{{$pos_resto}}" value="{{$item['tallos_x_ramo']}}">
                            <input type="hidden" id="longitud_ramo_resto_{{$pos_resto}}" value="{{$item['longitud_ramo']}}">
                            {{--<input type="hidden" id="id_empaque_e_resto_{{$pos_resto}}" value="{{$item['id_empaque_e']}}">--}}
                            <input type="hidden" id="id_empaque_p_resto_{{$pos_resto}}" value="{{$item['id_empaque_p']}}">
                            <input type="hidden" id="id_unidad_medida_resto_{{$pos_resto}}" value="{{$item['id_unidad_medida']}}">
                            <input type="hidden" id="basura_resto_{{$pos_resto}}" value="0">

                            <input type="hidden" id="conversion_calibres_resto_{{$pos_resto}}"
                                   value="{{round(getCalibreRamoById($item['clasificacion_ramo'])->nombre / getCalibreRamoById($clasificacion_ramo)->nombre,2)}}">
                        </td>
                    </tr>
                    @php
                        $pos_resto++;
                    @endphp
                @endforeach
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d" colspan="5">
                        Basura
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d" colspan="2">
                        <input type="number" id="destinar_inventario_{{$pos_resto}}" style="width: 100%" value="0"
                               min="0" class="text-center" max="">

                        <input type="hidden" id="texto_resto_{{$pos_resto}}" value="{{$texto}}">
                        <input type="hidden" id="clasificacion_ramo_resto_{{$pos_resto}}" value="{{$clasificacion_ramo}}">
                        <input type="hidden" id="tallos_x_ramo_resto_{{$pos_resto}}" value="{{$tallos_x_ramo}}">
                        <input type="hidden" id="longitud_ramo_resto_{{$pos_resto}}" value="{{$longitud_ramo}}">
                        {{--<input type="hidden" id="id_empaque_e_resto_{{$pos_resto}}" value="{{$id_empaque_e}}">--}}
                        <input type="hidden" id="id_empaque_p_resto_{{$pos_resto}}" value="{{$id_empaque_p}}">
                        <input type="hidden" id="id_unidad_medida_resto_{{$pos_resto}}" value="{{$id_unidad_medida}}">
                        <input type="hidden" id="basura_resto_{{$pos_resto}}" value="1">
                    </th>
                </tr>
            </table>
            <input type="hidden" id="fecha_inventario_frio">
            <input type="hidden" id="editar_inventario_frio">
            <input type="hidden" id="pos_resto" value="{{$pos_resto}}">
            <div class="text-center">
                <button type="button" class="btn btn-xs btn-success" onclick="update_inventario('{{$pos_resto}}')">
                    <i class="fa fa-fw fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
    <script>
        function editar_inventario(fecha) {
            editar = parseFloat($('#editar_inventario_' + fecha).val());
            if (editar > 0 && editar != '' && editar <= $('#editar_inventario_' + fecha).prop('max')) {
                $('#div_resto_inventarios').show();
                $('#div_listado_maduracion').hide();
                $('#badge_cantidad_seleccionada').html('Cambiar ' + editar + ' ramos de');
                $('#fecha_inventario_frio').val(fecha);
                $('#editar_inventario_frio').val(editar);

                pos_resto = $('#pos_resto').val();
                for (i = 1; i <= pos_resto; i++) {
                    set_max_field(i);
                }
                $('#destinar_inventario_' + pos_resto).prop('max', editar);
            }
        }

        function ocultar_mostrar_maduracion() {
            $('#div_resto_inventarios').hide();
            $('#div_listado_maduracion').show();
            $('#badge_cantidad_seleccionada').html('');
        }

        function update_inventario(pos_resto) {
            if ($('#form-update_inventario').valid()) {
                arreglo = [];
                editar = 0;
                for (i = 1; i <= pos_resto; i++) {
                    if ($('#destinar_inventario_' + i).val() != '' && parseFloat($('#destinar_inventario_' + i).val()) > 0) {
                        data = {
                            editar: $('#destinar_inventario_' + i).val() != '' ? parseFloat($('#destinar_inventario_' + i).val()) : 0,
                            clasificacion_ramo: $('#clasificacion_ramo_resto_' + i).val(),
                            tallos_x_ramo: $('#tallos_x_ramo_resto_' + i).val(),
                            longitud_ramo: $('#longitud_ramo_resto_' + i).val(),
                            /*id_empaque_e: $('#id_empaque_e_resto_' + i).val(),*/
                            id_empaque_p: $('#id_empaque_p_resto_' + i).val(),
                            id_unidad_medida: $('#id_unidad_medida_resto_' + i).val(),
                            basura: $('#basura_resto_' + i).val(),
                            texto: $('#texto_resto_' + i).val()
                        };
                        editar += data['editar'];
                        arreglo.push(data);
                    }
                }
                if (editar > 0) {
                    datos = {
                        _token: '{{csrf_token()}}',
                        fecha_inventario_frio: $('#fecha_inventario_frio').val(),
                        id_variedad: $('#id_variedad').val(),
                        id_blanco: $('#id_blanco').val(),
                        editar: parseFloat($('#editar_inventario_frio').val()),
                        arreglo: arreglo,
                        clasificacion_ramo: $('#clasificacion_ramo_resto_' + pos_resto).val(),
                        tallos_x_ramo: $('#tallos_x_ramo_resto_' + pos_resto).val(),
                        longitud_ramo: $('#longitud_ramo_resto_' + pos_resto).val(),
                        id_empaque_p: $('#id_empaque_p_resto_' + pos_resto).val(),
                        id_unidad_medida: $('#id_unidad_medida_resto_' + pos_resto).val(),
                    };
                    post_jquery('{{url('clasificacion_blanco/update_inventario')}}', datos, function () {
                        cerrar_modals();
                        listar_clasificacion_blanco($('#id_variedad').val());
                    });
                }
            }
        }

        function set_max_field(pos_resto) {
            $('#destinar_inventario_' + pos_resto).prop('max', Math.round((parseInt($('#editar_inventario_frio').val() /
                parseFloat($('#conversion_calibres_resto_' + pos_resto).val()) * 100))) / 100);
        }
    </script>
@else
    <div class="well text-center" style="padding: 5px">
        No se han encontrado resultados
    </div>
@endif