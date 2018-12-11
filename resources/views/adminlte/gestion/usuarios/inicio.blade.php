@extends('layouts.adminlte.master')

@section('titulo')
    Usuarios
@endsection

@section('script_inicio')
    {{--<script src="{{url('js/portada/login.js')}}"></script>--}}

    <script language="JavaScript" type="text/javascript" src="{{url('js/rsa/jsbn.js')}}"></script>
    <script language="JavaScript" type="text/javascript" src="{{url('js/rsa/jsbn2.js')}}"></script>
    <script language="JavaScript" type="text/javascript" src="{{url('js/rsa/prng4.js')}}"></script>
    <script language="JavaScript" type="text/javascript" src="{{url('js/rsa/rng.js')}}"></script>
    <script language="JavaScript" type="text/javascript" src="{{url('js/rsa/rsa.js')}}"></script>
    <script language="JavaScript" type="text/javascript" src="{{url('js/rsa/rsa2.js')}}"></script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Usuarios
            <small>módulo de administrador</small>

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
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de los usuarios
                </h3>
            </div>
            <div class="box-body" id="div_content_usuarios">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-group input-group" style="padding: 0px">
                                <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_usuarios"
                                       name="busqueda_usuarios">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="buscar_listado()"
                                            onmouseover="$('#title_btn_buscar').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar').html('')">
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar"></em>
                                    </button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" onclick="add_usuario()"
                                            onmouseover="$('#title_btn_add').html('Añadir')"
                                            onmouseleave="$('#title_btn_add').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em id="title_btn_add"></em>
                                    </button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-success" onclick="exportar_usuarios()"
                                            onmouseover="$('#title_btn_exportar').html('Exportar')"
                                            onmouseleave="$('#title_btn_exportar').html('')">
                                        <i class="fa fa-fw fa-file-excel-o" style="color: #0c0c0c"></i> <em id="title_btn_exportar"></em>
                                    </button>
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_usuarios"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.usuarios.script')
@endsection