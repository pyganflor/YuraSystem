<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Cama;
use yura\Modelos\CosechaPlantasMadres;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;

class propagCosechaPtasMadresController extends Controller
{
    public function inicio(Request $request)
    {
        $camas = DB::table('ciclo_cama as cc')
            ->join('cama as c', 'c.id_cama', 'cc.id_cama')
            ->join('variedad as v', 'v.id_variedad', 'cc.id_variedad')
            ->select('cc.id_cama', 'c.nombre')->distinct()
            ->where('cc.activo', 1)
            ->orderBy('c.nombre')
            ->get();
        return view('adminlte.gestion.propagacion.cosecha_plantas_madres.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'camas' => $camas,
        ]);
    }

    public function listar_cosechas(Request $request)
    {
        $cosechas = CosechaPlantasMadres::where('fecha', $request->fecha)->orderBy('cantidad')->get();
        $camas_activas = DB::table('ciclo_cama as cc')
            ->join('cama as c', 'c.id_cama', 'cc.id_cama')
            ->join('variedad as v', 'v.id_variedad', 'cc.id_variedad')
            ->select('cc.id_cama', 'c.nombre', 'cc.id_variedad', 'v.siglas')->distinct()
            ->where('cc.activo', 1)
            ->orderBy('c.nombre')
            ->get();
        return view('adminlte.gestion.propagacion.cosecha_plantas_madres.partials.listado_cosechas', [
            'cosechas' => $cosechas,
            'camas' => $camas_activas,
        ]);
    }

    public function select_cama(Request $request)
    {
        $cama = Cama::find($request->cama);
        $ciclo_actual = $cama->ciclo_actual();
        return [
            'variedad' => $ciclo_actual->variedad
        ];
    }

    public function store_cosechas(Request $request)
    {
        $success = true;
        $msg = '<div class="alert alert-success text-center">Se ha ingresado la cosecha satisfactoriamente</div>';
        if ($request->fecha != '') {
            if (count($request->cantidades) > 0) {
                foreach ($request->cantidades as $item) {
                    $cosecha = new CosechaPlantasMadres();
                    $cosecha->fecha = $request->fecha;
                    $cosecha->id_cama = $item['cama'];
                    $cosecha->id_variedad = $item['variedad'];
                    $cosecha->cantidad = $item['cantidad'];
                    $cosecha->fecha_registro = date('Y-m-d H:i:s');
                    if ($cosecha->save()) {
                        $cosecha = CosechaPlantasMadres::All()->last();
                        bitacora('cosecha_plantas_madres', $cosecha->id_cosecha_plantas_madres, 'I', 'Insercion de una cosecha_plantas_madres');
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-danger text-center">Ha ocurrido un problema al guardar la cosecha</div>';
                    }
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">Debe ingresar las cantidades</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">Debe indicar la fecha</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function update_cosecha(Request $request)
    {
        $cosecha = CosechaPlantasMadres::find($request->cosecha);
        if ($cosecha != '') {
            $cosecha->id_cama = $request->cama;
            $cosecha->id_variedad = $request->variedad;
            $cosecha->cantidad = $request->cantidad;
            if ($cosecha->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">Se ha actualizado la cosecha satisfactoriamente</div>';
                bitacora('cosecha_plantas_madres', $cosecha->id_cosecha_plantas_madres, 'U', 'Update de una cosecha_plantas_madres');
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">Ha ocurrido un problema al guardar la cosecha</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">No se ha encontrado la cosecha</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function eliminar_cosecha(Request $request)
    {
        $cosecha = CosechaPlantasMadres::find($request->cosecha);
        if ($cosecha != '') {
            if ($cosecha->delete()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">Se ha eliminado la cosecha satisfactoriamente</div>';
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">Ha ocurrido un problema al eliminar la cosecha</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">No se ha encontrado la cosecha</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }
}
