<div id="control-sidebar-config-tab" class="tab-pane active">
    <h4 class="control-sidebar-heading">Opciones de diseño</h4>
    <div class="form-group">
        <label class="control-sidebar-subheading">
            <input type="checkbox" class="pull-right" id="config_fixed_layout" onclick="set_config('')"
                    {{getUsuario(Session::get('id_usuario'))->configuracion->fixed_layout == 'S' ? 'checked' : ''}}> Diseño compacto
        </label>
        <p>Si esta opción está activa, el contenido se acloplará al tamaño de la pantalla</p>
    </div>
    <div class="form-group">
        <label class="control-sidebar-subheading">
            <input type="checkbox" class="pull-right" id="config_boxed_layout" onclick="set_config('')"
                    {{getUsuario(Session::get('id_usuario'))->configuracion->boxed_layout == 'S' ? 'checked' : ''}}> Diseño en caja
        </label>
        <p>Si esta opción está activa, el contenido se contraerá en forma de caja</p>
    </div>
    <div class="form-group">
        <label class="control-sidebar-subheading">
            <input type="checkbox" class="pull-right" id="config_color" onclick="set_config('')"
                    {{getUsuario(Session::get('id_usuario'))->configuracion->toggle_color_config == 'S' ? 'checked' : ''}}> Cambiar color del
            menú derecho
        </label>
    </div>
    <div class="form-group">
        <label class="control-sidebar-subheading">
            <input type="checkbox" class="pull-right" id="config_online" onclick="set_config('')"
                    {{getUsuario(Session::get('id_usuario'))->configuracion->config_online == 'S' ? 'checked' : ''}}> Mostrarme como online
        </label>
    </div>
    <div class="form-group">
        <label class="control-sidebar-subheading">
            <a style="cursor: pointer;" onclick="form_codigo_barra()">Generar código de barras</a>
        </label>
    </div>
    <div class="form-group">
        <label class="control-sidebar-subheading">
            <a style="cursor: pointer;" onclick="admin_colores()">Administrar colores</a>
        </label>
    </div>
    <h4 class="control-sidebar-heading">
        Temas
        @php
            $random_face = rand(0,99);
        @endphp
        @if($random_face == 90)
            <span style="color: #222d32" class="pull-right"> (◠‿◠)</span>
        @elseif($random_face == 91)
            <span style="color: #222d32" class="pull-right">(◕‿◕)</span>
        @elseif($random_face == 92)
            <span style="color: #222d32" class="pull-right">(◕‿-)</span>
        @elseif($random_face == 93)
            <span style="color: #222d32" class="pull-right">(╯°□°)╯</span>
        @elseif($random_face == 94)
            <span style="color: #222d32" class="pull-right">╭( ◕﹏◕ )╮</span>
        @elseif($random_face == 95)
            <span style="color: #222d32" class="pull-right">( ̯͡◕ ▽ ̯͡◕ )</span>
        @elseif($random_face == 96)
            <span style="color: #222d32" class="pull-right">(~￣▽￣)~</span>
        @elseif($random_face == 97)
            <span style="color: #222d32" class="pull-right">(｡^◕‿◕^｡)</span>
        @elseif($random_face == 98)
            <span style="color: #222d32" class="pull-right">(•̪◡•̪)</span>
        @elseif($random_face == 99)
            <span style="color: #222d32" class="pull-right">ʕ•ܫ•ʔ</span>
        @endif
    </h4>
    <ul class="list-unstyled clearfix">
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-blue')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9"></span>
                    <span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin">Azul</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-black')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix">
                    <span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe"></span>
                    <span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin">Blanco</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-purple')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span>
                    <span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin">Morado</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-green')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span>
                    <span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin">Verde</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-red')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span>
                    <span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin">Rojo</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-yellow')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span>
                    <span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin">Naranja</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-blue-light')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9"></span>
                    <span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin" style="font-size: 12px">Azul claro</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-black-light')"
               style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix">
                    <span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe"></span>
                    <span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin" style="font-size: 12px">Blanco total</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-purple-light')"
               style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span>
                    <span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin" style="font-size: 12px">Morado claro</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-green-light')"
               style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span>
                    <span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin" style="font-size: 12px">Verde claro</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" onclick="set_config('skin-red-light')" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span>
                    <span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin" style="font-size: 12px">Rojo claro</p>
        </li>
        <li style="float:left; width: 33.33333%; padding: 5px;">
            <a href="javascript:void(0)" data-skin="skin-yellow-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
               class="clearfix full-opacity-hover">
                <div>
                    <span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span>
                    <span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span>
                </div>
                <div>
                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span>
                    <span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                </div>
            </a>
            <p class="text-center no-margin" style="font-size: 12px">Naranja claro</p>
        </li>
    </ul>

    <input type="hidden" id="skin_config" name="skin_config" value="skin-black">

    <button type="button" class="btn btn-block btn-success" onclick="save_config()"><i class="fa fa-fw fa-save"></i> Guardar</button>
</div>
