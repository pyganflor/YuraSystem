@extends('layouts.adminlte.master')

@section('titulo')
    Cosecha Ptas. Madres
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cosecha de Plantas Madres
            <small>módulo de propagación</small>
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

    <!-- Main content -->
    <section class="content">
        <table style="width: 100%">
            <tr>
                <td style="padding-right: 10px">
                    <div class="form-group input-group">
                        <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                            <i class="fa fa-fw fa-calendar"></i>
                        </span>
                        <input type="date" id="fecha_search" name="fecha_search" value="{{date('Y-m-d')}}"
                               class="form-control input-yura_default text-center" onchange="listar_cosechas();"
                               style="width: 100% !important;" max="2020-10-01">
                    </div>
                </td>
                <td style="padding-right: 10px; width: 150px">
                    <div class="form-group input-group">
                        <input type="text" readonly id="total_cosecha_dia" name="total_cosecha_dia" style="width: 100% !important;"
                               class="form-control text-center input-yura_disabled" placeholder="Cosecha">
                    </div>
                </td>
                <td style="padding-right: 10px">
                    <div class="form-group input-group" style="" id="div_cosecha_x_variedad">
                        <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                            <i class="fa fa-fw fa-leaf"></i>
                        </span>
                        <select name="datos_cosecha_x_variedad" id="datos_cosecha_x_variedad" class="form-control input-yura_default"
                                style="width: 100%;">
                        </select>
                    </div>
                </td>
            </tr>
        </table>
        <div class="row">
            <div class="col-md-7 col-sm-12" id="div_listado_cosechas">
            </div>
            <div class="col-md-5" id="div_form_add_cosecha">
                @include('adminlte.gestion.propagacion.cosecha_plantas_madres.forms.add_cosecha')
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.propagacion.cosecha_plantas_madres.script')
@endsection