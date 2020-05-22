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

        @php
            $semana = \yura\Modelos\Semana::All()->where('codigo', 2004)->first();
            $pedidos = \yura\Modelos\Pedido::where('estado', 1)
                    ->where('fecha_pedido', '>=', $semana->fecha_inicial)
                    ->where('fecha_pedido', '<=', $semana->fecha_final)
                    ->get();
            $venta_sem = 0;
        @endphp
        <legend class="mouse-hand" ondblclick="$('.li_hide').toggleClass('hide')">VENTAS: {{$semana->codigo}} ({{$semana->fecha_inicial}} __ {{$semana->fecha_final}})</legend>
        <ul>
            @foreach($pedidos as $p)
                @php
                    $facturaAnulada = getFacturaAnulada($p->id_pedido);
                    $precio = $p->getPrecioByPedido();
                    if(!$facturaAnulada)
                        $venta_sem += $precio;
                @endphp
                <li class="{{!$facturaAnulada ? 'text-color_yura' : ''}} li_hide hide" >
                    {{$p->id_pedido}} __ {{$p->fecha_pedido}} __ {{$p->cliente->detalle()->nombre_completo}} __ $ {{$precio}}
                </li>
            @endforeach
        </ul>
        Venta total por semana = {{number_format($venta_sem, 2)}}

@php
    $pedido = \yura\Modelos\Pedido::find(21);
dd($pedido->id_pedido, $pedido->fecha_pedido, $pedido->getPrecioByPedido());
@endphp
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
