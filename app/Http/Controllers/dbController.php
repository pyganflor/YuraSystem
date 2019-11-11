<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Submenu;

class dbController extends Controller
{
    public function jobs(Request $request)
    {
        return view('adminlte.gestion.db.jobs', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'tabla' => DB::table('jobs')->get()
        ]);
    }
}
