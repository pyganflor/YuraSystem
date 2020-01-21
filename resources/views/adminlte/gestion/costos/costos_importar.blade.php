@extends('layouts.adminlte.master')

@section('titulo')
    Costos - Gestión
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('css_inicio')
@endsection

@section('contenido')
    <section class="content-header">
        <h1>
            Costos
            <small>Importar</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li>
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li>
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('{{$submenu->url}}')">
                    <i class="fa fa-fw fa-refresh"></i> {!! $submenu->nombre !!}
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <form id="form-importar_costos" action="{{url('costos_importar/importar_file_costos')}}" method="POST">
                    {!! csrf_field() !!}
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Concepto
                        </span>
                        <select name="concepto_importar" id="concepto_importar" class="form-control input-group-addon">
                            <option value="I">Insumos</option>
                            <option value="M">Mano de Obra</option>
                        </select>
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Archivo
                        </span>
                        <input type="file" id="file_costos" name="file_costos" required class="form-control input-group-addon"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Criterio
                        </span>
                        <select name="criterio_importar" id="criterio_importar" class="form-control input-group-addon">
                            <option value="V">Dinero</option>
                            <option value="C">Cantidad</option>
                        </select>
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Sobreescribir
                        </span>
                        <select name="sobreescribir_importar" id="sobreescribir_importar" class="form-control input-group-addon">
                            <option value="N">No</option>
                            <option value="S">Sí</option>
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" onclick="importar_file_costos()">
                                <i class="fa fa-fw fa-check"></i>
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    <script>
        function importar_file_costos() {
            if ($('#form-importar_costos').valid()) {
                $.LoadingOverlay('show');
                formulario = $('#form-importar_costos');
                var formData = new FormData(formulario[0]);
                //hacemos la petición ajax
                $.ajax({
                    url: formulario.attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    //necesario para subir archivos via ajax
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (retorno2) {
                        notificar('Se ha importado un archivo', '{{url('costos_gestion')}}');
                        if (retorno2.success) {
                            $.LoadingOverlay('hide');
                            alerta_accion(retorno2.mensaje, function () {
                                //location.reload();
                            });
                        } else {
                            alerta(retorno2.mensaje);
                            $.LoadingOverlay('hide');
                        }
                    },
                    //si ha ocurrido un error
                    error: function (retorno2) {
                        console.log(retorno2);
                        alerta(retorno2.responseText);
                        alert('Hubo un problema en el envío de la información');
                        $.LoadingOverlay('hide');
                    }
                });
            }
        }
    </script>
@endsection