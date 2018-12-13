@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Bienvenido
            <small>módulo de administrador</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <legend>Escoja una de las opciones que se muestran en el menú lateral con las que desee trabajar.</legend>

        <ul>
            <li><strong>Sistema operativo:</strong> {{detect()['os']}}</li>
            <li><strong>Navegador:</strong> {{detect()['browser']}}</li>
            <li><strong>Versión:</strong> {{detect()['version']}}</li>
            <li><strong>Info:</strong> {{$_SERVER['HTTP_USER_AGENT']}}</li>
        </ul>
    </section>

@endsection

@section('script_final')
    <script>
    </script>
@endsection
