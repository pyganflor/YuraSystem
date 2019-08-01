<div class="row" id="busqueda">
    <div class="col-md-12">
        <div class="input-group">
            <input type="text" id="nombre_pais" name="nombre_pais" value="" placeholder="Buscar país" class="form-control" onkeyup="buscar_pais(this.value)">
            <span class="input-group-btn">
                <a class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></a>
            </span>
        </div>
    </div>
    <div class="row" style="margin-top:55px">
        <div class="container-fluid">
            <div id="paises" class="col-md-9">
                @foreach ($dataPaises as $pais)
                    <div class="col-md-3">
                        <input type="checkbox" id="codigo_pais_{{$pais->codigo}}" name="codigo_pais_{{$pais->codigo}}" onclick="selected(this)" value="{{$pais->codigo}}">
                        <label for="codigo_pais_{{$pais->codigo}}">{{$pais->nombre}}</label>
                    </div>
                @endforeach
            </div>
            <div class="col-md-3">
                <div class="list-group" id="paises_selected">
                    <a href="javascript:void(0)" class="list-group-item active list-group-item-action">
                        <i class="fa fa-flag" aria-hidden="true"></i>
                        Paises Seleccionados
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
