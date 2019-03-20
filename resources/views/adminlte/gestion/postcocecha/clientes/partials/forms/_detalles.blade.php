{{---------------- Especificacion_empaque-------------------}}
{{--<form id="form-add_especificacion_empaque_{{$cant_detalles}}" method="POST" action="{{url('clientes/store_especificacion')}}">--}}
<div class="row">
    <div class="col-md-3">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Cantidad</span>
            <input type="number" onkeypress="return isNumber(event)" id="cantidad_{{$cant_detalles}}" name="cantidad_{{$cant_detalles}}"
                   readonly class="form-control" ondblclick="activar_input(this)"
                   maxlength="3" max="999" min="1" value="1" required placeholder="Escriba la cantidad">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Caja</span>
            <select name="id_empaque_{{$cant_detalles}}" id="id_empaque_{{$cant_detalles}}" class="form-control" required>
                <option value="">Seleccione</option>
                @foreach($cajas as $caja)
                    <option value="{{$caja->id_empaque}}">{{explode("|",$caja->nombre)[0]}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Imagen</span>
            <input type="file" id="imagen_{{$cant_detalles}}" name="imagen_{{$cant_detalles}}" class="form-control" maxlength="250"
                   placeholder="Escriba el nombre" accept="image/jpeg">
        </div>
    </div>
</div>
{{---------------- Detalles_especificacion_empaque-------------------}}
<legend style="font-size: 1.3em">
    Desgloses
    <a href="javascript:void(0)" class="btn btn-xs btn-danger pull-right" title="Añadir desglose" id="btn_del_desgloses_{{$cant_detalles}}"
       onclick="del_desgloses_especificacion('{{$cant_detalles}}')" style="display: none">
        <i class="fa fa-fw fa-trash"></i>
    </a>
    <a href="javascript:void(0)" class="btn btn-xs btn-primary pull-right" title="Añadir desglose" id="btn_add_desgloses_{{$cant_detalles}}"
       onclick="add_desgloses_especificacion('{{$cant_detalles}}')">
        <i class="fa fa-fw fa-plus"></i>
    </a>
</legend>
<table width="100%" class="table" id="table_forms_especificaciones_desgloses_{{$cant_detalles}}">
</table>

<input type="hidden" id="pos_form_detalles_{{$cant_detalles}}" name="pos_form_detalles_{{$cant_detalles}}" value="{{$cant_detalles}}">
<input type="hidden" id="cant_forms_desgloses_{{$cant_detalles}}" name="cant_forms_desgloses_{{$cant_detalles}}" value="0">
{{--</form>--}}

{{-- ejemplo de ingreso
4 Cajas 1/2, de 13 ramos, de 500gr c/u, de la variedad GLX, con envoltura Capuchón, de plástico transparente
2 Cajas Full, de 7 ramos de GLX de 500gr y 6 ramos de XL de 500gr c/u, con envoltura Capuchón, de plástico transparente
--}}
