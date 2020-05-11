<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        Yura - @yield('titulo')
    </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link href="{{url('images/logo_yura.png')}}" rel="shortcut icon">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{url('adminlte')}}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('adminlte')}}/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{url('adminlte')}}/bower_components/Ionicons/css/ionicons.min.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{url('adminlte')}}/bower_components/jvectormap/jquery-jvectormap.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{url('adminlte')}}/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{url('adminlte')}}/dist/css/skins/_all-skins.min.css">

    {{-- Select Multiple --}}
    <link rel="stylesheet" href="{{url('adminlte')}}/bower_components/select2/dist/css/select2.min.css">

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

    @yield('css_inicio')
    @yield('script_inicio')
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

@include('layouts.adminlte.menu_superior')
<!-- Left side column. contains the logo and sidebar -->
@include('layouts.adminlte.menu_izquierda')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('contenido')
    </div>

    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.4.0
        </div>
        <strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights
        reserved.
    </footer>

    <!-- Control Sidebar -->
@include('layouts.adminlte.opciones_derecha')
<!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{url('adminlte')}}/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{url('adminlte')}}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="{{url('adminlte')}}/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="{{url('adminlte')}}/dist/js/adminlte.min.js"></script>
<!-- Sparkline -->
<script src="{{url('adminlte')}}/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap  -->
<script src="{{url('adminlte')}}/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="{{url('adminlte')}}/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll -->
<script src="{{url('adminlte')}}/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS -->
<script src="{{url('adminlte')}}/bower_components/chart.js/Chart.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{--<script src="{{url('adminlte')}}/dist/js/pages/dashboard2.js"></script>--}}
<!-- AdminLTE for demo purposes -->
<script src="{{url('adminlte')}}/dist/js/demo.js"></script>

<!-- LoadingOverlay -->
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.5/dist/loadingoverlay.min.js"></script>

{{-- Select Multiple --}}
<script src="{{url('adminlte')}}/bower_components/select2/dist/js/select2.full.min.js"></script>

