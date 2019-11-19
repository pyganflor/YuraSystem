<div style="overflow-x: scroll">
    <table class="table-striped table-bordered table-hover" width="100%" style="border: 2px solid #9d9d9d" id="db_tbl_jobs">
        <thead>
        <tr style="background-color: #e9ecef">
            <th class="text-center" style="border-color: #9d9d9d">
                id
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                queue
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                payload
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                attempts
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                reserved_at
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                available_at
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                created_at
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                tiempo
            </th>
            <th class="text-center" style="border-color: #9d9d9d">

            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($tabla as $item)
            <tr style="background-color: {{$item->queue == 'job' ? '#cbffc1' : ''}}">
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$item->id}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$item->queue}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d" title="{{$item->payload}}">
                    {{str_limit($item->payload, 50)}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$item->attempts}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$item->reserved_at}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$item->available_at}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$item->created_at}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{difFechas(getCurrentDateDB(), $item->fecha_registro)->h}}:{{difFechas(getCurrentDateDB(), $item->fecha_registro)->i}}:{{difFechas(getCurrentDateDB(), $item->fecha_registro)->s}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <button type="button" class="btn btn-xs btn-danger" title="Eliminar" onclick="delete_job('{{$item->id}}')">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </th>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    estructura_tabla('db_tbl_jobs', false, false);

    function delete_job(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id: id
        };
        post_jquery('{{url('db_jobs/delete_job')}}', datos, function (retorno) {
            $.get('{{url('db_jobs/actualizar')}}', {}, function (retorno) {
                $('#div_listado').html(retorno);
            });
        });
    }
</script>