<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title text-color_yura">Agregar contenedor</h3>
    </div>
    <div class="box-body">
        <div class="input-group">
            <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                Nombre
            </div>
            <input type="text" maxlength="25" name="nombre" id="nombre" class="form-control input-yura_default text-center"
                   value="{{isset($contenedor) ? $contenedor->nombre : ''}}">
        </div>
        <div class="input-group" style="margin-top: 10px">
            <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                Cantidad plantas
            </div>
            <input type="number" name="cantidad" id="cantidad" class="form-control input-yura_default text-center"
                   value="{{isset($contenedor) ? $contenedor->cantidad : ''}}">
        </div>
    </div>
    <div class="box-footer">
        @if(!isset($contenedor))
            <button type="button" class="btn btn-primary pull-right btn-yura_primary" onclick="store_contenedor()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        @else
            <button type="button" class="btn btn-primary pull-right btn-yura_primary"
                    onclick="update_contenedor({{$contenedor->id_contenedor_propag}})">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        @endif
    </div>
</div>