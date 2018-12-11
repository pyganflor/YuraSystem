<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Detalles reales ingresados
        </h3>
        <button class="pull-right btn btn-xs btn-primary" title="Añadir detalle"
                onclick="add_verde('{{substr($clasificacion->recepciones[0]->recepcion->fecha_ingreso,0,10)}}')">
            <i class="fa fa-fw fa-plus"></i>
        </button>
    </div>
    <div class="box-body">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Variedades</span>
            <select name="_id_variedad_search" id="_id_variedad_search" class="form-control" onchange="buscar_detalles_reales()">
                <option value="">Seleccione...</option>
                @foreach($variedades as $item)
                    <option value="{{$item->id_variedad}}">{{$item->planta->nombre}} - {{$item->siglas}}</option>
                @endforeach
            </select>
        </div>
        <div id="div_content_detalles_reales"></div>
    </div>
</div>
<script>
    buscar_detalles_reales();
</script>