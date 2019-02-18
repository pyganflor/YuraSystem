<div class="box box-info">
    <div class="box-header with-border">
        <div class="col-md-6">
            <h3 class="box-title">
                Detalles de clasificaciones estandar
            </h3>
        </div>
    </div>
    <div class="box-body">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Variedades</span>
            <select name="_id_variedad_search" id="_id_variedad_search" class="form-control" onchange="buscar_detalles_estandar()">
                <option value="">Todas...</option>
                @foreach($variedades as $item)
                    <option value="{{$item->id_variedad}}">{{$item->planta->nombre}} - {{$item->siglas}}</option>
                @endforeach
            </select>
        </div>

        <div id="div_content_detalles_estandar">
        </div>
    </div>
</div>

<script>
    buscar_detalles_estandar();
</script>