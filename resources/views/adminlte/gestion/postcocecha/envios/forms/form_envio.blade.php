@for($i=0;$i<$cantForms;$i++)
    <form id="form_envio_{{$i+1}}" name="form_envio_{{$i+1}}">
        <div class="well sombra_estandar">
            <input type="hidden" value="{{$dataDetallesPedidos[$i]->cantidad}}" id="cantidad_detalle_form_{{$i+1}}">
            <input type="hidden" id="id_especificacion_{{$i+1}}" value="{{$dataDetallesPedidos[$i]->id_especificacion}}">
            <input type="hidden" id="id_cliente" value="{{$dataDetallesPedidos[$i]->id_cliente}}">
            <legend style="font-size: 1.3em">
                Detalle N# {{$i+1}} <span id="numero_detalle_{{$i+1}}">({{$dataDetallesPedidos[$i]->cantidad ." ".$dataDetallesPedidos[$i]->nombre}}) </span>
                <div>
                    <label style="float: left;margin-bottom: 0;margin-top: 4px;margin-right: 5px;">Fecha envío:</label>
                    <input type="date" id="fecha_envio_{{$i+1}}" class="form-control" style="width: 25%;margin-bottom: 5px"></div>
                <a href="javascript:void(0)" class="btn btn-xs btn-warning pull-right" title="Reiniciar envío" onclick="delete_input('{{$i+1}}')" id="btn_reset_form_envio_{{$i+1}}">
                    <i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0)" class="btn btn-xs btn-primary pull-right" title="Añadir envío" onclick="add_form_envio('{{$i+1}}','{{$dataDetallesPedidos[$i]->cantidad}}')" id="btn_add_form_envio_{{$i+1}}">
                    <i class="fa fa-fw fa-plus"></i>
                </a>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger pull-right hide" title="Eliminar envío" onclick="delete_form_envio('{{$i+1}}')" id="btn_delete_form_envio_{{$i+1}}">
                    <i class="fa fa-fw fa-trash"></i>
                </a>
            </legend>
            <div id="div_inputs_envios_{{$i+1}}"></div>
            <span id="msg_{{$i+1}}" class="error" style="margin-left:4%;"></span>
        </div>
    </form>
@endfor
