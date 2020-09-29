<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title text-color_yura">Agregar cama</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="input-group">
            <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                Área de trabajo
            </div>
            <select name="area_trabajo" id="area_trabajo" class="form-control input-yura_default">
                <option value="PLANTAS MADRES" {{(isset($cama) && $cama->area_trabajo == 'PLANTAS MADRES' ? 'selected' : '')}}>PLANTAS MADRES
                </option>
                <option value="ENRAIZAMIENTO" {{(isset($cama) && $cama->area_trabajo == 'ENRAIZAMIENTO' ? 'selected' : '')}}>ENRAIZAMIENTO
                </option>
                <option value="CONFINAMIENTO" {{(isset($cama) && $cama->area_trabajo == 'CONFINAMIENTO' ? 'selected' : '')}}>CONFINAMIENTO
                </option>
            </select>
        </div>
        <div class="input-group" style="margin-top: 10px">
            <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                Nombre
            </div>
            <input type="text" maxlength="25" name="nombre_cama" id="nombre_cama" class="form-control input-yura_default text-center"
                   value="{{isset($cama) ? $cama->nombre : ''}}">
        </div>
    </div>
    <div class="box-footer">
        @if(!isset($cama))
            <button type="button" class="btn btn-primary pull-right btn-yura_primary" onclick="store_cama()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        @else
            <button type="button" class="btn btn-primary pull-right btn-yura_primary" onclick="update_cama({{$cama->id_cama}})">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        @endif
    </div>
</div>