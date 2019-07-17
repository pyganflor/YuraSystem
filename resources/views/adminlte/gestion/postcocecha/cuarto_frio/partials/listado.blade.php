@if(count($inventarios) > 0)
    <div style="overflow-x: scroll">
        <table class="table-bordered table-striped" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em" id="table_cuarto_frio">
            <thead>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                    rowspan="2">
                    Variedad
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                    rowspan="2">
                    Calibre
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                    rowspan="2">
                    Presentación
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                    rowspan="2">
                    Tallos x ramo
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                    rowspan="2">
                    Longitud
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                    colspan="10">
                    Días
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" rowspan="2"
                    style="border-color: #9d9d9d">
                    Total
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" rowspan="2"
                    style="border-color: #9d9d9d">
                    Cajas
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" rowspan="2"
                    style="border-color: #9d9d9d">
                    Opciones
                </th>
            </tr>
            <tr>
                @for($i = 0; $i <= 9; $i++)
                    @php
                        $fecha = opDiasFecha('-',$i,date('Y-m-d'));
                        $fecha = getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime(substr($fecha,0,10))))].' '.
                                            convertDateToText(substr($fecha,0,10))
                    @endphp
                    <th class="text-center" style="border-color: #0a0a0a; background-color: #e9ecef" width="40px" title="{{$fecha}}">
                    <span style="padding: 2px">
                        {{$i == 9 ? $i.'...' : $i}}
                    </span>
                    </th>
                @endfor
            </tr>
            </thead>

            <tbody>
            @php
                $total_ramos = 0;
                $total_cajas = 0;
            @endphp
            @foreach($inventarios as $pos_inv => $inv)
                <tr onmouseover="$(this).addClass('bg-aqua')" onmouseleave="$(this).removeClass('bg-aqua')">
                    <th class="text-center" style="border-color: #9d9d9d">
                        <input type="hidden" id="variedad_{{$pos_inv}}" value="{{$inv['variedad']->id_variedad}}">
                        <input type="hidden" id="peso_{{$pos_inv}}" value="{{$inv['peso']->id_clasificacion_ramo}}">
                        <input type="hidden" id="nombre_peso_{{$pos_inv}}" value="{{explode('|', $inv['peso']->nombre)[0]}}">
                        <input type="hidden" id="presentacion_{{$pos_inv}}" value="{{$inv['presentacion']->id_empaque}}">
                        <input type="hidden" id="tallos_x_ramo_{{$pos_inv}}" value="{{$inv['tallos_x_ramo']}}">
                        <input type="hidden" id="longitud_ramo_{{$pos_inv}}" value="{{$inv['longitud_ramo']}}">
                        <input type="hidden" id="unidad_medida_{{$pos_inv}}" value="{{$inv['unidad_medida']->id_unidad_medida}}">

                        {{$inv['variedad']->siglas}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{explode('|',$inv['peso']->nombre)[0] . '' . $inv['peso']->unidad_medida->siglas}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{explode('|',$inv['presentacion']->nombre)[0]}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{$inv['tallos_x_ramo']}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        @if($inv['longitud_ramo'] != '')
                            {{$inv['longitud_ramo'] . '' . $inv['unidad_medida']->siglas}}
                        @endif
                    </th>
                    @foreach($inv['dias'] as $pos_dia => $dia)
                        @php
                            $fecha = opDiasFecha('-',$pos_dia,date('Y-m-d'));
                            $fecha = getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime(substr($fecha,0,10))))].' '.
                                                convertDateToText(substr($fecha,0,10))
                        @endphp
                        <td class="text-center" style="border-color: #0a0a0a; background-color: #e9ecef; color: #0a0a0a;"
                            title="{{$fecha}}">
                            <div class="btn-group span_editar_{{$pos_dia}}" id="span_editar_{{$pos_inv}}_{{$pos_dia}}">
                                <span type="button" class="dropdown-toggle mouse-hand span_editar_{{$pos_dia}}"
                                      style="padding-bottom: 0; padding-top: 0; padding-left: 20px; padding-right: 20px"
                                      data-toggle="dropdown" aria-expanded="false">
                                    {{$dia['cantidad'] != '' ? $dia['cantidad'] : '-'}}
                                </span>
                                <ul class="dropdown-menu" style="width: 30px">
                                    @if($dia['cantidad'] != '')
                                        <li>
                                            <a href="javascript:void(0)" onclick="editar_dia('{{$pos_inv}}', '{{$pos_dia}}')" title="Editar">
                                                <i class="fa fa-fw fa-edit"></i> Editar
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="javascript:void(0)" onclick="add_dia('{{$pos_inv}}', '{{$pos_dia}}')" title="Ingresar">
                                            <i class="fa fa-fw fa-plus-circle"></i> Ingresar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <input type="number" onkeypress="return isNumber(event)" id="input_editar_{{$pos_inv}}_{{$pos_dia}}"
                                   max="{{$dia['cantidad']}}" min="0" value="{{$dia['cantidad']}}" style="width: 100%; display: none"
                                   maxlength="5" class="text-center">
                            <input type="number" onkeypress="return isNumber(event)" id="input_add_{{$pos_inv}}_{{$pos_dia}}"
                                   name="add_{{$pos_inv}}" min="0" value="" style="width: 100%; display: none" maxlength="5"
                                   class="text-center input_add_{{$pos_dia}}">
                            <input type="text" id="input_accion_{{$pos_inv}}_{{$pos_dia}}" style="display: none">
                        </td>
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{$inv['disponibles']}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{round(convertToEstandar($inv['disponibles'], explode('|', $inv['peso']->nombre)[0]) / getConfiguracionEmpresa()->ramos_x_caja, 2)}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-success" title="Aceptar" id="btn_save_{{$pos_inv}}"
                                    style="display: none" onclick="editar_inventario('{{$pos_inv}}')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                        </div>
                    </th>
                </tr>
                @php
                    $total_ramos += $inv['disponibles'];
                    $total_cajas += round(convertToEstandar($inv['disponibles'], explode('|', $inv['peso']->nombre)[0]) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                @endphp
            @endforeach
            </tbody>
            <tr id="tr_basura" style="display: none">
                <th class="text-center" colspan="5" style="border-color: #9d9d9d">
                    Basura
                </th>
                @for($i = 0; $i <= 9; $i++)
                    @php
                        $fecha = opDiasFecha('-',$i,date('Y-m-d'));
                        $fecha = getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime(substr($fecha,0,10))))].' '.
                                            convertDateToText(substr($fecha,0,10))
                    @endphp
                    <th class="text-center" style="border-color: #0a0a0a; background-color: #e9ecef" width="40px" title="{{$fecha}}">
                        <input type="number" class="text-center" onkeypress="return isNumber(event)" id="basura_dia_{{$i}}" style="width: 100%"
                               min="0">
                    </th>
                @endfor
                <th colspan="2" style="border-color: #9d9d9d"></th>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" colspan="5"
                    style="border-color: #9d9d9d">
                    Opciones
                </th>
                @for($i = 0; $i <= 9; $i++)
                    @php
                        $fecha = opDiasFecha('-',$i,date('Y-m-d'));
                        $fecha = getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime(substr($fecha,0,10))))].' '.
                                            convertDateToText(substr($fecha,0,10))
                    @endphp
                    <th class="text-center" style="border-color: #0a0a0a; background-color: #e9ecef" width="40px" title="{{$fecha}}">
                        <div class="dropup">
                            <button type="button" class="dropdown-toggle btn btn-xs"
                                    style="padding-bottom: 0; padding-top: 0; padding-left: 20px; padding-right: 20px" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" style="width: 30px">
                                <li id="btn_save_dia_{{$i}}" style="display: none;">
                                    <a href="javascript:void(0)" onclick="save_dia('{{$i}}')">
                                        <i class="fa fa-fw fa-save"></i> Guardar
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="delete_dia('{{$i}}')">
                                        <i class="fa fa-fw fa-trash"></i> Botar todo
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <input type="hidden" id="inventario_target_{{$i}}">
                    </th>
                @endfor
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">{{$total_ramos}}</th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">{{$total_cajas}}</th>
                <th style="border-color: #9d9d9d"></th>
            </tr>
        </table>
    </div>
@else
    <div class="alert alert-info text-center">
        El cuarto frío se encuentra vacío en estos momentos
    </div>
@endif