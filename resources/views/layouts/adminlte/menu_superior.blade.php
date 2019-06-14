<header class="main-header">
    <!-- Logo -->
    <a href="{{url('')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
            <img src="{{url('images/logo_yura.png')}}" alt="" width="30px">
        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img src="{{url('images/logo_yura_full.png')}}" alt="" width="60px">
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Menú</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{url('storage/imagenes').'/'.getUsuario(Session::get('id_usuario'))->imagen_perfil}}" class="user-image"
                             alt="User Image" id="img_perfil_menu_superior">
                        <span class="hidden-xs">{{getUsuario(Session::get('id_usuario'))->nombre_completo}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="{{url('storage/imagenes').'/'.getUsuario(Session::get('id_usuario'))->imagen_perfil}}"
                                 class="img-circle" alt="User Image" id="img_perfil_menu_superior_2">

                            <p>
                                {{getUsuario(Session::get('id_usuario'))->nombre_completo}}
                                <small>Miembro desde {{substr(getUsuario(Session::get('id_usuario'))->fecha_registro,0,10)}}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="javascript:void(0)" class="btn btn-default btn-flat" onclick="cargar_url('perfil')">
                                    Mi Perfil
                                </a>
                            </div>
                            <div class="pull-right">
                                <a href="javascript:void(0)" onclick="cargar_url('logout')" class="btn btn-default btn-flat">Salir</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>