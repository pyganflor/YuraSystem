<form id="form-importar_mano_obra" action="{{url('gestion_mano_obra/importar_file_mano_obra')}}" method="POST">
    {!! csrf_field() !!}
    <div class="input-group">
        <span class="input-group-addon" style="background-color: #e9ecef">
            Archivo
        </span>
        <input type="file" id="file_producto" name="file_mano_obra" required class="form-control input-group-addon"
               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
        <span class="input-group-btn">
            <button type="button" class="btn btn-primary" onclick="importar_file_mano_obra()">
                <i class="fa fa-fw fa-check"></i>
            </button>
        </span>
    </div>
</form>