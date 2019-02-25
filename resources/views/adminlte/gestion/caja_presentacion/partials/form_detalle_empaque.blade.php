<form id="form_add_detalle_empaque">
    <input type="hidden" id="id_empaque" value="{{isset($dataDetalleEmpaque[0]->id_empaque) ? $dataDetalleEmpaque[0]->id_empaque : ""}}">
    <input type="hidden" id="nombre_empaque" value="{{$nombreEmpaque->nombre}}">
    @if(count($dataDetalleEmpaque) > 0)
        @foreach($dataDetalleEmpaque as $key => $empaque)
            <div class="row">
            <div class="col-md-3">
                <div class="">
                    <label for="nombre_detalle_empaque">Variedad</label>
                    <input type="text" id="id_variedad_{{$key+1}}" name="id_variedad" class="form-control" required maxlength="250" autocomplete="off" value='{{$empaque->nombre_variedad}}' readonly>
                    {{--<select id="id_variedad_{{$key+1}}" name="id_variedad" class="form-control">
                        @foreach($variedades as $variedad)
                            <option {{$empaque->id_variedad == $variedad->id_variedad ? "selected" : ""}} value="{{$variedad->id_variedad}}">{{$variedad->nombre}}</option>
                        @endforeach
                    </select>--}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="">
                    <label for="nombre_detalle_empaque">Clasificación ramo</label>
                    <input type="text" id="id_clasificacion_ramo_{{$key+1}}" name="id_clasificacion_ramo" class="form-control" required maxlength="250" autocomplete="off" value='{{$empaque->nombre_clasificacion_ramo}}' readonly>
                    {{--<select id="id_clasificacion_ramo_{{$key+1}}" name="id_clasificacion_ramo" class="form-control">
                        @foreach($clasificacionRamo as $cr)
                            <option {{$cr->id_clasificacion_ramo == $empaque->id_clasificacion_ramo ? "selected" : ""}} value="{{$cr->id_clasificacion_ramo}}">{{$cr->nombre}}</option>
                        @endforeach
                    </select>--}}
                    {{--<input type="number" id="clasificacion_ramo_{{$key+1}}" name="clasificacion_ramo" class="form-control" required maxlength="250" autocomplete="off" value='{{$empaque->nombre_clasificacion_ramo}}'>
                    <input type="hidden" id="id_clasificacion_ramo_{{$key+1}}" value="{{$empaque->id_clasificacion_ramo}}">--}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="">
                    <label for="tipo_detalle_empaque">Unidad de medida</label>
                    <input type="text" id="id_unidad_medida_{{$key+1}}" name="id_unidad_medida_" class="form-control" required maxlength="250" autocomplete="off" value='{{$empaque->siglas_unidad_medida}}' readonly>
                    {{--<select id="id_unidad_medida_{{$key+1}}" name="id_unidad_medida" class="form-control">
                        <option value="1" {{$empaque->id_unidad_medida == 1 ? "selected" : ""}}>cm</option>
                        <option value="2" {{$empaque->id_unidad_medida == 2 ? "selected" : ""}}>gr</option>
                    </select>--}}
                </div>
            </div>
            <div class="col-md-2">
                <div class="">
                    <label for="nombre_detalle_empaque">Cantidad ramos</label>
                    <input type="number" id="cantidad_ramo_{{$key+1}}" name="cantidad_ramo" class="form-control" required maxlength="250" autocomplete="off" value='{{$empaque->cantidad}}' readonly>
                </div>
            </div>
            <div class="col-md-1" style="margin-top: 30px;">
                <button type="button" class="btn btn-danger btn-xs" title="Eliminar detalle empaque" onclick="delete_detalle_empaque('{{$empaque->id_detalle_empaque}}','{{$dataDetalleEmpaque[0]->id_empaque}}')">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </div>
                <input type="hidden" id="id_detalle_empaque_{{$key+1}}" value="{{$empaque->id_detalle_empaque}}">
        </div>
            <hr />
        @endforeach
    @else
        <div class="alert alert-info text-center">Este empaque no posee detalles</div>
    @endif
</form>
