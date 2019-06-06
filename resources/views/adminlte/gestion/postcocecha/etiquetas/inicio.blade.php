@extends('layouts.adminlte.master')

@section('titulo')
    Etiquetas
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de etiquetas
                </h3>
            </div>
            <div class="box-body" id="div_content_etiquetas">
                <div class="col-md-8 col-md-offset-2">
                    <table width="100%">
                        <tr>
                            <td>
                                <label>Desde</label>
                                <input type="date" class="form-control" id="desde" name="desde" value="{{now()->toDateString()}}">
                            </td>
                            <td>
                                <label>Hasta</label>
                                <input type="date" class="form-control"  id="hasta" name="hasta" value="">
                            </td>
                            <td>
                                <label style="visibility: hidden">.</label><br/>
                                <button class="btn btn-default" onclick="listado_etiquetas()">
                                    <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                        id="title_btn_buscar">Buscar</em>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12" id="div_listado_etiquetas"></div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.postcocecha.etiquetas.script')
@endsection
