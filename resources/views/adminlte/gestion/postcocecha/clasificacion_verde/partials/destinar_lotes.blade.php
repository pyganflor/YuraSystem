<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Producción de <em class="badge">{{$clasificacion->tallos_x_variedad($variedad->id_variedad)}}</em> tallos de
            <strong>{{$variedad->planta->nombre}} - {{$variedad->nombre}}</strong>
            <em class="badge">{{$clasificacion->fecha_ingreso}}</em>
        </h3>
        <input type="hidden" id="terminar_calsificacion_verde" value="{{$clasificacion->activo}}">

    </div>
    <input type="hidden" id="id_variedad" value="{{$variedad->id_variedad}}">
    <input type="hidden" id="fecha_ingreso" value="{{$clasificacion->fecha_ingreso}}">
    <div class="box-body">
        <form id="form-add_lote_re">
            <table class="table table-bordered table-responsive sombra_estandar" width="100%" style="font-size: 0.8em">
                <tr>
                    <th style="border-color: #9d9d9d" colspan="4"
                        class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        Clasificaciones
                    </th>
                    <th style="border-color: #9d9d9d"
                        class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        Disponibles
                    </th>
                    <th style="border-color: #9d9d9d"
                        class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        Stock
                    </th>
                </tr>
                @foreach(getUnitarias() as $unitaria)
                    @if($clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria) > 0)
                        <tr>
                            <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center" width="12%">
                                    <span class="badge" id="badge_tallos_x_unitaria_{{$unitaria->id_clasificacion_unitaria}}">
                                        {{$clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}
                                    </span>
                                {{explode('|',$unitaria->nombre)[0]}}{{$unitaria->unidad_medida->siglas}}
                                <input type="hidden" id="tallos_x_unitaria_{{$unitaria->id_clasificacion_unitaria}}"
                                       name="tallos_x_unitaria_{{$unitaria->id_clasificacion_unitaria}}"
                                       value="{{$clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}">
                            </th>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center" width="12%">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Días</span>
                                    <input type="number" name="dias_{{$unitaria->id_clasificacion_unitaria}}"
                                           min="{{$variedad->minimo_apertura}}" max="{{$variedad->maximo_apertura}}"
                                           id="dias_{{$unitaria->id_clasificacion_unitaria}}"
                                           class="form-control" required value="{{$variedad->estandar_apertura}}"
                                           onkeypress="return isNumber(event)"
                                           onchange="calcular_stock('{{$unitaria->id_clasificacion_unitaria}}')">
                                </div>
                            </td>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Apertura</span>
                                    <input type="number" name="apertura_{{$unitaria->id_clasificacion_unitaria}}" min="0"
                                           max="{{$clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}"
                                           id="apertura_{{$unitaria->id_clasificacion_unitaria}}" class="form-control" required
                                           value="{{$clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}"
                                           onchange="calcular_stock('{{$unitaria->id_clasificacion_unitaria}}')"
                                           onkeypress="return isNumber(event)">
                                </div>
                            </td>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Guarde</span>
                                    <input type="number" name="guarde_{{$unitaria->id_clasificacion_unitaria}}" min="0"
                                           max="{{$clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}"
                                           id="guarde_{{$unitaria->id_clasificacion_unitaria}}" class="form-control" required
                                           value="0" onkeypress="return isNumber(event)">
                                </div>
                            </td>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center" width="20%">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                <span class="input-group-addon" style="background-color: #357ca5; color: white"
                                      id="fecha_disponible_{{$unitaria->id_clasificacion_unitaria}}">
                                </span>
                                    <input type="text" class="form-control" id="disponible_{{$unitaria->id_clasificacion_unitaria}}" readonly
                                           name="disponible_{{$unitaria->id_clasificacion_unitaria}}"
                                           value="{{getStockToFecha($variedad->id_variedad, $unitaria->id_clasificacion_unitaria, $clasificacion->fecha_ingreso, $variedad->estandar_apertura) +
                                       $clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}">
                                </div>
                            </td>
                            <td style="border-color: #9d9d9d; padding: 0" class="text-center" width="20%">
                                <div class="form-group input-group" style="margin-bottom: 0">
                                    <span class="input-group-addon" style="background-color: #357ca5; color: white">Stock</span>
                                    <input type="text" class="form-control" id="stock_{{$unitaria->id_clasificacion_unitaria}}" readonly
                                           name="stock_{{$unitaria->id_clasificacion_unitaria}}"
                                           value="{{getStock($variedad->id_variedad, $unitaria->id_clasificacion_unitaria, $variedad->estandar_apertura) +
                                       $clasificacion->getTallosByvariedadUnitaria($variedad->id_variedad, $unitaria->id_clasificacion_unitaria)}}">
                                </div>
                            </td>
                        </tr>
                        <input type="hidden" class="ids_unitaria" id="id_clasificacion_unitaria_{{$unitaria->id_clasificacion_unitaria}}"
                               value="{{$unitaria->id_clasificacion_unitaria}}">
                        <script>
                            //calcular_stock('{{$unitaria->id_clasificacion_unitaria}}');
                        </script>
                    @endif
                @endforeach
            </table>
        </form>
        <div class="text-center" style="margin-top: 10px">
            <button type="button" class="btn btn-sm btn-success" onclick="store_lote_re()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        </div>
    </div>
