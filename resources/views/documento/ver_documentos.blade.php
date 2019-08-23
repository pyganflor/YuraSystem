@if(count($documentos)>0)
    @foreach($documentos as $item)
        <form id="form-update_documento_{{$item->id_documento}}">
            <table width="100%" class="table table-responsive table-bordered"
                   style="border-color: #9d9d9d; margin: 0px" id="table_content_detalles">
                <tr>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d" width="20%">
                        <input type="text" class="form-control" id="nombre_campo_{{$item->id_documento}}"
                               name="nombre_campo_{{$item->id_documento}}"
                               required maxlength="250" placeholder="Escriba el nombre del campo" value="{{$item->nombre_campo}}"
                               style="background-color: #fffefe99">
                    </th>
                    <td style="border-color: #9d9d9d" class="text-center" width="20%">
                        @if($item->tipo_dato == 'int')
                            <input type="number" class="form-control text-center" id="valor_{{$item->id_documento}}"
                                   name="valor_{{$item->id_documento}}"
                                   max="99999999999" maxlength="11" required onkeypress="return isNumber(event)" value="{{$item->int}}">
                        @elseif($item->tipo_dato == 'float')
                            <input type="text" class="form-control text-center" id="valor_{{$item->id_documento}}"
                                   name="valor_{{$item->id_documento}}"
                                   max="99999999999" maxlength="11" pattern="^[0-9]+(\.[0-9]+)?$" required value="{{$item->float}}">
                        @elseif($item->tipo_dato == 'char')
                            <input type="text" class="form-control text-center" id="valor_{{$item->id_documento}}"
                                   name="valor_{{$item->id_documento}}"
                                   maxlength="1"
                                   required value="{{$item->char}}">
                        @elseif($item->tipo_dato == 'varchar')
                            <input type="text" class="form-control text-center" id="valor_{{$item->id_documento}}"
                                   name="valor_{{$item->id_documento}}"
                                   maxlength="1000" required value="{{$item->varchar}}">
                        @elseif($item->tipo_dato == 'boolean')
                            <select class="form-control text-center" id="valor_{{$item->id_documento}}" name="valor_{{$item->id_documento}}"
                                    required>
                                <option value="1" {{$item->boolean == 1 ? 'selected' : ''}}>1</option>
                                <option value="0" {{$item->boolean == 0 ? 'selected' : ''}}>0</option>
                            </select>
                        @elseif($item->tipo_dato == 'date')
                            <input type="date" class="form-control text-center" id="valor_{{$item->id_documento}}"
                                   name="valor_{{$item->id_documento}}"
                                   required
                                   value="{{$item->date}}">
                        @elseif($item->tipo_dato == 'datetime')
                            <input type="datetime-local" class="form-control text-center" id="valor_{{$item->id_documento}}"
                                   name="valor_{{$item->id_documento}}" required value="{{$item->datetime}}">
                        @endif
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                    <textarea name="descripcion_{{$item->id_documento}}" id="descripcion_{{$item->id_documento}}" rows="2"
                              maxlength="4000" class="form-control" placeholder="Escriba alguna descripción sobre la información adicional">
                        {{$item->descripcion}}
                    </textarea>
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center" width="10%">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-success"
                                    onclick="update_documento('{{$item->id_documento}}', '{{$item->codigo}}', '{{$item->entidad}}')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger"
                                    onclick="delete_documento('{{$item->id_documento}}', '{{$item->codigo}}', '{{$item->entidad}}')">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    @endforeach
@else
    <div class="alert alert-info text-center">
        No existen registros para este elemento
    </div>
@endif
