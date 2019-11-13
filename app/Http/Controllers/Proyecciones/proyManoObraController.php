<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class proyManoObraController extends Controller
{
    public function inicio(Request $request)
    {
        $hasta = getSemanaByDate(opDiasFecha('+', 98, date('Y-m-d')));
        return view('adminlte.gestion.proyecciones.mano_obra.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'semana_hasta' => $hasta
        ]);
    }

    public function listar_proyecciones(Request $request)
    {
        $semana_desde = Semana::All()
            ->where('estado', 1)
            ->where('codigo', $request->desde)
            ->first();
        $semana_hasta = Semana::All()
            ->where('estado', 1)
            ->where('codigo', $request->hasta)
            ->first();

        if ($semana_desde != '' && $semana_hasta != '') {
            $sem_last_4 = getSemanaByDate(opDiasFecha('-', 28, date('Y-m-d')));
            $sem_last_1 = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));

            $query_tallos = DB::table('resumen_semana_cosecha as r')
                ->select(DB::raw('sum(tallos_proyectados) as cant'), 'codigo_semana as semana')
                ->where('estado', '=', 1)
                ->where('codigo_semana', '>=', $semana_desde->codigo)
                ->where('codigo_semana', '<=', $semana_hasta->codigo)
                ->groupBy('codigo_semana')
                ->get();

            if ($request->area == 'C') {    // cosecha
                $rend_cosecha = getRendimientoCosechaByRangoVariedad($sem_last_4->fecha_inicial, $sem_last_1->fecha_final, 'T');
                $pers_cosecha = getPersonalCosechaByRango($sem_last_4->fecha_inicial, $sem_last_1->fecha_final);
                $hr_diarias_cosecha = getConfiguracionEmpresa()->horas_diarias_cosecha;

                return view('adminlte.gestion.proyecciones.mano_obra.partials.listado_cosecha', [
                    'rend_cosecha' => $rend_cosecha,
                    'list_tallos' => $query_tallos,
                    'pers_cosecha' => $pers_cosecha,
                    'hr_diarias_cosecha' => $hr_diarias_cosecha,
                ]);
            } else if ($request->area == 'V') { // clasificacion verde
                $rend_verde = getRendimientoVerdeByRangoVariedad($sem_last_4->fecha_inicial, $sem_last_1->fecha_final, 'T');
                $pers_verde = getPersonalVerdeByRango($sem_last_4->fecha_inicial, $sem_last_1->fecha_final);
                $hr_diarias_verde = getConfiguracionEmpresa()->horas_diarias_verde;

                return view('adminlte.gestion.proyecciones.mano_obra.partials.listado_verde', [
                    'rend_verde' => $rend_verde,
                    'list_tallos' => $query_tallos,
                    'pers_verde' => $pers_verde,
                    'hr_diarias_verde' => $hr_diarias_verde,
                ]);
            }
        } else {
            return '<div class="alert alert-warning text-center">Las semanas están incorrectas</div>';
        }
    }

    public function update_horas_diarias_cosecha(Request $request)
    {
        $model = getConfiguracionEmpresa();
        $model->horas_diarias_cosecha = $request->valor;
        $model->save();

        bitacora('configuracion_empresa', $model->id_configuracion_empresa, 'U', 'Modificacion de las horas diarias de cosecha');
        return [
            'success' => true
        ];
    }

    public function update_horas_diarias_verde(Request $request)
    {
        $model = getConfiguracionEmpresa();
        $model->horas_diarias_verde = $request->valor;
        $model->save();

        bitacora('configuracion_empresa', $model->id_configuracion_empresa, 'U', 'Modificacion de las horas diarias de verde');
        return [
            'success' => true
        ];
    }
}