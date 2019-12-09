<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="desde_{{$x}}">Desde</label>
            <input type="number" id="desde_{{$x}}" name="desde_{{$x}}" class="form-control desde" required maxlength="250"  min="0" autocomplete="off"
                   value=''>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="hasta">Hasta</label>
            <input type="number" id="hasta_{{$x}}" name="hasta_{{$x}}" class="form-control hasta" min="1" required
                   autocomplete="off" value="">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="id_color_{{$x}}">
                Color <span style="width: 79px;height: 10px;float: right;margin-top: 6px;margin-left: 10px;" id="color_{{$x}}"></span>
            </label>
            <select class="form-control color" id="id_color_{{$x}}" name="id_color_{{$x}}"
                onchange="cambia_color('{{$x}}',this)">
                @foreach($colores as $color)
                    <option value="{{$color->fondo}}">{{$color->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