</div>
<input type="hidden" id="id_clasificacion_verde" value="{{$clasificacion->id_clasificacion_verde}}">
<script>
    function store_lote_re() {
        if ($('#form-add_lote_re').valid()) {
            unitarias = $('.ids_unitaria');

            arreglo = [];
            success = true;

            for (i = 0; i < unitarias.length; i++) {
                total_tallos = parseInt($('#tallos_x_unitaria_' + unitarias[i].value).val());
                apertura = parseInt($('#apertura_' + unitarias[i].value).val());
                guarde = parseInt($('#guarde_' + unitarias[i].value).val());
                dias = parseInt($('#dias_' + unitarias[i].value).val());

                if ((apertura + guarde) != total_tallos) {
                    $('#apertura_' + unitarias[i].value).addClass('error');
                    $('#guarde_' + unitarias[i].value).addClass('error');
                    $('#badge_tallos_x_unitaria_' + unitarias[i].value).addClass('error');
                    success = false;
                } else {
                    $('#apertura_' + unitarias[i].value).removeClass('error');
                    $('#guarde_' + unitarias[i].value).removeClass('error');
                    $('#badge_tallos_x_unitaria_' + unitarias[i].value).removeClass('error');

                    lote = {
                        id_clasificacion_unitaria: unitarias[i].value,
                        dias: dias,
                        apertura: apertura,
                        guarde: guarde,
                    };

                    arreglo.push(lote);
                }
            }
            if (success) {
                if ($('#terminar_calsificacion_verde').val() == 0)
                    terminar = 1;
                else
                    terminar = 0;

                datos = {
                    _token: '{{csrf_token()}}',
                    fecha: $('#fecha_ingreso').val(),
                    id_variedad: $('#id_variedad').val(),
                    id_clasificacion_verde: $('#id_clasificacion_verde').val(),
                    arreglo: arreglo,
                    terminar: terminar
                };

                if ($('#terminar_calsificacion_verde').val() == 1)
                    modal_quest('modal_quest_terminar_clasificacion',
                        '<div class="alert alert-info text-center">Al destinar esta clasificación en verde no podrá volver a clasificar más ramos en la fecha seleccionada</div>',
                        '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '{{isPc() ? '35%' : ''}}', function () {
                            post_jquery('{{url('clasificacion_verde/store_lote_re')}}', datos, function () {
                                buscar_listado();
                                ver_lotes($('#id_variedad').val(), $('#id_clasificacion_verde').val());
                                cerrar_modals();
                            });
                        });
                else {
                    post_jquery('{{url('clasificacion_verde/store_lote_re')}}', datos, function () {
                        buscar_listado();
                        ver_lotes($('#id_variedad').val(), $('#id_clasificacion_verde').val());
                        cerrar_modals();
                    });
                }
            } else {
                alerta('<p class="text-center">Debe distribuir la cantidad exacta de tallos entre Apertura y Guarde</p>');
            }
        } else {
            alerta('<p class="text-center">Faltan datos en el formulario</p>');
        }
    }
</script>
