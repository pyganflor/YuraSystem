@extends('layouts.adminlte.master')

@section('titulo')
    DB - Jobs
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <section class="content-header">
        <h1>
            DB
            <small>Jobs</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li>
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li>
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('{{$submenu->url}}')">
                    <i class="fa fa-fw fa-refresh"></i> {!! $submenu->nombre !!}
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-body" id="div_listado">
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
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tabla as $item)
                            <tr>
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
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    <script>
        estructura_tabla('db_tbl_jobs', false, false)
    </script>
@endsection