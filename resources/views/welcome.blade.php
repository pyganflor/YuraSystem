@extends('layouts.adminlte.master')

@section('titulo')
    TEST
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('script_final')
    <script>
        notificar('Notificación en desarrollo', '{{url('')}}', '', 10000);
    </script>
@endsection