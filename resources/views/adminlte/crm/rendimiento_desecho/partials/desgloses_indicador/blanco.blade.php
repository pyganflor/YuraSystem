<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            <strong>Filtro:</strong>

            <select name="filtro_desglose_tipo" id="filtro_desglose_tipo" style="height: 30px;"
                    onchange="filtrar_desglose_indicador()">
                <option value="R" {{isset($criterio_tipo) && $criterio_tipo == 'R' ? 'selected' : ''}}>Rendimiento</option>
                <option value="D" {{isset($criterio_tipo) && $criterio_tipo == 'D' ? 'selected' : ''}}>Desecho</option>
            </select>

            <select name="filtro_desglose_variedad" id="filtro_desglose_variedad" style="height: 30px;"
                    onchange="filtrar_desglose_indicador()">
                <option value="">Todas las variedades</option>
                @foreach(getVariedades() as $v)
                    <option value="{{$v->id_variedad}}" {{isset($id_variedad) && $id_variedad == $v->id_variedad ? 'selected' : ''}}>{{$v->nombre}}</option>
                @endforeach
            </select>

            <select name="filtro_desglose_criterio" id="filtro_desglose_criterio" style="height: 30px;"
                    onchange="filtrar_desglose_indicador()">
                <option value="1" {{isset($criterio_desglose) && $criterio_desglose == 1 ? 'selected' : ''}}>Mostrar por Horarios</option>
                <option value="2" {{isset($criterio_desglose) && $criterio_desglose == 2 ? 'selected' : ''}}>Mostrar por Días</option>
            </select>
        </h3>
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-pills nav-justified">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Gráfica</a></li>
                <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Tabla</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <canvas id="chart_desglose" width="100%" height="33" style="margin-top: 5px"></canvas>
                </div>
                <div class="tab-pane" id="tab_2">
                    @if($criterio_desglose == 1)
                        @include('adminlte.crm.rendimiento_desecho.partials.desgloses_indicador._tbl_horario')
                    @else
                        @include('adminlte.crm.rendimiento_desecho.partials.desgloses_indicador._tbl_diario')
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    @if($criterio_desglose == 1)
    chart_desgloses_x_hora();
    @else
    chart_desgloses_x_dias();

    @endif

    function chart_desgloses_x_hora() {
        labels = [];
        datasets = [];
        @for($i = 0; $i < 24; $i++)
        labels.push("{{getIntervalosHorasDiarias()[$i]['inicio'].'-'.getIntervalosHorasDiarias()[$i]['fin']}}");
        @endfor

                {{-- Data_list --}}
                @foreach($arreglo_horarios as $pos_a => $a)
            data_list = [];
        @foreach($a['arreglo'] as $item)
        data_list.push("{{$item['valor']}}");
        @endforeach

        datasets.push({
            label: '{{$a['fecha']}}' + ' ',
            data: data_list,
            backgroundColor: '{{getListColores()[$pos_a]}}',
            borderColor: '{{getListColores()[$pos_a]}}',
            borderWidth: 1,
            fill: false,
        });
        @endforeach

            ctx = document.getElementById("chart_desglose").getContext('2d');
        myChart = new Chart(ctx, {
            type: 'line',
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

    function chart_desgloses_x_dias() {
        labels = [];
        datasets = [];
        data_list = [];
        @for($i = 0; $i < 7; $i++)
        labels.push("{{$fechas[$i]}}");
        @endfor

        @foreach($arreglo_dias as $pos_a => $a)
        data_list.push("{{$a}}");
        @endforeach

            label = '';
        if ($('#filtro_desglose_tipo').val() == 'R')
            label = 'Rendimiento';
        if ($('#filtro_desglose_tipo').val() == 'D')
            label = 'Desecho';

        datasets.push({
            label: label + ' ',
            data: data_list,
            backgroundColor: 'black',
            borderColor: 'black',
            borderWidth: 1,
            fill: false,
        });

        ctx = document.getElementById("chart_desglose").getContext('2d');
        myChart = new Chart(ctx, {
            type: 'line',
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

    function filtrar_desglose_indicador() {
        if ($('#filtro_desglose_tipo').val() == 'D')
            $('#filtro_desglose_criterio').val(2);
        datos = {
            option: 'blanco',
            criterio_tipo: $('#filtro_desglose_tipo').val(),
            criterio_desglose: $('#filtro_desglose_criterio').val(),
            id_variedad: $('#filtro_desglose_variedad').val(),
        };
        cerrar_modals();
        get_jquery('{{url('crm_rendimiento/desglose_indicador')}}', datos, function (retorno) {
            modal_view('modal_view_desglose_indicador', retorno, '<i class="fa fa-fw fa-bar-chart"></i> Desglose', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }
</script>