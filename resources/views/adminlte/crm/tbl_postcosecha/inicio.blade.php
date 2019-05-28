@extends('layouts.adminlte.master')

@section('titulo')
    Tablas - Postcosecha
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Tablas
            <small>Postcosecha</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a href="javascript:void(0)"><i class="fa fa-table"></i> Tablas</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Postcosecha
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <div class="input-group">
                        <div class="input-group-btn bg-gray btn_desde-hasta_M" style="display: none">
                            <button type="button" class="btn btn-default dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fa fa-calendar"></i> Desde <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach(getMeses() as $pos => $m)
                                    <li>
                                        <a href="javascript:void(0)" onclick="select_mes('{{$pos + 1}}', 'desde')"
                                           class="{{$pos + 1 == date('m') ? 'bg-aqua-active' : ''}} li_mes_desde" id="li_mes_desde_{{$pos + 1}}">
                                            {{$m}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="input-group-addon bg-gray btn_desde-hasta_S">
                            <i class="fa fa-calendar"></i> Desde
                        </div>
                        <input type="number" class="form-control" id="desde" placeholder="Desde" min="1" max="53"
                               value="{{substr(getSemanaByDate(date('Y-m-d'))->codigo, 2)}}" onkeypress="return isNumber(event)">
                        <div class="input-group-btn bg-gray btn_desde-hasta_M" style="display: none">
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
                        <div class="input-group-addon bg-gray btn_desde-hasta_S">
                            <i class="fa fa-calendar"></i> Hasta
                        </div>
                        <input type="number" class="form-control" id="hasta" placeholder="Hasta" min="1" max="53"
                               value="{{substr(getSemanaByDate(date('Y-m-d'))->codigo, 2)}}" onkeypress="return isNumber(event)" maxlength="2">
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
                        <div class="input-group-addon bg-gray">
                            <i class="fa fa-leaf"></i> Variedad
                        </div>
                        <select name="variedad" id="variedad" class="form-control">
                            <option value="A">Acumulado</option>
                            @foreach(@getVariedades() as $v)
                                <option value="{{$v->id_variedad}}">{{$v->nombre}}</option>
                            @endforeach
                            <option value="T">Todas</option>
                        </select>
                        <div class="input-group-addon bg-gray">
                            <i class="fa fa-filter"></i> Criterio
                        </div>
                        <select name="criterio" id="criterio" class="form-control">
                            <option value="C">Cajas</option>
                            <option value="T">Tallos</option>
                            <option value="D">Desecho</option>
                            <option value="R">Rendimiento</option>
                            <option value="Q">Calibre</option>
                        </select>
                        <div class="input-group-addon bg-gray">
                            <i class="fa fa-calendar-check-o"></i> Rango
                        </div>
                        <select name="rango" id="rango" class="form-control" onchange="select_rango($(this).val())">
                            <option value="A">Anual</option>
                            <option value="M">Mensual</option>
                            <option value="S" selected="">Semanal</option>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" id="btn_filtrar" class="btn btn-default" onclick="filtrar_tablas()" title="Buscar">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                        </div>
                    </div>
                </h3>
            </div>

            <div class="box-body" id="div_contentido_tablas"></div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.crm.tbl_postcosecha.script')
@endsection