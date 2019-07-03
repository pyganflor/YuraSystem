@extends('layouts.adminlte.master')

@section('titulo')
    TEST
@endsection

@section('contenido')

@endsection

@section('script_final')
    <script>
        notificar('Notificación en desarrollo', '{{url('')}}', '', 10000);
    </script>
@endsection