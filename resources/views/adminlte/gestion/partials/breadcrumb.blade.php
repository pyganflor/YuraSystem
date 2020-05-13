<section class="content-header">
    <h1>
        {{$text['titulo']}}
        <small class="text-color_yura">{{$text['subtitulo']}}</small>
    </h1>

    <ol class="breadcrumb">
        <li><a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
        <li class="text-color_yura">
            {{$submenu->menu->grupo_menu->nombre}}
        </li>
        <li class="text-color_yura">
            {{$submenu->menu->nombre}}
        </li>

        <li class="active">
            <a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('{{$submenu->url}}')">
                <i class="fa fa-fw fa-refresh"></i> {{$submenu->nombre}}
            </a>
        </li>
    </ol>
</section>