<script src="{{url('adminlte')}}/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="{{url('adminlte')}}/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
    //Para generar un BEEP en javascript ---------------------------------------------------------------------------------
    function beep() {
        var snd = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
        snd.play();
    }

    function inicializa(elemento) {
        if (!elemento || elemento === 'file') {
            $(".file").fileinput({
                language: 'es',
                showUpload: false,
                showRemove: false,
                browseLabel: '',
                previewFileIconSettings: {
                    'doc': '<i class="fa fa-file-word-o text-primary"></i>',
                    'xls': '<i class="fa fa-file-excel-o text-success"></i>',
                    'ppt': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                    'jpg': '<i class="fa fa-file-photo-o text-warning"></i>',
                    'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
                    'zip': '<i class="fa fa-file-archive-o text-muted"></i>'
                },
                previewFileExtSettings: {
                    'doc': function (ext) {
                        return ext.match(/(doc|docx)$/i);
                    },
                    'xls': function (ext) {
                        return ext.match(/(xls|xlsx)$/i);
                    },
                    'ppt': function (ext) {
                        return ext.match(/(ppt|pptx)$/i);
                    }
                }
            });
        }

        //Inicializar summernote (Editor HTML) para la clase editorHTML -------------------------------------
        if (!elemento || elemento === 'editorPro') {
            CKEDITOR.replace('editorPro');
        }

        //Inicializar summernote (Editor HTML) para la clase editorHTML -------------------------------------
        if (!elemento || elemento === 'editor') {
            $('.editor').wysihtml5();
        }

        //Para añadir un contador de caranteres en orden descendente a lo que tenga la clase contador -----
        if (!elemento || elemento === 'contador') {
            contador = $('.contador');
            contador.after('<div class="text-count-wrapper"></div>');
            contador.textcounter({
                type: 'character',
                max: 'auto',
                countSpaces: true,
                countDown: true,
                countDownText: '<span class="badge pull-right">%d</span>'
            });
        }

        //Para inicializar un timepicker -----
        if (!elemento || elemento === 'timepicker') {
            $('.timepicker').timepicker({
                showInputs: false
            })
        }
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
            ' Se detectaron los siguientes errores',
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
                $('#btn_cerrar_' + id_modal).addClass('btn-yura_default');
                $('#btn_guardar_' + id_modal).addClass('btn-yura_default');
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

    function modal_view(id_modal, mensaje, title, draggable, closable, size) {
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
                $('#btn_cerrar_' + id_modal).addClass('btn-yura_default');
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
                }
            ]
        });
    }

    function modal_quest(id_modal, mensaje, title, draggable, closable, size, accion) {
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
                $('#btn_no_' + id_modal).addClass('btn-yura_default');
                $('#btn_continue_' + id_modal).addClass('btn-yura_default');
            },
            callback: function () {
                arreglo_modals_form = [];
            },
            buttons: [
                {
                    id: 'btn_no_' + id_modal,
                    label: 'No',
                    icon: 'fa fa-fw fa-times',
                    action: function (modal) {
                        modal.close();
                    }
                }, {
                    id: 'btn_continue_' + id_modal,
                    label: 'Continuar',
                    icon: 'fa fa-fw fa-check',
                    cssClass: 'btn btn-primary',
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

    /* =============== Variables para configuracion =====================*/
    var $pushMenu = $('[data-toggle="push-menu"]').data('lte.pushmenu')
    var $controlSidebar = $('[data-toggle="control-sidebar"]').data('lte.controlsidebar')
    var $layout = $('body').data('lte.layout')
    $(window).on('load', function () {
        // Reinitialize variables on load
        $pushMenu = $('[data-toggle="push-menu"]').data('lte.pushmenu')
        $controlSidebar = $('[data-toggle="control-sidebar"]').data('lte.controlsidebar')
        $layout = $('body').data('lte.layout')
    });
    var mySkins = [
        'skin-blue',
        'skin-black',
        'skin-red',
        'skin-yellow',
        'skin-purple',
        'skin-green',
        'skin-blue-light',
        'skin-black-light',
        'skin-red-light',
        'skin-yellow-light',
        'skin-purple-light',
        'skin-green-light'
    ];

    function set_config(skin) {
        // fixed layout config
        if ($('#config_fixed_layout').prop('checked')) {
            $('body').addClass('fixed');
            //$pushMenu.expandOnHover();
            //$layout.activate();
        } else {
            $('body').removeClass('fixed');
        }
        // boxed layout config
        if ($('#config_boxed_layout').prop('checked')) {
            $('body').addClass('layout-boxed');
            $controlSidebar.fix();
        } else {
            $('body').removeClass('layout-boxed');
        }
        // color config
        if ($('#config_color').prop('checked')) {
            $('#barra_control').addClass('control-sidebar-light');
            $('#barra_control').removeClass('control-sidebar-dark');
        } else {
            $('#barra_control').removeClass('control-sidebar-light');
            $('#barra_control').addClass('control-sidebar-dark');
        }
        //skins
        if (skin == '') {
            skin = $('#skin_config').val();
        } else {
            $('#skin_config').val(skin);
        }

        $.each(mySkins, function (i) {
            $('body').removeClass(mySkins[i]);
        });
        $('body').addClass(skin);
        if (typeof (Storage) !== 'undefined') {
            localStorage.setItem('skin', skin);
        } else {
            window.alert('Please use a modern browser to properly view this template!');
        }

        //$layout.fixSidebar();
    }

    function save_config() {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            fixed_layout: $('#config_fixed_layout').prop('checked'),
            boxed_layout: $('#config_boxed_layout').prop('checked'),
            color_config: $('#config_color').prop('checked'),
            skin: $('#skin_config').val(),
            config_online: $('#config_online').prop('checked'),
        };
        $.post('{{url('save_config_user')}}', datos, function (retorno) {
            if (retorno.success) {
                alerta_accion(retorno.mensaje, function () {
                    location.reload();
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

    function cerrar_modals() {
        for (i = 0; i < arreglo_modals_form.length; i++) {
            arreglo_modals_form[i].close();
        }
        arreglo_modals_form = [];
    }

    function estructura_tabla(id) {
        $('#' + id).DataTable({
            order: [],
            responsive: true,
            paging: false,
            info: false,
            search: false,
            columnDefs: [
                {
//                  targets: [9],
                    searchable: false,
                    orderable: false
                }
            ],
            language: {
                sSearch: "Filtrar en este listado: "
            }
        });
    }

    // FUNCION PARA MODIFICAR "MIN = HOY" A UN INPUT "DATE"
    function set_min_today(entrada) {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }

        today = yyyy + '-' + mm + '-' + dd;

        if (entrada.prop('type') == 'datetime-local') {
            var hh = today.getHours();
            var minutes = today.getMinutes();
            today += ' ' + hh + ':' + minutes;
        }

        alert(today);

        entrada.prop('min', today);
        entrada.val(today);
    }

    // FUNCION PARA MODIFICAR "MAX = HOY" A UN INPUT "DATE"
    function set_max_today(entrada) {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }


        if (entrada.prop('type') == 'datetime-local') {
            var hh = today.getHours();
            var minutes = today.getMinutes();
            today = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + minutes;
        } else {
            today = yyyy + '-' + mm + '-' + dd;
        }

        entrada.prop('max', today);
        entrada.val(today);
    }

    // FUNCION PARA SUMAR DIAS AL DIA DE HOY
    function sum_dias(dias) {
        var fecha = new Date();
        fecha.setDate(fecha.getDate() + dias);

        var dd = fecha.getDate();
        var mm = fecha.getMonth() + 1; //January is 0!
        var yyyy = fecha.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }

        fecha = yyyy + '-' + mm + '-' + dd;

        return fecha;
    }

    // Permitir solo numeros como entrada en un input
    function isNumber(event) {
        return (event.keyCode >= 48 && event.keyCode <= 57);
    }

    /* =============== Configuracion de LoadingOverlay ================*/
    $.LoadingOverlaySetup({
        background: "rgba(0, 0, 0, 0.5)",
        image: "{{url('images/logo_yura.png')}}",
        imageAnimation: "1.5s fadein",
        imageColor: "#ffcc00"
    });

    /* =============== Añadir Informacion Personalizada ===================*/
    function add_documento(entidad, codigo, accion) {
        $.LoadingOverlay('show');
        datos = {
            entidad: entidad,
            codigo: codigo
        };
        get_jquery('{{url('documento/add_documento')}}', datos, function (retorno) {
            modal_form('modal_add_documento', retorno, '<i class="fa fa-fw fa-object-group"></i> Añadir información personalizada', true, false, '{{isPC() ? '50%' : ''}}', function () {
                if ($('#form-add_documento').valid()) {
                    $.LoadingOverlay('show');
                    arreglo_forms_documento = [];
                    cant_doc = $('#cant_doc').val();
                    for (i = 1; i <= cant_doc; i++) {
                        data = {
                            nombre_campo: $('#nombre_campo_' + i).val(),
                            tipo_dato: $('#tipo_dato_' + i).val(),
                            valor: $('#valor_' + i).val(),
                            descripcion: $('#descripcion_' + i).val(),
                        };
                        arreglo_forms_documento.push(data);
                    }
                    datos = {
                        _token: '{{csrf_token()}}',
                        entidad: entidad,
                        codigo: codigo,
                        arreglo: arreglo_forms_documento
                    };
                    post_jquery('{{url('documento/store_documento')}}', datos, function () {
                        accion();
                        cerrar_modals();
                    });
                    $.LoadingOverlay('hide');
                }
            });
        });
        $.LoadingOverlay('hide');
    }

    function ver_documentos(entidad, codigo) {
        $.LoadingOverlay('show');
        datos = {
            entidad: entidad,
            codigo: codigo,
        };
        get_jquery('{{url('documento/ver_documentos')}}', datos, function (retorno) {
            $('#content_documentos').html(retorno);
        });
        $.LoadingOverlay('hide');
    }

    function update_documento(id, codigo, entidad) {
        if ($('#form-update_documento_' + id).valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_documento: id,
                nombre_campo: $('#nombre_campo_' + id).val(),
                valor: $('#valor_' + id).val(),
                descripcion: $('#descripcion_' + id).val(),
            };
            post_jquery('{{url('documento/update_documento')}}', datos, function () {
                ver_documentos(entidad, codigo);
            });
            $.LoadingOverlay('hide');
        }
    }

    function delete_documento(id, codigo, entidad) {
        modal_quest('modal_quest_del_documento', '<div class="alert alert-info text-center">¿Está seguro de eliminar esta información?</div>',
            '<i class="fa fa-fw fa-trash"></i> Eliminar información', true, false, '{{isPC() ? '35%' : ''}}', function () {
                $.LoadingOverlay('show');
                datos = {
                    _token: '{{csrf_token()}}',
                    id_documento: id
                };
                post_jquery('{{url('documento/delete_documento')}}', datos, function () {
                    cerrar_modals();
                });
                $.LoadingOverlay('hide');
            });
    }

    set_config('');


</script>

@yield('css_final')
@yield('script_final')
</body>
</html>