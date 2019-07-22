<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Detalles de clasificaciones por fechas
        </h3>
    </div>
    <div class="box-body">
        <table class="table-striped table-bordered table-hover" width="100%" style="border: 2px solid #9d9d9d">
            <tr>
                <th class="text-center" style="background-color: #357ca5; color: white; border-color: white">
                    Fechas
                </th>
                <th class="text-center" style="background-color: #357ca5; color: white; border-color: white">
                    Variedades
                </th>
                <th class="text-center" style="background-color: #357ca5; color: white; border-color: white">
                    Tallos x Calibre
                </th>
            </tr>
            @foreach($listado as $pos_f => $fecha)
                @foreach($fecha['variedades'] as $pos_v => $var)
                    <tr>
                        @if($pos_v == 0)
                            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef"
                                rowspan="{{count($fecha['variedades'])}}">
                                {{$fecha['fecha']}}
                            </th>
                        @endif
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{$var['variedad']->siglas}}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; padding: 0">
                            <table class="table-striped table-bordered" width="100%" style="border: 1px solid #9d9d9d; padding: 0">
                                <tr>
                                    @foreach($var['calibres'] as $cal)
                                        <th class="text-center"
                                            style="border-color: #9d9d9d; color: {{explode('|', getUnitaria($cal->id_clasificacion_unitaria)->color)[1]}};
                                                    background-color: {{explode('|', getUnitaria($cal->id_clasificacion_unitaria)->color)[0]}}">
                                            {{explode('|', getUnitaria($cal->id_clasificacion_unitaria)->nombre)[0]}}
                                            {{getUnitaria($cal->id_clasificacion_unitaria)->unidad_medida->siglas}}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($var['calibres'] as $cal)
                                        <th class="text-center"
                                            style="border-color: #9d9d9d">
                                            {{number_format($cal->cant)}}
                                        </th>
                                    @endforeach
                                </tr>
                            </table>
                        </th>
                    </tr>
                @endforeach
            @endforeach
        </table>
    </div>
</div>