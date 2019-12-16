<form id="form-importar_actividad" action="{{url('costos_gestion/importar_file_actividad')}}" method="POST">
    {!! csrf_field() !!}
    <div class="input-group">
        <span class="input-group-addon" style="background-color: #e9ecef">
            Archivo
        </span>
        <input type="file" id="file_actividad" name="file_actividad" required class="form-control input-group-addon"
               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
        <span class="input-group-addon" style="background-color: #e9ecef">Área</span>
        <select name="id_area_actividad" id="id_area_actividad" class="form-control" required>
            @foreach($areas as $item)
                <option value="{{$item->id_area}}">{{$item->nombre}}</option>
            @endforeach
        </select>
        <span class="input-group-btn">
            <button type="button" class="btn btn-primary" onclick="importar_file_actividad()">
                <i class="fa fa-fw fa-check"></i>
            </button>
        </span>
    </div>
</form>