<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;

class tblPostcosechaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.crm.tbl_postcosecha.inicio', [

        ]);
    }
}