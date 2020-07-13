<select id="edit_insumo_{{$form}}" style="width: 100%" onchange="select_insumo('{{$form}}')">
    <option value="">Seleccione...</option>
    @foreach($act_insumos as $act_ins)
        <option value="{{$act_ins->id_producto}}">{{$act_ins->producto->nombre}}</option>
    @endforeach
</select>