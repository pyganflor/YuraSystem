<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Etapas del lote
        </h3>
    </div>
    <div class="box-body">
        <ul class="timeline">
            {{-- Guarde despues de clasificación --}}
            @include('adminlte.gestion.postcocecha.lotes.partials.etapas.guarde_clasificacion')
            {{-- Apertura --}}
            @include('adminlte.gestion.postcocecha.lotes.partials.etapas.apertura')
            {{-- Guarde después de apertura --}}
            <li class="time-label">
                  <span class="bg-red">
                    10 Feb. 2014
                  </span>
            </li>
            <li>
                <i class="fa fa-envelope bg-blue"></i>

                <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                    <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                    <div class="timeline-body">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                        weebly ning heekya handango imeem plugg dopplr jibjab, movity
                        jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                        quora plaxo ideeli hulu weebly balihoo...
                    </div>
                    <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Read more</a>
                        <a class="btn btn-danger btn-xs">Delete</a>
                    </div>
                </div>
            </li>
            {{-- Empaquetado --}}
            <li class="time-label">
                  <span class="bg-red">
                    10 Feb. 2014
                  </span>
            </li>
            <li>
                <i class="fa fa-envelope bg-blue"></i>

                <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                    <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                    <div class="timeline-body">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                        weebly ning heekya handango imeem plugg dopplr jibjab, movity
                        jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                        quora plaxo ideeli hulu weebly balihoo...
                    </div>
                    <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Read more</a>
                        <a class="btn btn-danger btn-xs">Delete</a>
                    </div>
                </div>
            </li>
            <li>
                <i class="fa fa-clock-o bg-gray"></i>
            </li>
        </ul>
    </div>
</div>

<script>
    function store_etapa(etapa) {
        if ($('#form-etapa_' + etapa).valid()) {
            if (etapa == 'E') { // etapa empaquetado
                datos = {
                    _token: '{{csrf_token()}}',
                    fecha: $('#fecha_' + etapa).val(),
                    id_lote_re: $('#id_lote_re').val(),
                    etapa: etapa,
                };
            } else {
                datos = {
                    _token: '{{csrf_token()}}',
                    dias: parseInt($('#dias_' + etapa).val()),
                    fecha: $('#fecha_' + etapa).val(),
                    id_lote_re: $('#id_lote_re').val(),
                    etapa: etapa,
                };
            }
            post_jquery('{{url('lotes/store_etapa')}}', datos, function () {
                cerrar_modals();
                ver_lote(datos['id_lote_re']);
                buscar_listado();
            });
        }
    }
</script>