<input type="hidden" id="id_recepcion" value="{{$recepcion->id_recepcion}}">
<div class="row">
    <div class="col-md-8">
        <table width="100%" class="table table-responsive table-bordered sombra_estandar"
               style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_detalles">
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Fecha de ingreso
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{convertDatetimeToText($recepcion->fecha_ingreso)}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Semana
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$recepcion->semana->codigo}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Tallos
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    <strong>{{$recepcion->cantidad_tallos()}}</strong>
                    <input type="hidden" id="total_recepcion" value="{{$recepcion->cantidad_tallos()}}">
                    <ul class="text-left">
                        @foreach(getRecepcion($recepcion->id_recepcion)->desgloses as $item)
                            <li>
                                {{$item->variedad->planta->nombre}} - {{$item->variedad->siglas}}:
                                <strong>{{$item->cantidad_mallas}}</strong> mallas
                                de <strong>{{$item->tallos_x_malla}}</strong> tallos =
                                <strong>{{$item->cantidad_mallas * $item->tallos_x_malla}}</strong>
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4">
        <div class="list-group">
            <a href="javascript:void(0)" class="list-group-item list-group-item-action active">
                Opciones
            </a>
            {{------------------- Enlaces para documentos -------------------}}
            @if(count(getDocumentos('recepcion', $recepcion->id_recepcion))>0)
                <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                   onclick="ver_documentos('recepcion', '{{$recepcion->id_recepcion}}')">
                    Ver información personalizada
                </a>
            @endif
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
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