@extends('layouts.adminlte.error.master')

@section('titulo')
    Usuario inactivo
@endsection

@section('contenido')
    <!-- Main content -->
    <a href="{{url('/')}}" title="Retornar a la página de inicio.">
        <img src="{{url('images/usuario_inactivo.png')}}" alt="Acceso denegado" width="450px">
    </a>
    <div class="alert alert-info text-center">
        <h4 style="color: black">Lo sentimos, su cuenta ha sido desactivada, ponte en contacto con el administrador.</h4>
    </div>

@endsection