@foreach(getColores() as $c)
    <input type="hidden" id="fondo_color_{{$c->id_color}}" value="{{$c->fondo}}">
    <input type="hidden" id="texto_color_{{$c->id_color}}" value="{{$c->texto}}">
    <input type="hidden" id="nombre_color_{{$c->id_color}}" value="{{$c->nombre}}">
@endforeach

<select name="select_colores" id="select_colores" style="display: none">
    @foreach(getColores() as $c)
        <option value="{{$c->id_color}}">{{$c->nombre}}</option>
    @endforeach
</select>