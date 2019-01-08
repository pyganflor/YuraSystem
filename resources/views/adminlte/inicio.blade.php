@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>módulo de administrador</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Dashboard
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div id="div_recepciones"></div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    <script>
        cargar_recepciones();

        function cargar_recepciones() {
            get_jquery('{{url('dashboard/recepciones')}}', {}, function (retorno) {
                $('#div_recepciones').html(retorno);
            });
        }
    </script>
@endsection