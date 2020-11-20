<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\DetalleEnraizamientoSemanal;
use yura\Modelos\EnraizamientoSemanal;
use yura\Modelos\Submenu;

class EnraizamientoController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.propagacion.enraizamiento.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function store_enraizamiento(Request $request)
    {
        $msg = '<div class="alert alert-success text-center">Se ha guardado la información satisfactoriamente</div>';
        $success = true;
        $semana_ini = getSemanaByDate($request->fecha);
        foreach ($request->data as $d) {
            $enr_sem = EnraizamientoSemanal::All()
                ->where('semana_ini', $semana_ini->codigo)
                ->where('id_variedad', $d['variedad'])
                ->first();
            if ($enr_sem == '') {
                $new_enr = true;
                $enr_sem = new EnraizamientoSemanal();
                $enr_sem->semana_ini = $semana_ini->codigo;
                $enr_sem->id_variedad = $d['variedad'];
                $enr_sem->cantidad_siembra = $d['cantidad'];
            } else {
                $new_enr = false;
                $enr_sem->cantidad_siembra += $d['cantidad'];
            }
            $enr_sem->cantidad_semanas = $d['semanas'];
            $semana_fin = getSemanaByDate(opDiasFecha('+', ($d['semanas'] * 7), $semana_ini->fecha_inicial));
            $enr_sem->semana_fin = $semana_fin->codigo;
            if ($enr_sem->save()) {
                if ($new_enr)
                    $enr_sem = EnraizamientoSemanal::All()->last();

                /* =============== DetalleEnraizamientoSemanal ================== */
                $det_enr = new DetalleEnraizamientoSemanal();
                $det_enr->id_enraizamiento_semanal = $enr_sem->id_enraizamiento_semanal;
                $det_enr->fecha = $request->fecha;
                $det_enr->cantidad_siembra = $d['cantidad'];
                $det_enr->save();
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">Ha ocurrido un problema al guardar el enraizamiento</div>';
            }
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function buscar_enraizamiento_semanal(Request $request)
    {
        $semana_ini = getSemanaByDate($request->fecha);
        $model = EnraizamientoSemanal::All()
            ->where('semana_ini', $semana_ini->codigo)
            ->where('id_variedad', $request->variedad)
            ->first();
        if ($model != '')
            return [
                'cantidad_semanas' => $model->cantidad_semanas
            ];
        else
            return [
                'cantidad_semanas' => ''
            ];
    }
}
