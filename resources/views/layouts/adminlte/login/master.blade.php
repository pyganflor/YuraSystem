<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Yura - @yield('titulo')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->

    <link href="{{url('images/logo_yura.png')}}" rel="shortcut icon">

    <link rel="stylesheet" href="{{url('adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('adminlte/bower_components/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{url('adminlte/bower_components/Ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{url('adminlte/dist/css/AdminLTE.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{url('adminlte/plugins/iCheck/square/blue.css')}}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link rel="stylesheet" href="{{url('css/yura_estilos.css')}}">

    <!-- Bootstrap Validator -->
    <link href="{{url('css/bootstrapValidator.css')}}" rel="stylesheet" type="text/css">

    @yield('css_inicio')
    @yield('script_inicio')
</head>
<body class="hold-transition login-page">
<div class="login-box">
    @yield('contenido')
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="{{url('adminlte/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{url('adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- iCheck -->
<script src="{{url('adminlte/plugins/iCheck/icheck.min.js')}}"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });
    });
</script>

<!-- BootstrapDialog -->
<script src="{{url('bootstrap3-dialog-master/dist/js/bootstrap-dialog.min.js')}}" type="text/javascript"></script>

<!-- LoadingOverlay -->
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.5/dist/loadingoverlay.min.js"></script>

<!-- Bootstrap Validation -->
<script src="{{url('js/jquery.validate/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{url('js/jquery.validate/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{url('js/jquery.validate/messages_es.min.js')}}" type="text/javascript"></script>

<script>
    //Para generar un BEEP en javascript ---------------------------------------------------------------------------------
    function beep() {
        var snd = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
        snd.play();
    }

    //Sustituto de la funcion alert de javascrip por aleta en ventana modal html -------------
    function alerta(mensaje, foco) {
        BootstrapDialog.alert({
            title: '<i class="fa fa-info-circle"></i> Información',
            message: $('<div></div>').html(mensaje),
            buttonLabel: 'Aceptar',
            closable: false,
            draggable: true,
            callback: function () {
                if (!!foco) {
                    var myVar = setInterval(function () {
                        var elemento = $(foco);
                        if (!!elemento) {
                            elemento.select();
                            elemento.focus();
                        }
                        clearInterval(myVar);
                    }, 500);
                }
            },
            onshown: function (dialogItself) {
                beep();
            }
        });
    }

    function alerta_accion(mensaje, accion) {
        BootstrapDialog.alert({
            title: '<i class="fa fa-info-circle"></i> Información',
            message: $('<div></div>').html(mensaje),
            buttonLabel: 'Aceptar',
            closable: false,
            draggable: true,
            callback: function () {
                if (!!accion) {
                    accion();
                }
            },
            onshown: function (dialogItself) {
                beep();
            }
        });
    }

    function alerta_errores(mensaje, accion) {
        BootstrapDialog.alert({
            type: BootstrapDialog.TYPE_DANGER,
            closable: true,
            draggable: true,
            title: '<i class="fa fa-exclamation-triangle"></i>' +
            'Se detectaron los siguientes errores',
            message: $('<div></div>').html(mensaje),
            buttonLabel: 'Aceptar',
            callback: function () {
                if (!!accion) {
                    accion();
                }
            },
            onshown: function (modal) {
                beep();
                $('#' + modal.getId() + '>div').css('width', '95%');
            }
        });
    }

    arreglo_modals_form = [];

    function modal_form(id_modal, mensaje, title, draggable, closable, size, accion) {
        BootstrapDialog.show({
            title: title,
            closable: closable,
            draggable: draggable,
            message: $('<div></div>').html(mensaje),
            onshown: function (modal) {
                $('#' + modal.getId()).css('overflow-y', 'scroll');
                $('#' + modal.getId() + '>div').css('width', size);
                modal.setId(id_modal);
                arreglo_modals_form.push(modal);
            },
            callback: function () {
                arreglo_modals_form = [];
            },
            buttons: [
                {
                    id: 'btn_cerrar_' + id_modal,
                    label: 'Cerrar',
                    icon: 'fa fa-fw fa-times',
                    action: function (modal) {
                        modal.close();
                    }
                }, {
                    id: 'btn_guardar_' + id_modal,
                    label: 'Guardar',
                    icon: 'fa fa-fw fa-save',
                    cssClass: 'btn btn-success',
                    action: function (modal) {
                        if (!!accion) {
                            accion();
                        } else {
                            modal.close();
                        }
                    }
                }
            ]
        });
    }

    function post_jquery(url, datos, success) {
        $.LoadingOverlay('show');
        $.post(url, datos, function (retorno) {
            if (retorno.success) {
                alerta_accion(retorno.mensaje, function () {
                    success();
                });
            } else {
                alerta(retorno.mensaje);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
            alerta('Ha ocurrido un problema al enviar la información');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function get_jquery(url, datos, funcion) {
        $.LoadingOverlay('show');
        $.get(url, datos, function (retorno) {
            funcion(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function post_ajax_admin(formulario, success, m, callback) {
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
                if (!m) {
                    if (retorno.success) {
                        alerta_accion(retorno.mensaje, function () {
                            success();
                        });
                    } else {
                        alerta(retorno.mensaje);
                    }
                } else {
                    $.LoadingOverlay('hide');
                    msg += '' + retorno.mensaje;
                    callback(msg);
                }
                $.LoadingOverlay('hide');
            },
            //si ha ocurrido un error
            error: function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
                alerta('Hubo un problema en la envío de la información');
                $.LoadingOverlay('hide');
            }
        });
    }

    function cargar_url(url) {
        $.LoadingOverlay('show');
        location.href = '{{url('')}}/' + url;
        $.LoadingOverlay('hide');
    }

    /* =============== Configuracion de LoadingOverlay ================*/
    $.LoadingOverlaySetup({
        background: "rgba(0, 0, 0, 0.5)",
        image: "{{url('images/logo_yura.png')}}",
        imageAnimation: "1.5s fadein",
        imageColor: "#ffcc00"
    });
</script>

@yield('css_final')
@yield('script_final')
</body>
</html>
