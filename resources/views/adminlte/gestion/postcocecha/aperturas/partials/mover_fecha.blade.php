<form id="form_mover_fecha">
    <div class="input-group">
    <span class="input-group-addon bg-gray">
        Saldo
    </span>
        <input type="text" readonly id="saldo" name="saldo" value="{{$apertura->cantidad_disponible}}" class="form-control">
        <span class="input-group-addon bg-gray">
        Mover
    </span>
        <input type="number" onkeypress="return isNumber(event)" id="mover" name="mover" value="0" class="form-control"
               max="{{$apertura->cantidad_disponible}}" min="1" onchange="calcular_saldo()">
        <span class="input-group-addon bg-gray">
        Para
    </span>
        <input type="date" required id="fecha_mover" name="fecha_mover" value="{{date('Y-m-d')}}" class="form-control">
        <span class="input-group-btn">
        <button type="button" class="btn btn-primary" onclick="store_mover_fecha()">
            <i class="fa fa-fw fa-check"></i>
        </button>
    </span>
    </div>
</form>

<input type="hidden" readonly id="disponibles" value="{{$apertura->cantidad_disponible}}">
<input type="hidden" readonly id="id_apertura" value="{{$apertura->id_stock_apertura}}">

<script>
    function calcular_saldo() {
        disponibles = parseInt($('#disponibles').val());
        mover = parseInt($('#mover').val());

        if (mover <= disponibles)
            $('#saldo').val(disponibles - mover);
        else {
            $('#mover').val(0);
            $('#saldo').val(disponibles);
        }
    }

    function store_mover_fecha() {
        if ($('#form_mover_fecha').valid()) {
            datos = {
                _token: '{{csrf_token()}}',
                apertura: $('#id_apertura').val(),
                saldo: $('#saldo').val(),
                mover: $('#mover').val(),
                fecha: $('#fecha_mover').val(),
            };
            post_jquery('{{url('apertura/store_mover_fecha')}}', datos, function () {
                buscar_listado();
                cerrar_modals();
            });
        }
    }
</script>