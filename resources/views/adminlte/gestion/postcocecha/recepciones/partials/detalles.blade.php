<input type="hidden" id="id_recepcion" value="{{$recepcion->id_recepcion}}">
<div class="row">
    <div class="col-md-8">
        <table width="100%" class="table table-responsive table-bordered sombra_estandar"
               style="font-size: 0.8em; border-color: #9d9d9d;" id="table_content_detalles">
            <tr>
                <th class="text-center th_yura_default" style="border-color: #9d9d9d">
                    Fecha de ingreso
                </th>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{convertDatetimeToText($recepcion->fecha_ingreso)}}
                </td>
            </tr>
            <tr>
                <th class="text-center th_yura_default" style="border-color: #9d9d9d">
                    Semana
                </th>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$recepcion->semana->codigo}}
                </td>
            </tr>
            <tr>
                <th class="text-center th_yura_default" style="border-color: #9d9d9d">
                    Tallos
                </th>
                <td class="text-center" style="border-color: #9d9d9d">
                    <strong>{{$recepcion->cantidad_tallos()}}</strong>
                    <input type="hidden" id="total_recepcion" value="{{$recepcion->cantidad_tallos()}}">
                    <ul class="text-left">
                        @foreach(getRecepcion($recepcion->id_recepcion)->desgloses as $item)
                            <li>
                                <a href="javascript:void(0)" class="text-color_yura" onclick="editar_desglose_recepcion('{{$item->id_desglose_recepcion}}')">
                                    {{$item->variedad->planta->nombre}} - {{$item->variedad->siglas}}:
                                    <strong class="text-black">{{$item->cantidad_mallas}}</strong> mallas
                                    de <strong class="text-black">{{$item->tallos_x_malla}}</strong> tallos =
                                    <strong class="text-black">{{$item->cantidad_mallas * $item->tallos_x_malla}}</strong> módulo:
                                    <strong class="text-black">{{$item->modulo->nombre}}</strong>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4">
        <div class="list-group">
            <a href="javascript:void(0)" class="list-group-item list-group-item-action active bg-yura_dark">
                Opciones
            </a>
            {{------------------- Enlaces para documentos -------------------}}
            @if(count(getDocumentos('recepcion', $recepcion->id_recepcion))>0)
                <a href="javascript:void(0)" class="list-group-item list-group-item-action text-color_yura"
                   onclick="ver_documentos('recepcion', '{{$recepcion->id_recepcion}}')">
                    Ver información personalizada
                </a>
            @endif
            <a href="#div_content_opciones" class="list-group-item list-group-item-action text-color_yura"
               onclick="cargar_opcion('add_desglose', '{{$recepcion->id_recepcion}}')">
                Añadir desglose
            </a>
            <a href="#div_content_opciones" class="list-group-item list-group-item-action text-color_yura"
               onclick="add_info('{{$recepcion->id_recepcion}}')">
                Añadir información personalizada
            </a>
        </div>
    </div>
</div>

@if(count(getDocumentos('recepcion', $recepcion->id_recepcion))>0)
    <div id="content_documentos"></div>
@endif
<legend></legend>
<div id="div_content_opciones" style="margin-top: 10px"></div>