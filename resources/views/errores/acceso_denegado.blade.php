@extends('layouts.adminlte.error.master')

@section('titulo')
    Acceso denegado
@endsection

@section('contenido')
    <!-- Main content -->
    <a href="{{url('/')}}" title="Retornar a la página de inicio.">
        <img src="{{url('images/acceso_denegado.png')}}" alt="Acceso denegado" width="450px">
    </a>
    <div class="alert alert-info text-center">
        <h4 style="color: black">Lo sentimos, usted no tiene acceso a la acción solicitada.</h4>
    </div>

@endsection