<div class="row" id="row_input_{{$x}}">
    <div class="col-md-4">
        <div class="form-group">
            <label for="condicional_{{$x}}">Condicional</label>
            <select id="condicional_{{$x}}" name="condicional_{{$x}}" class="form-control condicional text-center" required>
                <option value="=<"> Mayor o igual que</option>
                <option value="=>"> Menor o igual que</option>
                <option value="="> Igual que</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="cantidad_{{$x}}">Cantidad</label>
            <input type="number" id="cantidad_{{$x}}" name="cantidad_{{$x}}" class="form-control cantidad text-center" min="1"
                   required autocomplete="off" value="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="id_color_{{$x}}"> Color {{--<span style="width: 79px;height: 10px;float: right;margin-top: 6px;margin-left: 10px;" id="color_{{$x}}"></span>--}} </label>
            <input type="color" class="form-control color" id="id_color_{{$x}}" name="id_color_{{$x}}" value="">
        </div>
    </div>
    <div class="text-center">
        <div class="form-group">
            <label> Acción </label>
            <button type="button" class="btn btn-danger" title="Eliminar rango" id="{{$x}}" onclick="delete_row(this.id)">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
</div>
<hr style="margin: 0 0 3px 0;"/>
