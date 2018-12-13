<input type="hidden" value="{{$id_empaque}}" id="id_empque">
<input type="hidden" value="{{$nombre}}" id="nombre_empque">

<form id="form_add_detalle_empaque" name="form_add_detalle_empaque">
    <div class="col-md-12">
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
            <thead>
            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                <th class="text-center" style="border-color: #9d9d9d">Variedad</th>
                <th class="text-center" style="border-color: #9d9d9d">Calificación por ramos</th>
                <th class="text-center" style="border-color: #9d9d9d">Cantidad</th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <button type="button" id="add_campo_detalle" class="btn btn-xs btn-default" title="Añadir campo" onclick="add_inptus('detalles_empaques',['empaque_id_variedad','empaque_id_clasificacion_por_ramo','cantidad_empaque'])">
                        <i class="fa fa-fw fa-plus"></i>
                    </button>
                </th>
            </tr>
            <tbody id="detalles_empaques">
            <!--@if(count($dataDetalleEmpaque) > 0)-->
            @foreach($dataDetalleEmpaque as $key => $detEmpaque)
                <tr id="tr_detalles_empaque_{{$key+1}}">
                    <td>
                        <select id="empaque_id_variedad_{{$key+1}}" name="variedad_empaque" {!! $detEmpaque->estado == 0 ? 'disabled' : '' !!} class="form-control" required>
                            <option selected disabled>Seleccione</option>
                            @foreach($variedades as $variedad)
                                {!! $selected = "" !!}
                                @if($variedad->estado == 1)
                                    @if(isset($detEmpaque))
                                        @if($detEmpaque->id_variedad == $variedad->id_variedad)
                                            {!! $selected = "selected=selected" !!}
                                        @endif
                                    @endif
                                    <option value="{{$variedad->id_variedad}}" {{ $selected }}> {{$variedad->nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select id="empaque_id_clasificacion_por_ramo_{{$key+1}}" {!! $detEmpaque->estado == 0 ? 'disabled' : '' !!} name="empaque_name_clasificacion_por_ramo" class="form-control" required="">
                            <option selected disabled="">Seleccione</option>
                            @foreach($clasificacionesRamos as $clasificacionesRamo)
                                {!! $selected = "" !!}
                                @if($clasificacionesRamo->estado == 1)
                                    @if(isset($detEmpaque))
                                        @if($detEmpaque->id_clasificacion_ramo == $clasificacionesRamo->id_clasificacion_ramo)
                                            {!! $selected = "selected=selected" !!}
                                        @endif
                                    @endif
                                    <option value="{{$clasificacionesRamo->id_clasificacion_ramo}}" {{$selected}}>{{$clasificacionesRamo->nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" id="cantidad_empaque_{{$key+1}}" onkeypress="return isNumber(event)" name="cantidad_empaque" {!! $detEmpaque->estado == 0 ? 'disabled' : '' !!} class="form-control" required maxlength="25" autocomplete="off" value="{{ isset($detEmpaque) ? $detEmpaque->cantidad : '' }}" pattern="^([0-9])*$" aria-required minlength="1">
                    </td>
                    <td class="text-center">
                        <input type="hidden" id="id_detalle_empaque_{{$key+1}}" value="{!! isset($detEmpaque->id_detalle_empaque) ? $detEmpaque->id_detalle_empaque : '' !!}"><input type="hidden" value="{{$detEmpaque->id_detalle_empaque}}" id="">
                        <button type="button" class="btn btn-xs btn-{!! $detEmpaque->estado == 1 ? 'warning' : 'success' !!}" title="{!! $detEmpaque->estado == 1 ? 'Desactivar' : 'Activar' !!} campo" onclick="actualizarEstadoDetallePaquete('{{$detEmpaque->id_detalle_empaque}}','{{$detEmpaque->estado}}')"> <i class="fa fa-{!! $detEmpaque->estado == 1 ? 'ban' : 'check' !!} " aria-hidden="true"></i></button>
                    </td>
                </tr>
            @endforeach
            <!--@else
                <div id="message_detalles_empaque">No existen detalles registrados para este empaque</div>
            @endif-->
            </tbody>
            </thead>
        </table>
    </div>
</form>

