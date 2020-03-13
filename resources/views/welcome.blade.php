@extends('layouts.adminlte.master')

@section('titulo')
    TEST
@endsection

@section('contenido')
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <label for="curva">Curva</label>
                <input type="text" id="curva" placeholder="30-40-30" class="text-center form-control" style="width: 100%">
            </div>
            <div class="col-md-6">
                <label for="curva">Inicio</label>
                <input type="number" id="inicio" placeholder="Número" class="text-center form-control" style="width: 100%">
            </div>
        </div>
        <br>
        <label for="curva">Nueva Curva</label>
        <input type="text" id="new_curva" placeholder="Nueva Curva" class="text-center form-control" style="width: 100%" readonly>
        <br>
        <button type="button" class="btn btn-block btn-default btn-lg" onclick="recalcular()" id="btn_border">
            RECALCULAR
        </button>
    </section>

    <style>
        #btn_border {
            border-top: 3px solid orange !important;
            border-left: 3px solid orange !important;
            border-bottom: 3px solid blue !important;
            border-right: 3px solid blue !important;
        }
    </style>
@endsection

@section('script_final')
    <script>
        function recalcular() {
            datos = {
                _token: '{{csrf_token()}}',
                curva: $('#curva').val(),
                inicio: $('#inicio').val(),
            };
            $.post('{{url('test')}}', datos, function (retorno) {
                $('#new_curva').val(retorno.r)
            }, 'json')
        }
    </script>
@endsection
