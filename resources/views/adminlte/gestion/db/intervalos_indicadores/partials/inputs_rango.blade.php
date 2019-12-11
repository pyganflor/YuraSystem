<div class="row" id="row_input_{{$x}}">
    <input type="hidden" class="tipo" value="I">
    <div class="col-md-4">
        <div class="">
            <label for="desde_{{$x}}">Desde</label>
            <input type="number" id="desde_{{$x}}" name="desde_{{$x}}" class="form-control desde text-center" required
                   autocomplete="off" value=''>
        </div>
    </div>
    <div class="col-md-4">
        <div class="">
            <label for="hasta">Hasta</label>
            <input type="number" id="hasta_{{$x}}" name="hasta_{{$x}}" class="form-control hasta text-center"
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

