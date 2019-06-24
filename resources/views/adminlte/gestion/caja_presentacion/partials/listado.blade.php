 <div class="col-md-6">
        <div id="table_empaque_c">
            @if(sizeof($empaques)>0)
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
                       id="table_content_empaque_c">
                    <thead>
                    <tr style="background-color: #dd4b39; color: white">
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                            NOMBRE CAJAS | FACTOR DE CONVERCIÓN | PESO CAJA (gr)
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            OPCIONES
                        </th>
                    </tr>
                    </thead>
                    @foreach($empaques as $item)
                        @if($item->tipo == "C")
                            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                                <td style="border-color: #9d9d9d" class="text-center">
                                    {{$item->nombre}}
                                </td>
                                <td style="border-color: #9d9d9d" class="text-center">
                                    <button type="button" {{$item->estado == 1 ? '' : 'disabled'}} class="btn btn-default btn-xs" title="Detalle empaque" onclick="detalle_empaque('{{$item->id_empaque}}')">
                                        <i class="fa fa-list-ol" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" {{$item->estado == 1 ? '' : 'disabled'}} class="btn btn-default btn-xs" title="editar" onclick="add_empaque('{{$item->id_empaque}}')">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{$item->estado == 1 ? 'warning' : 'success'}} btn-xs" title="{{$item->estado == 1 ? 'Deshabilitar' : 'Habilitar'}}" onclick="update_detalle_empaque('{{$item->id_empaque}}','{{$item->estado}}')">
                                        <i class="fa fa-{{$item->estado == 1 ? 'ban' : 'check'}}" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
                <div id="pagination_listado_empaques_c">
                   {{-- {!! str_replace('/?','?',$listado->render()) !!}--}}
                </div>
            @else
                <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
            @endif
        </div>
 </div>
 <div class="col-md-6">
     <div id="table_empaque_p">
         @if(sizeof($empaques)>0)
             <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
                    id="table_content_empaques_p">
                 <thead>
                 <tr style="background-color: #dd4b39; color: white">
                     <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                         NOMBRE PRESENTACIÓN
                     </th>
                     <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                         OPCIONES
                     </th>
                 </tr>
                 </thead>
                 @foreach($empaques as $item)
                     @if($item->tipo == "P")
                         <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                             <td style="border-color: #9d9d9d" class="text-center">
                                 {{$item->nombre}}
                             </td>
                             <td style="border-color: #9d9d9d" class="text-center">
                                 {{--<button type="button" class="btn btn-default btn-xs" title="Detalle empaque" onclick="detalle_empaque('{{$item->id_empaque}}')">
                                     <i class="fa fa-list-ol" aria-hidden="true"></i>
                                 </button>--}}
                                 <button type="button" {{$item->estado == 1 ? '' : 'disabled'}} class="btn btn-default btn-xs" title="editar" onclick="add_empaque('{{$item->id_empaque}}')">
                                     <i class="fa fa-pencil" aria-hidden="true"></i>
                                 </button>
                                 <button type="button" class="btn btn-{{$item->estado == 1 ? 'warning' : 'success'}} btn-xs" title="{{$item->estado == 1 ? 'Deshabilitar' : 'Habilitar'}}" onclick="update_detalle_empaque('{{$item->id_empaque}}','{{$item->estado}}')">
                                     <i class="fa fa-{{$item->estado == 1 ? 'ban' : 'check'}}" aria-hidden="true"></i>
                                 </button>
                             </td>
                         </tr>
                     @endif
                 @endforeach
             </table>
             <div id="pagination_listado_empaques_p">
                {{-- {!! str_replace('/?','?',$listado->render()) !!}--}}
             </div>
         @else
             <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
         @endif
     </div>
 </div>



