<select id="edit_mo_{{$form}}" style="width: 100%" onchange="select_mo('{{$form}}')">
    <option value="">Seleccione...</option>
    @foreach($act_mo as $act_ins)
        <option value="{{$act_ins->id_mano_obra}}">{{$act_ins->mano_obra->nombre}}</option>
    @endforeach
</select>