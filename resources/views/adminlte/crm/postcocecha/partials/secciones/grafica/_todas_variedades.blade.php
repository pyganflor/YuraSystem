<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#cajas-chart" data-toggle="tab" aria-expanded="true">Cajas</a></li>
        <li class=""><a href="#tallos-chart" data-toggle="tab" aria-expanded="false">Tallos</a></li>
        {{--<li class=""><a href="#ramos-chart" data-toggle="tab" aria-expanded="false">Ramos</a></li>--}}
        <li class=""><a href="#calibres-chart" data-toggle="tab" aria-expanded="false">Calibres</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="cajas-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_cajas" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="tallos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_tallos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        {{--<div class="chart tab-pane" id="ramos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_ramos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>--}}
        <div class="chart tab-pane" id="calibres-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_calibres" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>

<script>
    construir_char_acumulado('Cajas', 'chart_acumulado_cajas');
    //construir_char_acumulado('Ramos', 'chart_acumulado_ramos');
    construir_char_acumulado('Tallos', 'chart_acumulado_tallos');
    construir_char_acumulado('Calibres', 'chart_acumulado_calibres');

    function construir_char_acumulado(label, id) {
        labels = [];
        datasets = [];
        @for($i = 0; $i < count($labels); $i++)
        @if($periodo == 'diario')
        labels.push("{{$labels[$i]->dia}}");
        @elseif($periodo == 'semanal')
        labels.push("{{$labels[$i]->semana}}");
        @elseif($periodo == 'anual')
        labels.push("{{$labels[$i]->ano}}");
        @else
        labels.push("{{getMeses(TP_ABREVIADO)[$labels[$i]->mes - 1]. ' - '.$labels[$i]->ano}}");
        @endif
                @endfor

                {{-- Data_list --}}
                @foreach($arreglo_variedades as $variedad)
            data_list = [];
        if (label == 'Cajas') {
            @foreach($variedad['cajas'] as $caja)
            data_list.push("{{$caja}}");
            @endforeach
        }
        /*else if (label == 'Ramos') {
            foreach($variedad['ramos'] as $ramos)
            data_list.push("{$ramos}}");
            endforeach
        }*/
        else if (label == 'Tallos') {
            @foreach($variedad['tallos'] as $tallos)
            data_list.push("{{$tallos}}");
            @endforeach
        }
        else {
            @foreach($variedad['calibre'] as $calibres)
            data_list.push("{{$calibres}}");
            @endforeach
        }

        datasets.push({
            label: '{{$variedad['variedad']->nombre}}' + ' ',
            data: data_list,
            backgroundColor: '{{$variedad['variedad']->color}}',
            borderColor: '{{$variedad['variedad']->color}}',
            borderWidth: 2,
            fill: false,
        });
        @endforeach

            ctx = document.getElementById(id).getContext('2d');
        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                elements: {
                    line: {
                        tension: 0, // disables bezier curves
                    }
                },
                tooltips: {
                    mode: 'point' // nearest, point, index, dataset, x, y
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    fullWidth: false,
                    onClick: function () {
                    },
                    onHover: function () {
                    },
                    reverse: true,
                },
                showLines: true, // for all datasets
                borderCapStyle: 'round',    // "butt" || "round" || "square"
            }
        });
    }
</script>
