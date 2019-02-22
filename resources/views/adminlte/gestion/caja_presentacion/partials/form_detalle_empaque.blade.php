{{--<div class="text-right">
    <button type="button" class="btn btn-success btn-xs" title="Agredar campo detalle empaque" onclick="add_input_detalle_empaque()">
        <i class="fa fa-plus" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn btn-danger btn-xs" title="Eliminar campo detalle empaque" onclick="delete_input_detalle_empaque()">
        <i class="fa fa-minus" aria-hidden="true"></i>
    </button>
</div>
<hr />--}}
<form id="form_add_detalle_empaque">
    <input type="hidden" id="id_empaque" value="{{$dataDetalleEmpaque[0]->id_empaque}}">
    <input type="hidden" id="nombre_empaque" value="{{$nombreEmpaque->nombre}}">
    @foreach($dataDetalleEmpaque as $empaque)
        <div class="row">
            <div class="col-md-3">
                <div class="">
                    <label for="nombre_detalle_empaque">Variedad</label>
                    <select id="id_variedad" name="id_variedad" class="form-control">
                        @foreach($variedades as $variedad)
                        <option {{$empaque->id_variedad == $variedad->id_variedad ? "selected" : ""}} value="{{$variedad->id_variedad}}">{{$variedad->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="">
                    <label for="nombre_detalle_empaque">Clasificacion ramo</label>
                    <input type="number" id="clasificacion_ramo" name="clasificacion_ramo" class="form-control" required maxlength="250" autocomplete="off" value='{{$empaque->nombre_clasificacion_ramo}}'>
                    <input type="hidden" id="id_clasificacion_ramo" value="{{$empaque->id_clasificacion_ramo}}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="">
                    <label for="tipo_detalle_empaque">Unidad de medida</label>
                    <select id="id_unidad_medida" name="id_unidad_medida" class="form-control">
                        <option value="1" {{$empaque->id_unidad_medida == 1 ? "selected" : ""}}>cm</option>
                        <option value="2" {{$empaque->id_unidad_medida == 2 ? "selected" : ""}}>gr</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="">
                    <label for="nombre_detalle_empaque">Cantidad ramos</label>
                    <input type="number" id="cantidad_ramo" name="cantidad_ramo" class="form-control" required maxlength="250" autocomplete="off" value='{{$empaque->cantidad}}'>
                </div>
            </div>
            <div class="col-md-1" style="margin-top: 30px;">
                <button type="button" class="btn btn-danger btn-xs" title="Eliminar detalle empaque" onclick="delete_detalle_empaque('{{$empaque->id_detalle_empaque}}',{{$dataDetalleEmpaque[0]->id_empaque}})">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <hr />
    @endforeach
</form>
