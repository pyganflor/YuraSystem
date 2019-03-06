<div class="text-right">
    <button type="button" onclick="add_input()" id="btn_add_input" title="Agregar campos" class="btn btn-success btn-xs">
        <i class="fa fa-plus" aria-hidden="true"></i>
    </button>
    <button type="button" onclick="delete_input('{{count($cliente_pedido_especificacion)}}')" title="Eliminar campos" class="btn btn-danger btn-xs">
        <i class="fa fa-minus" aria-hidden="true"></i>
    </button>
</div>
<form id="form_add_precio">
    @foreach($cliente_pedido_especificacion as $key => $cpe)
        <div class="row" id="row_{{$key+1}}">
            <input type="hidden" id="id_cliente_pedido_especificacion_{{$key+1}}" value="{{$cpe->id_cliente_pedido_especificacion}}">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="nombre_marca">Cliente</label>
                    <select id="id_cliente_{{$key+1}}" name="id_cliente" class="form-control" disabled required>
                        <option disabled selected> Seleccione </option>
                        @foreach($clientes as $c)
                            <option {{$cpe->id_cliente == $c->id_cliente ? "selected" : ""}} value="{{$c->id_cliente}}"> {{$c->nombre}} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="identificacion">Precio</label>
                    <input type="number" id="precio_{{$key+1}}" name="precio" class="form-control" min="1" value="{{$cpe->precio}}" required>
                </div>
            </div>
        </div>
    @endforeach
</form>
