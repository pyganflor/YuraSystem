<div class="form-row">
    <div class="col-md-5 col-sm-12 col-xs-12 mt-2 mt-md-0">
        <div class="form-group input-group">
            <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                Desde
            </span>
            <input type="date" class="form-control input-yura_default" id="filtro_desde" value="{{$desde}}" required>

        </div>
    </div>
    <div class="col-md-5 col-sm-12 col-xs-12 mt-2 mt-md-0">
        <div class="form-group input-group">
            <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                Hasta
            </span>
            <input type="date" class="form-control input-yura_default" id="filtro_hasta" value="{{$hasta}}" required>
        </div>
    </div>
    <div class="col-md-2 col-sm-12 col-xs-12 mt-2 mt-md-0 text-center">
        <div class="btn-group">
            <button type="button" class="btn btn-yura_dark" onclick="listar_temperaturas()">
                <i class="fa fa-fw fa-search"></i>
            </button>
            <button type="button" class="btn btn-yura_primary" onclick="add_temperatura()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 mt-2 mt-md-0" style="overflow-y: scroll; max-height: 450px;">
        <table class="table-striped table-bordered" style="border: 1px solid #9d9d9d; width: 100%; border-radius: 18px 18px 0 0"
               id="table_temperaturas">
            <thead>
            <tr id="tr_fijo_0">
                <th class="" style="border-color: #9d9d9d; background-color: #e9ecef !important; border-radius: 18px 0 0 0; padding-left: 5px">
                    Fecha
                </th>
                <th class="" style="border-color: #9d9d9d; background-color: #e9ecef !important; padding-left: 5px">
                    Mínima
                </th>
                <th class="" style="border-color: #9d9d9d; background-color: #e9ecef !important; padding-left: 5px">
                    Máxima
                </th>
                <th class="" style="border-color: #9d9d9d; background-color: #e9ecef !important; border-radius: 0 18px 0 0; padding-left: 5px">
                    Lluvia
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#e5f7f3 !important');"
                    onmouseleave="$(this).css('background-color','');">
                    <td class="" style="border-color: #9d9d9d; padding-left: 5px">
                        {{$item->fecha}}
                    </td>
                    <td class="" style="border-color: #9d9d9d; padding-left: 5px">
                        {{$item->minima}}
                    </td>
                    <td class="" style="border-color: #9d9d9d; padding-left: 5px">
                        {{$item->maxima}}
                    </td>
                    <td class="" style="border-color: #9d9d9d; padding-left: 5px">
                        {{$item->lluvia}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    estructura_tabla('table_temperaturas', false, false);
    $('#table_temperaturas_wrapper .row:first').hide()
</script>

<style>
    #tr_fijo_0 th {
        position: sticky;
        top: 0;
        z-index: 1;
    }
</style>