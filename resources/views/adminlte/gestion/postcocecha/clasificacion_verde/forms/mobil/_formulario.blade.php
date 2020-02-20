@if($verde != '' && $cosecha != '')
    <table class="table-striped table-bordered" style="border: 2px solid #9d9d9d" width="100%" id="table_formulario">
        <thead>
        <tr>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d; width: 120px">
                <select name="variedad_form" id="variedad_form" style="width: 100%">
                    @foreach($cosecha->getVariedades() as $v)
                        <option value="{{$v->id_variedad}}">{{getVariedad($v->id_variedad)->siglas}}</option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Calibre
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Ramos
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Tallos x Ramo
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Total
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d; width: 20px">
                <a href="javascript:void(0)" onclick="$('#div_form_verde').toggleClass('hide')"
                   class="pull-right text-black btn btn-xs btn-default">
                    <i class="fa fa-fw fa-ellipsis-v"></i>
                </a>
            </th>
        </tr>
        </thead>
        <tbody id="body_tabla_formulario"></tbody>
        @if($verde->activo == 1)
            <tfoter>
                <tr>
                    <th colspan="6" class="text-center" style="border-color: #9d9d9d">
                        <button type="button" class="btn btn-xs btn-block btn-primary" onclick="store_detalle_verde()">
                            <i class="fa fa-fw fa-save"></i> Guardar
                        </button>
                    </th>
                </tr>
            </tfoter>
        @endif
    </table>

    @if($verde->activo == 1)
        <div id="btn_terminar_clasificacion" style="margin-top: 15px">
            <button type="button" class="btn btn-danger btn-sm" onclick="terminar_clasificacion()">
                <i class="fa fa-fw fa-times"></i> Terminar Clasificaci�n
            </button>
        </div>
        @foreach($verde->variedades() as $variedad)
            <div id="div_destinar_lotes_{{$variedad->id_variedad}}" style="display: none;"></div>
            <script>
                destinar_lotes_form('{{$variedad->id_variedad}}', '{{$verde->id_clasificacion_verde}}');
            </script>
        @endforeach
    @endif

    <script>
        $('#personal').val('{{$verde->personal}}');
        $('#hora_inicio').val('{{$verde->hora_inicio}}');
        $('#id_clasificacion_verde').val('{{$verde->id_clasificacion_verde}}');
        construir_tabla();

        function store_detalle_verde() {
            list_pos = $('.pos_c');
            array_data = [];
            for (i = 0; i < list_pos.length; i++) {
                pos = list_pos[i].value;
                array_data.push({
                    mesa: $('#mesa_' + pos).val(),
                    unitaria: $('#id_unitaria_' + pos).val(),
                    ramos: $('#ramos_' + pos).val(),
                    tallos_x_ramo: $('#tallos_x_ramo_' + pos).val(),
                });
            }

            datos = {
                _token: '{{csrf_token()}}',
                verde: $('#id_clasificacion_verde').val(),
                variedad: $('#variedad_form').val(),
                fecha_recepciones: $('#fecha_recepciones').val(),
                array_data: array_data
            };

            post_jquery('{{url('clasificacion_verde/store_detalle_verde')}}', datos, function (retorno) {
                select_fecha_recepciones();
                buscar_listado();
            }, 'div_formulario');
        }
    </script>
@else
    <div class="well text-center">
        No se ha encontrado ningún trabajo realizado en la fecha seleccionada
        <a href="javascript:void(0)" onclick="$('#div_form_verde').toggleClass('hide')" class="pull-right text-black btn btn-xs btn-default">
            <i class="fa fa-fw fa-ellipsis-v"></i>
        </a>
    </div>

    <script>
        $('#personal').val('');
        $('#hora_inicio').val('');
    </script>
@endif