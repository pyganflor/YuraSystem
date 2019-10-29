@extends('layouts.adminlte.login.master')

@section('titulo')
    Login
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
    <div class="login-logo">
        <a href="{{url('')}}">
            <img src="{{url('images/logo_yura_full.png')}}" alt="" width="150px">
        </a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body sombra_estandar">
        <p class="login-box-msg">Ingrese sus credenciales para comenzar</p>

        <form action="{{url('login')}}" method="post" id="form_login">
            {!! csrf_field() !!}
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Nombre de usuario" id="username" name="username"
                       value="{{old('username')}}" autofocus required autocomplete="off" style="text-transform: lowercase">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Contraseña" id="password" name="password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>

            <input type="hidden" name="h_clave" id="h_clave" value="">

            <div class=" panel panel-default" id="div_captcha">
                <div class="text-center">
                    {{--{!! captcha_img() !!}--}}
                    {!! NoCaptcha::display() !!}
                </div>
                {{--<input class="form-control text-center" name="captcha" placeholder="Ingrese el código" autocomplete="off" required>--}}
            </div>

            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <button type="button" id="btn_login" class="btn btn-primary btn-block btn-flat">Comenzar</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script_final')
    <script>
        var div_captcha = $('#div_captcha>img');
        div_captcha.css('width', '100%');
        div_captcha.css('margin', '0');
        div_captcha.css('padding', '0');

        var password = $('#password');
        var formulario = $('#form_login');
        var h_clave = $('#h_clave');

        $('#btn_login').on('click', function () {
            if ($('#form_login').valid()) {
                $('#username').val($('#username').val().toLowerCase());
                var publickey = "{{$key}}";
                var rsakey = new RSAKey();
                rsakey.setPublic(publickey, "10001");
                console.log(rsakey);
                h_clave.val(rsakey.encrypt(password.val()));
                password.val('');

                //--------------------------------- post_ajax ----------------------------
                $.LoadingOverlay('show');
                var formData = new FormData(formulario[0]);
                //hacemos la petición ajax
                $.ajax({
                    url: formulario.attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    //necesario para subir archivos via ajax
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (retorno) {
                        if (retorno.success) {
                            location.href = '{{url('')}}';
                        } else {
                            alerta_accion(retorno.mensaje, function () {
                                cargar_url('');
                            });
                        }
                        $.LoadingOverlay('hide');
                    },
                    //si ha ocurrido un error
                    error: function (retorno) {
                        console.log(retorno);
                        alerta(retorno.responseText);
                        alert('Hubo un problema en la envío de la información');
                        $.LoadingOverlay('hide');
                    }
                });
            }
        });
    </script>
@endsection
