<div>
    <form name="form_config" id="form_config">
        @csrf
        <input type="hidden" id="id_config"
               value="{!! isset($config_empresa[0]->id_configuracion_empresa) ? $config_empresa[0]->id_configuracion_empresa : '' !!}">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Nombre comercial</span>
                    <input type="text" id="nombre_empresa" name="nombre_empresa" class="form-control" required maxlength="300" minlength="3"
                           autocomplete="off" placeholder="Nombre de la Empresa" pattern="[A-Za-z]+"
                           value="{!! isset($config_empresa[0]->nombre) ? $config_empresa[0]->nombre : '' !!}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Razón social</span>
                    <input type="text" id="razon_social" name="razon_social" class="form-control" required maxlength="300" minlength="3"
                           autocomplete="off" placeholder="Razón social"
                           value="{!! isset($config_empresa[0]->razon_social) ? $config_empresa[0]->razon_social : '' !!}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">País</span>
                    <select id="codigo_pais" name="codigo_pais" class="form-control">
                        @foreach($paises as $pais)
                            <option {{ ($config_empresa[0]->codigo_pais == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">N°. Usuarios</span>
                    <input type="text" id="cant_usuarios" name="cant_usuarios" class="form-control" required maxlength="3"
                           autocomplete="off" placeholder="Ej. 5" onkeypress="return isNumber(event)" minlength="1" pattern="^([0-9])*$"
                           value="{!! isset($config_empresa[0]->cantidad_usuarios) ? $config_empresa[0]->cantidad_usuarios : '' !!}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Hectareas</span>
                    <input type="text" id="hectarea" name="hectarea" class="form-control" required maxlength="20"
                           autocomplete="off" placeholder="Ej. 1.00" minlength="1" pattern="^-?\d+(?:.\d+)?$"
                           value="{!! isset($config_empresa[0]->cantidad_hectareas) ? $config_empresa[0]->cantidad_hectareas : '' !!}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef" id="icono_moneda">
                        {!! (isset($config_empresa[0]->moneda) && !empty($config_empresa[0]->moneda)) ? '<i class="fa fa-'.$config_empresa[0]->moneda.'"" aria-hidden="true"></i>' : '' !!}
                    </span>
                    <select class="form-control" id="moneda" onchange="icono_moneda()">
                        <option disabled selected>Seleccione</option>

                        @foreach($iconoMoneda as $moneda)
                            @php
                                $config_empresa[0]->moneda === $moneda->nombre ? $selected = 'selected=selected' : $selected = '';

                            @endphp
                            <option value="{{$moneda->nombre}}" {{$selected}}>
                                @if($moneda->nombre==='usd')  {{'Dolar'}} @endif
                                @if($moneda->nombre==='try')  {{'Lira turca'}} @endif
                                @if($moneda->nombre==='rub')  {{'Rublo ruso'}} @endif
                                @if($moneda->nombre==='krw')  {{'Won surcoreano'}} @endif
                                @if($moneda->nombre==='jpy')  {{'Yen japonés'}} @endif
                                @if($moneda->nombre==='inr')  {{'Rupia india'}} @endif
                                @if($moneda->nombre==='gbp')  {{'Libra esterlina'}} @endif
                                @if($moneda->nombre==='eur')  {{'Euros'}} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Matriz</span>
                    <input type="text" id="matriz" name="matriz" class="form-control" required maxlength="300"
                           autocomplete="off" minlength="3"
                           value="{!! isset($config_empresa[0]->direccion_matriz) ? $config_empresa[0]->direccion_matriz : '' !!}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Establecimiento</span>
                    <input type="text" id="establecimiento" name="establecimiento" class="form-control" required maxlength="300"
                           autocomplete="off" minlength="1"
                           value="{!! isset($config_empresa[0]->direccion_establecimiento) ? $config_empresa[0]->direccion_establecimiento : '' !!}">
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-4">
                <h4 class="box-title">
                    Procesos de propagación
                </h4>
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Nombre de procesos</th>
                    </tr>
                    </thead>
                    <tbody>
                    <td>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 1</span>
                            <input type="text" id="propagacion_proceso1" name="propagacion_proceso1" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_propagacion[0]) ? $arr_propagacion[0] : '' !!}">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 2</span>
                            <input type="text" id="propagacion_proceso2" name="propagacion_proceso2" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_propagacion[1]) ? $arr_propagacion[1] : '' !!}">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 3</span>
                            <input type="text" id="propagacion_proceso3" name="propagacion_proceso3" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_propagacion[2]) ? $arr_propagacion[2] : '' !!}">
                        </div>
                    </td>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h4 class="box-title">
                    Procesos de campo
                    <!--<small id="texto_seleccionar_rol">seleccione un rol</small>-->
                </h4>
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Nombre de procesos</th>
                    </tr>
                    </thead>
                    <tbody>
                    <td>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 1</span>
                            <input type="text" id="campo_proceso1" name="campo_proceso1" class="form-control" minlength="1" maxlength="255"
                                   value="{!! isset($arr_campo[0]) ? $arr_campo[0] : '' !!}">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 2</span>
                            <input type="text" id="campo_proceso2" name="campo_proceso2" class="form-control" minlength="1" maxlength="255"
                                   value="{!! isset($arr_campo[1]) ? $arr_campo[1] : '' !!}">
                        </div>
                    </td>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h4 class="box-title">
                    Procesos de postcosecha
                    <!--<small id="texto_seleccionar_rol">seleccione un rol</small>-->
                </h4>
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Nombre de procesos</th>
                    </tr>
                    </thead>
                    <tbody>
                    <td>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 1</span>
                            <input type="text" id="postcocecha_proceso1" name="postcoceha_proceso1" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_postcocecha[0]) ? $arr_postcocecha[0] : '' !!}">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 2</span>
                            <input type="text" id="postcocecha_proceso2" name="postcoceha_proceso2" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_postcocecha[1]) ? $arr_postcocecha[1] : '' !!}">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 3</span>
                            <input type="text" id="postcocecha_proceso3" name="postcoceha_proceso3" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_postcocecha[2]) ? $arr_postcocecha[2] : '' !!}">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 4</span>
                            <input type="text" id="postcocecha_proceso4" name="postcoceha_proceso4" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_postcocecha[3]) ? $arr_postcocecha[3] : '' !!}">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Proceso 5</span>
                            <input type="text" id="postcocecha_proceso5" name="postcoceha_proceso5" class="form-control" minlength="1"
                                   maxlength="255" value="{!! isset($arr_postcocecha[4]) ? $arr_postcocecha[4] : '' !!}">
                        </div>
                    </td>
                    </tbody>
                </table>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-3">
                <h4 class="box-title">
                    <a href="javascript:void(0)" onclick="admin_clasificacion_unitaria()">Clasificación unitaria</a>
                </h4>
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Datos</th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            <button type="button" class="btn btn-xs btn-default" title="Añadir campo"
                                    onclick="add_inptus('campos_clasifc_unitaria','clasificacion_unitaria')">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                    <tbody id="campos_clasifc_unitaria">
                    @if(isset($clasifiUnit) && !empty($clasifiUnit))
                        @foreach($clasifiUnit as $key =>$clUnit)
                            <tr id="campos_clasifc_unitaria_{{$key+1}}">
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Cantidad</span>
                                        <input type="text" placeholder="30|7" id="clasificacion_unitaria_{{$key +1}}"
                                               name="clasificacion_unitaria_{{$key +1}}" required="" class="form-control" minlength="1"
                                               maxlength="10"
                                               {!! $clUnit->estado == 0 ? 'disabled' : '' !!} value="{{$clUnit->nombre}}">
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" id="id_clasificacion_unitaria_{{$key+1}}"
                                           value="{!! isset($clUnit->id_clasificacion_unitaria) ? $clUnit->id_clasificacion_unitaria : '' !!}">
                                    <button type="button" class="btn btn-xs btn-{!! $clUnit->estado == 1 ? 'warning' : 'success' !!}"
                                            title="{!! $clUnit->estado == 1 ? 'Desactivar' : 'Activar' !!} campo"
                                            onclick="actualizarClasificacion('{{$clUnit->id_clasificacion_unitaria}}','{{$clUnit->estado}}','clasificacion_unitaria')">
                                        <i class="fa fa-{!! $clUnit->estado == 1 ? 'ban' : 'check' !!} " aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Cantidad</span>
                                    <input type="text" placeholder="30|7" id="clasificacion_unitaria_1" name="clasificacion_unitaria_1"
                                           required="" class="form-control" minlength="1" maxlength="10" pattern="^([0-9])*$">
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                    </thead>
                </table>
            </div>
            <div class="col-md-3">
                <h4 class="box-title">
                    <a href="javascript:void(0)" onclick="admin_clasificacion_ramo()">
                        Clasificación por ramo
                    </a>
                </h4>
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Datos</th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            <button type="button" class="btn btn-xs btn-default" title="Añadir campo"
                                    onclick="add_inptus('campos_clasifc_x_ramo','clasificacion_por_ramo')">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                    <tbody id="campos_clasifc_x_ramo">
                    @if(isset($clasifiXRamo) && !empty($clasifiXRamo))
                        @foreach($clasifiXRamo as $key =>$clXRamo)
                            <tr id="campos_clasifc_x_ramo_{{$key+1}}">
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Cantidad</span>
                                        <input type="text" id="clasificacion_por_ramo_{{$key+1}}" placeholder="Sólo números"
                                               name="clasificacion_por_ramo_{{$key+1}}" required="" class="form-control" minlength="1"
                                               maxlength="10" pattern="^([0-9])*$"
                                               {!! $clXRamo->estado == 0 ? 'disabled' : '' !!} value="{{$clXRamo->nombre}}">
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" id="id_clasificacion_por_ramo_{{$key+1}}"
                                           value="{!! isset($clXRamo->id_clasificacion_ramo) ? $clXRamo->id_clasificacion_ramo : '' !!}">
                                    <button type="button" class="btn btn-xs btn-{!! $clXRamo->estado == 1 ? 'warning' : 'success' !!}"
                                            title="{!! $clXRamo->estado == 1 ? 'Desactivar' : 'Activar' !!} campo"
                                            onclick="actualizarClasificacion('{{$clXRamo->id_clasificacion_ramo}}','{{$clXRamo->estado}}','clasificacion_x_ramo')">
                                        <i class="fa fa-{!! $clXRamo->estado == 1 ? 'ban' : 'check' !!} " aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="background-color: #e9ecef">Cantidad</span>
                                <input type="text" id="clasificacion_por_ramo_1" placeholder="Sólo números" name="clasificacion_por_ramo_1"
                                       required="" class="form-control" minlength="1" maxlength="10" pattern="^([0-9])*$">
                            </div>
                        </td>
                    @endif

                    </tbody>
                    </thead>
                </table>
            </div>
            <div class="col-md-6">
                <h4 class="box-title">
                    Nombre de empaques
                </h4>
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Datos</th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            <button type="button" class="btn btn-xs btn-default" title="Añadir campo"
                                    onclick="add_inptus('empaques','campo_empaque')" id="add_empaques">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                    <tbody id="empaques">
                    @if(isset($empaques) && !empty($empaques))
                        @foreach($empaques as $key =>$empaque)
                            <tr id="empaques_{{$key+1}}">
                                <td>
                                    <div class="form-inline">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background-color: #e9ecef">Nombre</span>
                                            <input type="text" id="campo_empaque_{{$key+1}}" name="campo_empaque_{{$key+1}}" required=""
                                                   class="form-control" minlength="1" maxlength="255" placeholder="nombre|factor"
                                                   {!! $empaque->estado == 0 ? 'disabled' : '' !!} value="{{$empaque->nombre}}">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background-color: #e9ecef">Tipo</span>
                                            <select class="form-control"
                                                    {!! $empaque->estado == 0 ? 'disabled' : '' !!} id="tipo_empaque_{{$key+1}}">
                                                <option selected disabled="">Seleccione</option>
                                                <option value="C" {!! $empaque->tipo == 'C' ? 'selected' :'' !!} >Caja</option>
                                                {{--<option value="E" {!! $empaque->tipo == 'E' ? 'selected' :'' !!} >Envoltura</option>--}}
                                                <option value="P" {!! $empaque->tipo == 'P' ? 'selected' :'' !!} >Presentación</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <input type="hidden" id="id_campo_empaque_{{$key+1}}" placeholder="nombre|factor"
                                           value="{!! isset($empaque->id_empaque) ? $empaque->id_empaque : '' !!}">
                                    <button type="button" class="btn btn-xs btn-{!! $empaque->estado == 1 ? 'warning' : 'success' !!}"
                                            title="{!! $empaque->estado == 1 ? 'Desactivar' : 'Activar' !!} campo"
                                            onclick="actualizarClasificacion('{{$empaque->id_empaque}}','{{$empaque->estado}}','empaque')"><i
                                                class="fa fa-{!! $empaque->estado == 1 ? 'ban' : 'check' !!} " aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-xs btn-success {!! $empaque->estado != 1 ? 'hide' : '' !!}"
                                            id="detalle_empaque_{{$key+1}}"
                                            onclick="modal_detalle_empaque('{{$empaque->id_empaque}}','{{$empaque->nombre}}')"
                                            title="Detalles del empaque"><i class="fa fa-info-circle " aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <td>
                            <div class="form-inline">
                                <div class="input-group">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Nombre</span>
                                    <input type="text" id="campo_empaque_1" name="campo_empaque_1" required="" placeholder="nombre|factor"
                                           class="form-control" minlength="1" maxlength="255">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Tipo</span>
                                    <select class="form-control" id="tipo_empaque_1">
                                        <option selected disabled="">Seleccione</option>
                                        <option value="C">Caja</option>
                                        {{--<option value="E">Envoltura</option>--}}
                                        <option value="P">Presentación</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    @endif
                    </tbody>
                    </thead>
                </table>
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-success" type="button" id="btn_guardar_datos_config" onclick="store_data_config()">
                <span class="bootstrap-dialog-button-icon fa fa-fw fa-save"></span>
                Guardar
            </button>
        </div>
    </form>
</div>
