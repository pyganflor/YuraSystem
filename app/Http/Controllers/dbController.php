<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Job;
use yura\Modelos\Submenu;

class dbController extends Controller
{
    public function jobs(Request $request)
    {
        return view('adminlte.gestion.db.jobs', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
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
}
