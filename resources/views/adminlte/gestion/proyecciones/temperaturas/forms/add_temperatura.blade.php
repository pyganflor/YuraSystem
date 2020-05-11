<div id="div_formularios">
    <div id="div_form_1">
        <div class="input-group">
            <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                Fecha
            </div>
            <input type="date" class="form-control text-center input-yura_white" id="fecha1"
                   value="{{isset($temperatura) ? $temperatura->fecha : date('Y-m-d')}}"
                   required onchange="buscar_temperatura(1)">
            <div class="input-group-btn">
                <button type="button" class="btn btn-yura_dark" onclick="add_formulario()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="input-group" style="margin-top: 5px">
            <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                Mínima
            </div>
            <input type="number" class="form-control input-yura_white" id="minima1" value="{{isset($temperatura) ? $temperatura->minima : 0}}"
                   required>
            <div class="input-group-addon bg-yura_dark span-input-group-yura-middle">
                Máxima
            </div>
            <input type="number" class="form-control input-yura_white" id="maxima1" value="{{isset($temperatura) ? $temperatura->maxima : 0}}"
                   required>
            <div class="input-group-addon bg-yura_dark span-input-group-yura-middle">
                Lluvia
            </div>
            <input type="number" class="form-control input-yura_white" id="lluvia1" value="{{isset($temperatura) ? $temperatura->lluvia : 0}}"
                   required>
            <div class="input-group-btn">
                <button type="button" class="btn btn-yura_primary" onclick="store_temperatura(1)">
                    <i class="fa fa-fw fa-save"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="text-center" style="margin-top: 10px">
    <button class="btn btn-md btn-yura_primary" onclick="store_all_temperatura()">
        <i class="fa fa-fw fa-check"></i> Guardar
    </button>
</div>
<script>
    var num_form = 1;

    function store_temperatura(form) {
        datos = {
            _token: '{{csrf_token()}}',
            fecha: $('#fecha' + form).val(),
            minima: $('#minima' + form).val(),
            maxima: $('#maxima' + form).val(),
            lluvia: $('#lluvia' + form).val(),
        };
        $('#div_form_' + form).LoadingOverlay('show');
        $.post('{{url('temperaturas/store_temperatura')}}', datos, function (retorno) {
            if (!retorno.success) {
                alerta(retorno.mensaje)
            } else {
                listar_temperaturas();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#div_form_' + form).LoadingOverlay('hide');
        })
    }

    function buscar_temperatura(form) {
        datos = {
            _token: '{{csrf_token()}}',
            fecha: $('#fecha' + form).val(),
        };
        $.LoadingOverlay('show');
        $.post('{{url('temperaturas/buscar_temperatura')}}', datos, function (retorno) {
            $('#maxima' + form).val(0);
            $('#minima' + form).val(0);
            $('#lluvia' + form).val(0);
            if (retorno.temperatura != null) {
                $('#maxima' + form).val(retorno.temperatura.maxima);
                $('#minima' + form).val(retorno.temperatura.minima);
                $('#lluvia' + form).val(retorno.temperatura.lluvia);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }

    function add_formulario() {
        num_form++;
        next_fecha = sum_dias_a_fecha(num_form, $('#fecha1').val());
        $('#div_formularios').append('<legend style="margin-top: 10px; margin-bottom: 10px"></legend>' +
            '<div id="div_form_' + num_form + '">' +
            '<div class="input-group">' +
            '<div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">' +
            'Fecha' +
            '</div>' +
            '<input type="date" class="form-control text-center input-yura_white" id="fecha' + num_form + '" required onchange="buscar_temperatura(' + num_form + ')"' +
            ' value="' + next_fecha + '">' +
            '</div>' +
            '<div class="input-group" style="margin-top: 5px">' +
            '<div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">' +
            'Mínima' +
            '</div>' +
            '<input type="number" class="form-control input-yura_white" id="minima' + num_form + '" required>' +
            '<div class="input-group-addon bg-yura_dark span-input-group-yura-middle">' +
            'Máxima' +
            '</div>' +
            '<input type="number" class="form-control input-yura_white" id="maxima' + num_form + '" required>' +
            '<div class="input-group-addon bg-yura_dark span-input-group-yura-middle">' +
            'Lluvia' +
            '</div>' +
            '<input type="number" class="form-control input-yura_white" id="lluvia' + num_form + '" required>' +
            '<div class="input-group-btn">' +
            '<button type="button" class="btn btn-yura_primary" onclick="store_temperatura(' + num_form + ')">' +
            '<i class="fa fa-fw fa-save"></i>' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>')
    }

    function store_all_temperatura() {
        data = [];
        for (i = 1; i <= num_form; i++) {
            data.push({
                fecha: $('#fecha' + i).val(),
                minima: $('#minima' + i).val(),
                maxima: $('#maxima' + i).val(),
                lluvia: $('#lluvia' + i).val(),
            });
        }
        datos = {
            _token: '{{csrf_token()}}',
            data: data
        };
        $.LoadingOverlay('show');
        $.post('{{url('temperaturas/store_all_temperatura')}}', datos, function (retorno) {
            if (!retorno.success) {
                alerta(retorno.mensaje);
            } else {
                listar_temperaturas();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>