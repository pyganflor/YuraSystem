<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Jobs\ProyeccionVentaSemanalUpdate;
use yura\Jobs\ResumenSemanaCosecha;
use yura\Modelos\Job;
use yura\Modelos\Submenu;

class dbController extends Controller
{
    public function jobs(Request $request)
    {
        return view('adminlte.gestion.db.jobs', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'variedades' => getVariedades(),
            'modulos' => getModulos()->where('estado', 1),
            'clientes' => getClientes(),
            'semana_actual' => getSemanaByDate(date('Y-m-d')),
        ]);
    }

    public function actualizar_jobs(Request $request)
    {
        return view('adminlte.gestion.db.partials._jobs', [
            'tabla' => DB::table('jobs')->get()
        ]);
    }

    public function delete_job(Request $request)
    {
        $model = Job::find($request->id);
        $model->delete();

        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha eliminado el Job satisfactoriamente</div>',
        ];
    }

    public function send_queue_job(Request $request)
    {
        if ($request->comando == 1) {   // comando ProyeccionUpdateSemanal
            $restriccion = $request->restriccion == 'true' ? 1 : 0;
            ProyeccionUpdateSemanal::dispatch($request->desde, $request->hasta, $request->variedad, $request->modulo, $restriccion)
                ->onQueue('job');
        }
        if ($request->comando == 2) {   // comando ResumenSemanaCosecha
            ResumenSemanaCosecha::dispatch($request->desde, $request->hasta, $request->variedad)
                ->onQueue('job');
        }
        if ($request->comando == 3) {   // comando VentaSemanalReal
            ProyeccionVentaSemanalUpdate::dispatch($request->desde, $request->hasta, $request->cliente, $request->variedad)
                ->onQueue('job');
        }

        return ['success' => true];
    }
}
