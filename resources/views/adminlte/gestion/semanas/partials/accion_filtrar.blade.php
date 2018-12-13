<div class="row" id="accion_1">
    <div class="col-md-2">
        <select name="id_variedad" id="id_variedad" class="form-control" required>
            <option value="">Variedad</option>
            @foreach($variedades as $item)
                <option value="{{$item->id_variedad}}">{{$item->planta->nombre}} - {{$item->siglas}}</option>
            @endforeach
        </select>
    </div>
    @if(count($annos)>0)
        <div class="col-md-2">
            <select name="anno" id="anno" class="form-control" required>
                <option value="">Año</option>
                @foreach($annos as $item)
                    <option value="{{$item->anno}}">{{$item->anno}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-block btn-primary" onclick="listar()">
                <i class="fa fa-fw fa-check"></i> Continuar
            </button>
        </div>
    @else
        <div class="col-md-2">
            <p class="text-center">
                <button type="button" class="btn btn-block btn-default" disabled>
                    <i class="fa fa-fw fa-check"></i> Nada que mostrar
                </button>
            </p>
        </div>
    @endif
</div>