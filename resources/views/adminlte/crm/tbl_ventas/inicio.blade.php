@extends('layouts.adminlte.master')

@section('titulo')
    Tablas - Ventas
@endsection

@section('css_inicio')
@endsection

@section('script_inicio')

@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Tablas
            <small>Ventas</small>
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
                    <i class="fa fa-fw fa-refresh"></i> {{$submenu->nombre}}
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="input-group">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-calendar-check-o"></i> Rango
                    </div>
                    <select name="rango" id="rango" class="form-control">
                        <option value="A">Anual</option>
                        <option value="M" selected>Mensual</option>
                    </select>
                    <div class="input-group-btn bg-gray btn_desde-hasta_M">
                        <button type="button" class="btn btn-default dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fa fa-calendar"></i> Desde <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach(getMeses() as $pos => $m)
                                <li>
                                    <a href="javascript:void(0)" onclick="select_mes('{{$pos + 1}}', 'desde')"
                                       class="{{$pos + 1 == 1 ? 'bg-aqua-active' : ''}} li_mes_desde" id="li_mes_desde_{{$pos + 1}}">
                                        {{$m}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <input type="number" class="form-control" id="desde" placeholder="Desde" min="1" max="12"
                           value="01" onkeypress="return isNumber(event)" readonly maxlength="2">
                    <div class="input-group-btn bg-gray btn_desde-hasta_M">
                        <button type="button" class="btn btn-default dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fa fa-calendar"></i> Hasta
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            @foreach(getMeses() as $pos => $m)
                                <li>
                                    <a href="javascript:void(0)" onclick="select_mes('{{$pos + 1}}', 'hasta')"
                                       class="{{$pos + 1 == date('m') ? 'bg-aqua-active' : ''}} li_mes_hasta" id="li_mes_hasta_{{$pos + 1}}">
                                        {{$m}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <input type="number" class="form-control" id="hasta" placeholder="Hasta" min="1" max="12"
                           value="{{date('m')}}" onkeypress="return isNumber(event)" maxlength="2" readonly>
                    <div class="input-group-btn bg-gray">
                        <button type="button" class="btn btn-default dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fa fa-calendar-minus-o"></i> Años
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            @foreach($annos as $a)
                                <li>
                                    <a href="javascript:void(0)" onclick="select_anno('{{$a->anno}}')"
                                       class="{{$a->anno == date('Y') ? 'bg-aqua-active' : ''}} li_anno" id="li_anno_{{$a->anno}}">
                                        {{$a->anno}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <input type="text" class="form-control" placeholder="Años" id="annos" name="annos" readonly value="{{date('Y')}}">
                </div>
                <div class="input-group">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-user-circle"></i> Cliente
                    </div>
                    <select name="cliente" id="cliente" class="form-control">
                        <option value="A">Acumulado</option>
                        @foreach($clientes as $c)
                            <option value="{{$c->id_cliente}}">{{$c->detalle()->nombre}}</option>
                        @endforeach
                        <option value="T" selected>Todos</option>
                        <option value="P">País</option>
                    </select>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-leaf"></i> Variedad
                    </div>
                    <select name="variedad" id="variedad" class="form-control">
                        <option value="A" selected>Acumulado</option>
                        @foreach(getVariedades() as $item)
                            <option value="{{$item->id_variedad}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-filter"></i> Criterio
                    </div>
                    <select name="criterio" id="criterio" class="form-control">
                        <option value="V">Valor</option>
                        <option value="F">Físicas</option>
                        <option value="Q">Equivalentes</option>
                        <option value="P">Precios</option>
                    </select>
                    <div class="input-group-addon bg-gray mouse-hand">
                        <input type="checkbox" id="acumulado" name="acumulado" class="mouse-hand">
                        <label for="acumulado" class="mouse-hand">Mostrar Acumulados</label>
                    </div>

                    <div class="input-group-btn">
                        <button type="button" id="btn_filtrar" class="btn btn-default" onclick="filtrar_tablas()" title="Buscar">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                    <div class="input-group-btn">
                        <button type="button" id="btn_exportar" class="btn btn-success" onclick="exportar_tabla()" title="Exportar">
                            <i class="fa fa-fw fa-file-excel-o"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="box-body" id="div_contentido_tablas"></div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.crm.tbl_ventas.script')
@endsection