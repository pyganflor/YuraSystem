<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Rendimiento de <span class="badge">{{$blanco->personal}}</span> personas en
            <span class="badge">{{$blanco->getCantidadHorasTrabajo()}}</span> horas de trabajo:
            <span class="badge">{{$blanco->getRendimiento()}}</span> ramos por persona/hora.
            Desecho: <span class="badge">{{$blanco->getDesecho()}} %</span>
        </h3>
    </div>
    <div class="box-body">
        @if($blanco->personal > 0)
            <div class="nav-tabs-custom" style="cursor: move;">
                <!-- Tabs within a box -->
                <ul class="nav nav-pills nav-justified">
                    <li class="active"><a href="#rendimiento_horas" data-toggle="tab" aria-expanded="true">Rendimiento por horas</a></li>
                    <li class=""><a href="#rendimiento_ingresos" data-toggle="tab" aria-expanded="false">Gráfica</a></li>
                </ul>
                <div class="tab-content no-padding">
                    <div class="chart tab-pane active" id="rendimiento_horas" style="position: relative">
                        @include('adminlte.gestion.postcocecha.clasificacion_blanco.partials._rendimiento_horas')
                    </div>
                    <div class="chart tab-pane" id="rendimiento_ingresos" style="position: relative">
                        @include('adminlte.gestion.postcocecha.clasificacion_blanco.partials._rendimiento_grafica')
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info text-center">
                <h3>La cantidad de trabajadores no puede ser 0.</h3>
            </div>
        @endif
    </div>
</div>


<script>
    function mostrar_ocultar_rendimiento() {
        if ($('#check_mostrar_ocultar_rendimiento').prop('checked')) {
            $('#check_mostrar_ocultar_rendimiento').prop('checked', false);
            $('.elemento_horas').hide();
            $('#th_total_rendimiento').prop('colspan', 1)
        } else {
            $('#check_mostrar_ocultar_rendimiento').prop('checked', true);
            $('.elemento_horas').show();
            $('#th_total_rendimiento').prop('colspan', 8)
        }
    }
</script>