<div class="row">
    <div class="col-md-4">
        <select class="form-control input-yura_default" id="variedad_copiar">
            @foreach(getVariedades() as $var)
                <option value="{{$var->id_variedad}}">{{$var->planta->siglas}}-{{$var->nombre}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <select class="form-control input-yura_default" id="anno_copiar">
            @foreach($annos as $a)
                <option value="{{$a->anno}}">{{$a->anno}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <button type="button" class="btn btn-block btn-yura_primary" onclick="copiar_semanas()">
            <i class="fa fa-fw fa-check"></i> Aceptar
        </button>
    </div>
</div>

<script>
    function copiar_semanas() {
        datos = {
            _token: '{{csrf_token()}}',
            variedad: $('#variedad_copiar').val(),
            anno: $('#anno_copiar').val(),
        };
        post_jquery('{{url('semanas/copiar_semanas')}}', datos, function () {
            cerrar_modals();
        });
    }
</script>