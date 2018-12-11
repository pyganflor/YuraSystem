<section class="content-header">
    <h1>
        {{$text['titulo']}}
        <small>{{$text['subtitulo']}}</small>
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