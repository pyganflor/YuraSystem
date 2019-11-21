<div class="nav-tabs-custom">
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Gráfica</a></li>
        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Tabla</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <canvas id="chart" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane" id="tab_2">
            {{--<table class="table-striped table-responsive table-bordered" width="100%" style="border: 2px solid #9d9d9d">
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        Variedad
                    </th>
                    @php
                        $totales_dia = [];
                    @endphp
                    @foreach($labels as $pos => $f)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{$f->dia}}
                        </th>
                        @php
                            $totales_dia[$pos] = 0;
                        @endphp
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        @if($option == 'valor' || $option == 'cajas')
                            Total
                        @else
                            Promedio
                        @endif
                    </th>
                </tr>
                @foreach($arreglo_variedades as $pos_v => $v)
                    <tr style="color: {{$v['variedad']->color}}">
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{$v['variedad']->nombre}}
                        </th>
                        @php
                            if($option == 'valor')
                                $array = $v['valores'];
                            if($option == 'cajas')
                                $array = $v['cajas'];
                            if($option == 'precios')
                                $array = $v['precios'];
                            if($option == 'tallos')
                                $array = $v['tallos'];

                            $total_var = 0;
                        @endphp
                        @foreach($array as $pos => $valor)
                            <th class="text-center" style="border-color: #9d9d9d">
                                {{$valor}}
                            </th>
                            @php
                                $totales_dia[$pos] += $valor;
                                $total_var += $valor;
                            @endphp
                        @endforeach
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            @if($option == 'valor' || $option == 'cajas')
                                {{number_format($total_var, 2)}}
                            @else
                                {{round($total_var / count($labels), 2)}}
                            @endif
                        </th>
                    </tr>
                @endforeach
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        @if($option == 'valor' || $option == 'cajas')
                            Total
                        @else
                            Promedio
                        @endif
                    </th>
                    @php
                        $total = 0;
                    @endphp
                    @foreach($totales_dia as $pos => $valor)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            @if($option == 'valor' || $option == 'cajas')
                                {{number_format($valor, 2)}}
                                @php
                                    $total += $valor;
                                @endphp
                            @else
                                {{round($valor / count($arreglo_variedades), 2)}}
                                @php
                                    $total += round($valor / count($arreglo_variedades), 2);
                                @endphp
                            @endif
                        </th>
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        @if($option == 'valor' || $option == 'cajas')
                            {{number_format($total, 2)}}*
                        @else
                            {{round($total / count($totales_dia), 2)}}*
                        @endif
                    </th>
                </tr>
            </table>--}}
        </div>
    </div>
</div>

<script src="{{asset('Flot/jquery.flot.js')}}"></script>
<script src="{{asset('Flot/jquery.flot.resize.js')}}"></script>

<script>

    var sin = [], cos = []
    for (var i = 0; i < 14; i += 0.5) {
        sin.push([i, Math.sin(i)])
        cos.push([i, Math.cos(i)])
    }
    var line_data1 = {
        data : sin,
        color: '#3c8dbc'
    }
    var line_data2 = {
        data : cos,
        color: '#00c0ef'
    }
    $.plot('#chart', [line_data1, line_data2], {
        grid  : {
            hoverable  : true,
            borderColor: '#f3f3f3',
            borderWidth: 1,
            tickColor  : '#f3f3f3'
        },
        series: {
            shadowSize: 0,
            lines     : {
                show: true
            },
            points    : {
                show: true
            }
        },
        lines : {
            fill : false,
            color: ['#3c8dbc', '#f56954']
        },
        yaxis : {
            show: true
        },
        xaxis : {
            show: true
        }
    })
    //Initialize tooltip on hover
    $('<div class="tooltip-inner" id="line-chart-tooltip"></div>').css({
        position: 'absolute',
        display : 'none',
        opacity : 0.8
    }).appendTo('body')
    $('#line-chart').bind('plothover', function (event, pos, item) {

        if (item) {
            var x = item.datapoint[0].toFixed(2),
                y = item.datapoint[1].toFixed(2)

            $('#line-chart-tooltip').html(item.series.label + ' of ' + x + ' = ' + y)
                .css({ top: item.pageY + 5, left: item.pageX + 5 })
                .fadeIn(200)
        } else {
            $('#line-chart-tooltip').hide()
        }

    })
</script>
