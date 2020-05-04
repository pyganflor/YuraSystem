<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{url('storage/imagenes').'/'.getUsuario(Session::get('id_usuario'))->imagen_perfil}}" class="img-circle"
                     alt="User Image" id="img_perfil_menu_izquierda">
            </div>
            <div class="pull-left info">
                <p>{{getUsuario(Session::get('id_usuario'))->nombre_completo}}</p>
                {{--<a href="javascript:void(0)"
                   onclick="{if ($('#config_online').prop('checked')) $('#config_online').prop('checked', false); else $('#config_online').prop('checked', true); save_config();}">
                    <i class="fa fa-circle {{getUsuario(Session::get('id_usuario'))->configuracion->config_online == 'S' ? 'text-success' : 'text-danger'}}"></i>
                    {{getUsuario(Session::get('id_usuario'))->configuracion->config_online == 'S' ? 'Online' : 'Offline'}}
                </a>--}}
                <a href="javascript:void(0)" onclick="window.open('http://benchmark.yurasystem.com', '_blank')" class="pull-right">
                    <i class="fa fa-fw fa-caret-right"></i>Benchmark
                </a>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            @if(getUsuario(Session::get('id_usuario'))->rol()->estado == 'A')
                @foreach(getGrupoMenusOfUser(Session::get('id_usuario')) as $g)
                    <li class="header mouse-hand" onclick="$('.menu_{{$g->id_grupo_menu}}').toggleClass('hide')"
                        style="color: #e9ecef; font-size: 1em" onmouseover="$(this).css('color', '#0b93d5')"
                        onmouseleave="$(this).css('color', '#e9ecef')">
                        {{$g->nombre}}
                    </li>
                    @foreach($g->menus_activos as $m)
                        <li class="treeview menu_{{$g->id_grupo_menu}} hide">
                            <a href="#">
                                <i class="fa fa-{{$m->icono->nombre}}"></i>
                                <span>{{$m->nombre}}</span>
                                <span class="pull-right-container">
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                            </span>
                            </a>
                            <ul class="treeview-menu">
                                @foreach($m->menus_activosByUser(Session::get('id_usuario')) as $s)
                                    @if(isActive_action($s->id_submenu))
                                        <li>
                                            <a href="javascript:void(0)" onclick="cargar_url('{{$s->url}}')">
                                                <i class="fa fa-circle-o"></i> {!! $s->nombre !!}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                @endforeach
            @else
                <li class="header">NADA QUE MOSTRAR</li>
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>