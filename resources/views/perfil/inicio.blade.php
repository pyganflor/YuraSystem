@extends('layouts.adminlte.master')

@section('titulo')
    Mi Perfil
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
    <section class="content-header">
        <h1>
            Mi perfil
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('perfil')">
                    <i class="fa fa-fw fa-refresh"></i> Mi Perfil
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <a href="javascript:void(0)" onclick="$('#box-edit-image').toggleClass('hidden')">
                            <img class="profile-user-img img-responsive img-circle" onmouseover="$(this).addClass('sombra_estandar')"
                                 onmouseleave="$(this).removeClass('sombra_estandar')" id="img_perfil"
                                 src="{{url('storage/imagenes').'/'.$usuario->imagen_perfil}}" alt="">
                        </a>
                        <h3 class="profile-username text-center">{{$usuario->nombre_completo}}</h3>

                        <p class="text-muted text-center">{{$usuario->rol()->nombre}}</p>
                    </div>
                </div>

                <div class="box box-primary hidden" id="box-edit-image">
                    <div class="box-body">
                        <form action="{{url('perfil/update_image_perfil')}}" method="post" id="form_edit_imagen_perfil">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="imagen_perfil">Imagen de perfil</label>
                                    <input type="file" class="form-control file" id="imagen_perfil" name="imagen_perfil"
                                           accept="image/jpeg">
                                </div>
                            </div>
                            <input type="hidden" id="id_usuario" name="id_usuario" value="{{$usuario->id_usuario}}">
                            <button type="button" class="btn btn-block btn-success" onclick="update_image_perfil()">
                                <i class="fa fa-fw fa-save"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active tab-detalles">
                            <a href="#datos_usuarios" data-toggle="tab">Detalles</a>
                        </li>
                        <li class="tab-detalles">
                            <a href="#seguridad" data-toggle="tab">Seguridad</a>
                        </li>
                        <li class="tab-detalles">
                            <a href="#accesos_directos" data-toggle="tab">Accesos directos</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="datos_usuarios">
                            @include('perfil.partials.edit_usuario')
                        </div>
                        <div class="tab-pane" id="seguridad">
                            @include('perfil.partials.seguridad')
                        </div>
                        <div class="tab-pane" id="accesos_directos">
                            @include('perfil.partials.accesos_directos')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('perfil.script')
@endsection