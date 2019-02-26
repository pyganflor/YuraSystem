<form id="form-add_clasificacion_verde_x_variedad_{{$variedad->id_variedad}}">
    <ul class="nav nav-tabs nav-justified">
        <li role="presentation" class="active nav_header" id="li_nav_automatico">
            <a href="javascript:void(0)" onclick="select_nav('automatico')">Automático</a>
        </li>
        <li role="presentation" id="li_nav_manual" class="nav_header">
            <a href="javascript:void(0)" onclick="select_nav('manual')">Manual</a>
        </li>
    </ul>

    <div id="div_table_automatico" class="div_nav">
        @include('adminlte.gestion.postcocecha.clasificacion_verde.forms.partials._automatico')
    </div>

    <div id="div_table_manual" style="display: none" class="div_nav">
        @include('adminlte.gestion.postcocecha.clasificacion_verde.forms.partials._manual')
    </div>

    <input type="hidden" id="id_variedad" name="id_variedad" value="{{$variedad->id_variedad}}">
</form>

<script>
    function select_nav(option) {
        $('.nav_header').removeClass('active');
        $('.div_nav').hide();
        $('#li_nav_' + option).show();
        $('#div_table_' + option).show();
    }
</script>