@extends('layouts.adminlte.master')

@section('script_final')
    <script>
        notificar('Notificación en desarrollo', '{{url('')}}', '', 10000);
    </script>
@endsection