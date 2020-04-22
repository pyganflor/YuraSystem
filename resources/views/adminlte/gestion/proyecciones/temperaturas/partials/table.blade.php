<div class="input-group">
    <div class="input-group-addon bg-gray">
        Desde
    </div>
    <input type="date" class="form-control" id="filtro_desde" value="{{$desde}}" required>
    <div class="input-group-addon bg-gray">
        Hasta
    </div>
    <input type="date" class="form-control" id="filtro_hasta" value="{{$hasta}}" required>
    <div class="input-group-btn">
        <button type="button" class="btn btn-default" onclick="listar_temperaturas()">
            <i class="fa fa-fw fa-search"></i>
        </button>
    </div>
</div>

<table class="table-striped table-bordered" style="border: 2px solid #9d9d9d; width: 100%; margin-top: 10px">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Fecha
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Mínima
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Máxima
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Lluvia
        </th>
    </tr>
    @foreach($listado as $item)
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$item->fecha}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$item->minima}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$item->maxima}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$item->lluvia}}
            </td>
        </tr>
    @endforeach
</table>