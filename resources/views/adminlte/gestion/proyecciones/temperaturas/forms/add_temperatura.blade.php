<div class="input-group">
    <div class="input-group-addon bg-gray">
        Fecha
    </div>
    <input type="date" class="form-control text-center" id="fecha" value="{{isset($temperatura) ? $temperatura->fecha : date('Y-m-d')}}" required
           onchange="buscar_temperatura()">
</div>
<div class="input-group" style="margin-top: 10px">
    <div class="input-group-addon bg-gray">
        Mínima
    </div>
    <input type="number" class="form-control" id="minima" value="{{isset($temperatura) ? $temperatura->minima : 0}}" required>
    <div class="input-group-addon bg-gray">
        Máxima
    </div>
    <input type="number" class="form-control" id="maxima" value="{{isset($temperatura) ? $temperatura->maxima : 0}}" required>
    <div class="input-group-addon bg-gray">
        Lluvia
    </div>
    <input type="number" class="form-control" id="lluvia" value="{{isset($temperatura) ? $temperatura->lluvia : 0}}" required>
    <div class="input-group-btn">
        <button type="button" class="btn btn-success" onclick="store_temperatura()">
            <i class="fa fa-fw fa-save"></i>
        </button>
    </div>
</div>
<script>
    function store_temperatura() {
        datos = {
            _token: '{{csrf_token()}}',
            fecha: $('#fecha').val(),
            minima: $('#minima').val(),
            maxima: $('#maxima').val(),
            lluvia: $('#lluvia').val(),
        };
        $.LoadingOverlay('show');
        $.post('{{url('temperaturas/store_temperatura')}}', datos, function (retorno) {
            if (!retorno.success) {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }

    function buscar_temperatura() {
        datos = {
            _token: '{{csrf_token()}}',
            fecha: $('#fecha').val(),
        };
        $.LoadingOverlay('show');
        $.post('{{url('temperaturas/buscar_temperatura')}}', datos, function (retorno) {
            $('#maxima').val(0);
            $('#minima').val(0);
            $('#lluvia').val(0);
            if (retorno.temperatura != null) {
                $('#maxima').val(retorno.temperatura.maxima);
                $('#minima').val(retorno.temperatura.minima);
                $('#lluvia').val(retorno.temperatura.lluvia);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }
</script>