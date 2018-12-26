<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Producción de <em class="badge">{{$clasificacion->tallos_x_variedad($variedad->id_variedad)}}</em> tallos de
            <strong>{{$variedad->planta->nombre}} - {{$variedad->nombre}}</strong>
            <em class="badge">{{$clasificacion->fecha_ingreso}}</em>
        </h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-responsive sombra_estandar" width="100%" style="font-size: 0.8em">
            <tr>
                <th style="border-color: #9d9d9d" colspan="3"
                    class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                    Descripción
                </th>
                <th style="border-color: #9d9d9d"
                    class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                    Disponible
                </th>
                <th style="border-color: #9d9d9d"
                    class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                    Stock
                </th>
            </tr>
            @foreach($clasificacion->lotes_reByVariedad($variedad->id_variedad) as $lote)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                    <th width="10%" class="text-center" style="border-color: #9d9d9d">
                        <span class="badge" title="Tallos">{{$lote->cantidad_tallos}}</span>
                        {{explode('|',$lote->clasificacion_unitaria->nombre)[0]}}
                        {{$lote->clasificacion_unitaria->unidad_medida->siglas}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        @php
                            $dias = 0;
                            $fecha = $lote->clasificacion_verde->fecha_ingreso;
                        @endphp
                        @if($lote->etapa == 'A')
                            {{$lote->stock_apertura->dias}} días
                            @php
                                $dias = $lote->stock_apertura->dias;
                                $fecha = $lote->apertura;
                            @endphp
                        @elseif($lote->etapa == 'C')
                            {{$lote->dias_guarde_clasificacion}} días
                            @php
                                $dias = $lote->dias_guarde_clasificacion;
                                $fecha = $lote->guarde_clasificacion;
                            @endphp
                        @elseif($lote->etapa == 'G')
                            {{$lote->dias_guarde_apertura}} días
                            @php
                                $dias = $lote->dias_guarde_apertura;
                                $fecha = $lote->guarde_apertura;
                            @endphp
                        @endif
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        @if($lote->etapa == 'A')
                            Apertura
                        @elseif($lote->etapa == 'C')
                            Guarde (clasificación)
                        @elseif($lote->etapa == 'G')
                            Guarde (apertura)
                        @elseif($lote->etapa == 'E')
                            Empaquetado
                        @endif
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; padding: 0" width="25%">
                        <div class="form-group input-group" style="margin-bottom: 0">
                            <span class="input-group-addon" style="background-color: #e9ecef">
                                @php
                                    $disponible = strtotime('+' . $dias . ' day', strtotime($fecha));
                                    $disponible = date('Y-m-d', $disponible);
                                @endphp
                                {{$disponible}}
                            </span>
                            <input type="text" class="form-control text-center" readonly
                                   value="{{$lote->etapa == 'A' ?
                                   getStockToFecha($lote->id_variedad, $lote->id_clasificacion_unitaria, $fecha, $dias) : '-'}}">
                        </div>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; padding: 0" width="25%">
                        <div class="form-group input-group" style="margin-bottom: 0">
                            <span class="input-group-addon" style="background-color: #e9ecef">Stock</span>
                            <input type="text" class="form-control text-center" readonly
                                   value="{{$lote->etapa == 'A' ? getStock($lote->id_variedad, $lote->id_clasificacion_unitaria) : '-'}}">
                        </div>
                    </th>
                </tr>
            @endforeach
        </table>
    </div>
</div>
<input type="hidden" id="id_clasificacion_verde" value="{{$lote->id_clasificacion_verde}}">
<input type="hidden" id="id_variedad" value="{{$variedad->id_variedad}}">
<script>
    function destinar_a(id_lote_re, etapa) {
        if (etapa != 'G' && etapa != 'E') {
            if (etapa == 'A') {
                etapa = 'G';
                texto = 'Guarde (apertura)';
            } else {
                etapa = 'A';
                texto = 'Apertura';
            }
            datos = {
                _token: '{{csrf_token()}}',
                id_lote_re: id_lote_re,
                etapa: etapa
            };
            modal_quest('modal_quest_destinar_a', '<div class="alert alert-info text-center">¿Está seguro de enviar este lote a ' + texto + '?</div>',
                '<i class="fa fa-fw fa-exchange"></i> Enviar lote a', true, false, '{{isPC() ? '35%' : ''}}', function () {
                    post_jquery('{{url('clasificacion_verde/destinar_a')}}', datos, function () {
                        ver_lotes($('#id_variedad').val(), $('#id_clasificacion_verde').val());
                        cerrar_modals();
                    });
                });
        }
    }

</script>