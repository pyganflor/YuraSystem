<div class="row" id="row_input_{{$x}}">
    <input type="hidden" class="tipo" value="C">
    <div class="col-md-4">
        <div class="">
            <label for="condicional_{{$x}}">Condicional</label>
            <select id="condicional_{{$x}}" name="condicional_{{$x}}" class="form-control condicional text-center" required>
                <option value="=<"> Mayor o igual que</option>
                <option value="=>"> Menor o igual que</option>
                <option value="="> Igual que</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="">
            <label for="cantidad_{{$x}}">Cantidad</label>
            <input type="number" id="cantidad_{{$x}}" name="cantidad_{{$x}}" class="form-control cantidad text-center"
                   required autocomplete="off" value="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="">
            <label for="id_color_{{$x}}"> Color {{--<span style="width: 79px;height: 10px;float: right;margin-top: 6px;margin-left: 10px;" id="color_{{$x}}"></span>--}} </label>
            <input type="color" class="form-control color" id="id_color_{{$x}}" name="id_color_{{$x}}" value="">
        </div>
    </div>
    <div class="text-center">
        <div class="">
            <label> Acción </label>
            <button type="button" class="btn btn-danger" title="Eliminar rango" id="{{$x}}" onclick="delete_row(this.id)">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
    <hr style="margin: 3px 0 3px 0;"/>
</div>

