<form id="form_despacho_{{$cant_form+1}}" class="form-horizontal sombra_estandar">
    <table width="100%" class="table-responsive table-bordered" style=" border-color: white;margin-top:20px" id="table_despacho_{{$cant_form+1}}">
        <tr>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"><b>Transportisa</b></td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <select id="id_transportista" name="id_transportista" style="width: 100%;border: none;" required>
                    @foreach ($transportistas as $t)
                        <option value="{{$t->id_transportista}}">{{$t->nombre_empresa}}</option>
                    @endforeach
                </select>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Camión</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <select id="id_camion" style="width: 100%;border: none" onchange="busqueda_placa_camion('form_despacho_{{$cant_form+1}}')" required></select>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Placa</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="n_placa" style="width: 100%;border: none;" required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Chofer</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <select id="id_chofer" style="width: 100%;border: none;" required></select>
            </td>
        </tr>
        <tr>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Fecha</b>
            </td>
            <td style="border-color: #9d9d9d;vertical-align: middle">
                <input type="date" id="fecha_despacho" name="fecha_despacho" value="{{now()->format('Y-m-d')}}"
                       style="width: 100%;border: none;" required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Sello de salida</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="sello_salida" name="sello_salida" style="width: 100%;border: none;">
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Responsable</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle" >
                <input type="text" id="responsable" name="responsable" onkeyup="duplicar_nombre(this)" style="width: 100%;border: none;" value="{{isset($resp_transporte->resp_transporte) ? $resp_transporte->resp_transporte : ""}}" required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Horario</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="horario" name="horario" style="width: 100%;border: none;" >
            </td>
        </tr>
        <tr>
            <td  class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Semana</b>
            </td>
            <td style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="semana" name="semana" value="{{getSemanaByDate(now()->toDateString())->codigo}}"
                       readonly style="width: 100%;border: none;" required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Rango Tmp</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="rango_temp" name="rango_temp" style="width: 100%;border: none;" >
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Sellos entregados</b>
            </td>
            <td class="text-center" id="cant_sellos" style="border-color: #9d9d9d;vertical-align: middle"></td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Sello adicional</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="sello_adicional" name="sello_adicional" style="width: 100%;border: none;" >
            </td>
        </tr>
        <tr>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Viaje N#</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input value="{{$cant_form+1}}" type="number" id="n_viaje" name="n_viaje" style="width: 100%;border: none;" required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Hora de salida</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="horas_salida" name="horas_salida" style="width: 100%;border: none;">
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Temperatura</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="temperatura" name="temperatura" style="width: 100%;border: none;" >
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Kilometraje</b>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <input type="text" id="kilometraje" name="kilometraje" style="width: 100%;border: none;" >
            </td>
        </tr>
        <tr>
            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                <b>Sellos</b>
            </td>
            @for ($i = 0; $i < 7; $i++)
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" class="sello" name="sello" style="width: 100%;border: none;" {{--{{$i==0 ? "required" : ""}}--}}>
                </td>
            @endfor
        </tr>

    </table>
</form>
<script>
    busqueda_camiones_conductores('form_despacho_{{$cant_form+1}}');

    add_pedido_piezas('table_despacho_{{$cant_form+1}}');

    function add_pedido_piezas(tabla) {

        cant_tr_pedido_piezas = $("table#"+tabla+" #tr_pedido_piezas").length;
        arr_pedidos = [];
        $.each($("input.id_pedido"),function(i,j){ arr_pedidos.push(j.value); });
        datos = {
            secuencial : cant_tr_pedido_piezas+1,
            arr_pedidos : arr_pedidos,
            cant_form : tabla.split("_")[2],
        };

        if(datos.secuencial > arr_pedidos.length){
            msg = " El despacho solo posee "+arr_pedidos.length+" pedidos";
            setInterval(function () { msg = "" },2000);
            $("#msg").html('<span style="color: red"><i class="fa fa-exclamation-triangle"></i> '+msg+'</span>');
            return false;
        }else{
            msg = "";
            $("#msg").html('<span style="color: red">'+msg+'</span>');
        }

        $.LoadingOverlay('show');
        $.get('{{url('despachos/add_pedido_piezas')}}', datos, function (retorno) {
            $("#"+tabla).append(retorno);
            if(datos.cant_form == 1){
                $("#full_1_1").val(parseInt($("#full_box").html()));
                $("#half_1_1").val(parseInt($("#half_box").html()));
                $("#cuarto_1_1").val(parseInt($("#cuarto_box").html()));
                $("#sexto_1_1").val(parseInt($("#sexto_box").html()));
                $("#octavo_1_1").val(parseInt($("#octavo_box").html()));
                $("#piezas_x_camion_1").html(parseInt($("#full_box").html())+parseInt($("#half_box").html())+parseInt($("#cuarto_box").html())+parseInt($("#sexto_box").html())+parseInt($("#octavo_box").html()));
            }else{
                $("select.pedido").attr('required',true);
            }
            if(datos.secuencial > 1 ) $("select.pedido").attr('required',true);
            for (i=1;i<=datos.cant_form;i++){
                for (x=1;x<=$("input.caja").length;x++){
                    if(parseInt($("#full_box").html()) === 0) $("#full_"+i+"_"+x).attr('readonly',true);
                    if(parseInt($("#half_box").html()) === 0) $("#half_"+i+"_"+x).attr('readonly',true);
                    if(parseInt($("#cuarto_box").html()) === 0) $("#cuarto_"+i+"_"+x).attr('readonly',true);
                    if(parseInt($("#sexto_box").html()) === 0) $("#sexto_"+i+"_"+x).attr('readonly',true);
                    if(parseInt($("#octavo_box").html()) === 0) $("#octavo_"+i+"_"+x).attr('readonly',true);
                }
            }
            $("#"+tabla+" td#td_piezas").attr('rowspan',datos.secuencial);
            $("#"+tabla+" td#td_btn").attr('rowspan',datos.secuencial);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function delete_pedido_piezas(tabla){
        if($("table#"+tabla+" #tr_pedido_piezas").length > 1 )
            $("table#"+tabla+" tr#tr_pedido_piezas:last-child").remove();

        cant_tr_pedido_piezas = $("table#"+tabla+" #tr_pedido_piezas").length;
        if(cant_tr_pedido_piezas < 2 )  $("select.pedido").removeAttr('required');
    }

    function calcular_piezas(tabla){

        piezasxcamion = 0;
        piezas_camion = 0;
        $.each($("input.caja"),function (i,j) { if(j.value > 0) piezasxcamion += parseInt(j.value); });
        if(piezasxcamion > parseInt($("span#piezas_totales").html())){
            msg = "<span style='color: red'><i class='fa fa-exclamation-triangle'></i> El total de las piezas distribuidas en los camiones sobrepasan la cantidad total de piezas de los pedidos</span>";
            $("#btn_guardar_modal_despacho").attr('disabled',true);
        }else if(piezasxcamion < parseInt($("span#piezas_totales").html())){
            msg = "<span style='color: red;'><i class='fa fa-exclamation-triangle'></i> Aún faltan piezas por distribuir</span>";
            $("#btn_guardar_modal_despacho").attr('disabled',true);
        }else{
            msg = "<span style='color: green'><i class='fa fa-check-circle'></i> Distribución completa</span>";
            $("#btn_guardar_modal_despacho").removeAttr('disabled');
        }
        $("#msg").html(msg);

        $.each($("table#"+tabla+" input.caja"),function (i,j) { if(j.value > 0)  piezas_camion += parseInt(j.value); });
        $("table#"+tabla+" span.piezas_x_camion").html(piezas_camion);

    }
</script>
