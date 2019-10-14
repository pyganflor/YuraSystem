<div style="overflow-x: scroll;">
    @if(count($proyeccionVentaSemanalReal)>0)
    <table class="table-striped table-bordered table-hover" style="border: 2px solid #000000;" width="100%">
        <tr>
            {{--<th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
               <div class="input-group-btn">
                   <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                       <span class="fa fa-caret-down"></span></button>
                  <ul class="dropdown-menu">
                       <li>
                           <a href="javascript:void(0)" class="hide">
                               Crear masiva
                           </a>
                       </li>
                   </ul>
                    </div>
                </th>--}}
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;">
                Clientes / Semanas
            </td>
            @foreach(array_values($proyeccionVentaSemanalReal)[0] as $semana => $item)
                <td class="text-center" style="border:1px solid #9d9d9d; background-color: #e9ecef; width:350px;border-bottom: 2px solid #000000;border-right: 2px solid #000000;" colspan="3">{{$semana}}</td>
            @endforeach
        </tr>
        @foreach($proyeccionVentaSemanalReal as $cliente => $semana)
            <tr>
                <td class="text-center" style="border-left:2px solid #000000;border-right:2px solid #000000;border-top:2px solid #000000;width: 250px">
                    <div style="width:100%"><b>{{$cliente}}</b></div>
                </td>
                @foreach($semana as $s)
                    <td class="text-center" style="border-color: #9d9d9d; width:350px;border-right: 2px solid #000000;"  colspan="3"></td>
                @endforeach
            </tr>
            <tr>
                <td class="text-center" style="border-left:2px solid #000000;border-right:2px solid #000000;width: 250px">Real</td>

                @foreach($semana as $x=> $s)

                    <td style="border: 1px solid #9d9d9d;padding-left: 3px;" ><div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Cajas físicas"><b>{{$s['proyeccion']->cajas_fisicas}}</b></div></td>
                    <td style="border: 1px solid #9d9d9d;padding-left: 3px;" ><div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Cajas equivalentes"><b>{{number_format($s['proyeccion']->cajas_equivalentes,2,".","")}}</b></div></td>
                    <td style="border-bottom: 1px solid #9d9d9d;padding-left: 3px;border-right: 2px solid #000000" ><div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Valor"><b>${{number_format($s['proyeccion']->valor,2,".",",")}}</b></div></td>
                @endforeach
            </tr>
            <tr>
                <td class="text-center" style="border-bottom:2px solid #000000;border-left:2px solid #000000;border-right:2px solid #000000;width: 250px">Proyectado</td>
                @foreach($semana as $x => $s)
                    <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;padding-left: 3px"><div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Cajas físicas proyectadas"><b>{{$s['proyeccion']->cajas_fisicas_proy}}</b></div></td>
                    <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;padding-left: 3px"><div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Cajas equivalentes proyectadas"><b>{{number_format($s['proyeccion']->cajas_equivalentes_proy,2,".","")}}</b></div></td>
                    <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;padding-left: 3px;border-right: 2px solid #000000"><div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Valor proyectado"><b>${{number_format($s['proyeccion']->valor_proy,2,".",",")}}</b></div></td>
                @endforeach
            </tr>
        @endforeach
    </table>
    @else
        <div class="alert alert-info text-center" style="font-size:14px">No se encontraron registros</div>
    @endif
</div>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
