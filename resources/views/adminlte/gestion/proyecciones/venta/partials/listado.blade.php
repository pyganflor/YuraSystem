@dump($proyeccionVentaSemanalReal)
<div style="overflow-x: scroll">
    <table class="table-striped table-bordered table-hover" style="border: 2px solid #9d9d9d" width="100%">
        <thead>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <span class="fa fa-caret-down"></span></button>
                    {{--<ul class="dropdown-menu">
                        <li>
                            <a href="javascript:void(0)" class="hide">
                                Crear masiva
                            </a>
                        </li>
                    </ul>--}}
                </div>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 250px">
                Clientes
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($proyeccionVentaSemanalReal as $proyeccion)
            @dd($proyeccion)
            <tr id="">
                <td class="text-center" style="border-color: #9d9d9d">
                </td>
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" id="">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">

                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" >
                                    Actualizar
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" >
                                    Actualizar manualmente
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="javascript:void(0)" >
                                    Restaurar Proyección
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">

                </td>
            </tr>
        @endforeach
        </tbody>

        <tr style="background-color: #fdff8b">
            <td class="text-center" style="border-color: #9d9d9d">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                Proyectados

            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                Proyectados
            </td>
        </tr>
        <tr style="background-color: #c4c4ff">
            <td class="text-center" style="border-color: #9d9d9d">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                Cosechados
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                Cosechados

            </td>
        </tr>

    </table>
</div>
