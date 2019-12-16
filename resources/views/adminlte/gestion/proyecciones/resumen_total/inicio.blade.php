@extends('layouts.adminlte.master')
@section('titulo')
    Resumen total de proyecciones
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <table class="table-bordered table-striped table-hover" width="100%" style="border: 2px solid #9d9d9d; font-size: 1em;">
                    <thead>
                        <tr style="background-color: #e9ecef">
                            <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                                <b >Semanas</b>
                            </th>
                            {{--data semana--}}
                            <th class="text-center" style="border-color: #9d9d9d; width: 250px">

                            </th>
                            {{--data semana--}}
                            <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                                <b >Semanas</b>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>

                            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                               <b>Cosechado</b>
                            </th>
                            {{--data cosechado--}}
                            <td class="text-center celda_hovered" id="">

                            </td>
                            {{--data cosechado--}}
                            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                <b>Proyeecion Cosecha</b>
                            </th>
                        <tr>

                            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                <b>Proyeeción venta</b>
                            </th>
                            {{--data vendido--}}
                            <td class="text-center celda_hovered" id="" >

                            </td>
                            {{--data vendido--}}
                            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                                <b>Proyeeción venta</b>
                            </th>
                        </tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.proyecciones.resumen_total.script')
@endsection
