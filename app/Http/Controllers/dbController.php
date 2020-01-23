<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Jobs\ProyeccionVentaSemanalUpdate;
use yura\Jobs\ResumenAreaSemanal;
use yura\Jobs\ResumenCostosSemanal;
use yura\Jobs\ResumenSemanaCosecha;
use yura\Jobs\UpdateIndicador;
use yura\Jobs\UpdateOtrosGastos;
use yura\Jobs\UpdateRegalias;
use yura\Jobs\UpdateTallosCosechadosProyeccion;
use yura\Modelos\Color;
use yura\Modelos\Indicador;
use yura\Modelos\IntervaloIndicador;
use yura\Modelos\Job;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Modelos\Submenu;
use Validator;

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
            'indicadores' => getIndicadores()->where('estado', 1),
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
        if ($request->comando == 4) {   // comando VentaSemanalReal
            if ($request->cola == 1) {    // en cola
                UpdateIndicador::dispatch($request->indicador)
                    ->onQueue('job');
            } else {
                Artisan::call('indicador:update', [
                    'indicador' => $request->indicador
                ]);
            }
        }
        if ($request->comando == 5) {   // comando ResumenAreaSemanal
            ResumenAreaSemanal::dispatch($request->desde, $request->hasta, $request->variedad)
                ->onQueue('job');
        }
        if ($request->comando == 6) {   // comando UpdateTallosCosechadosProyeccion
            UpdateTallosCosechadosProyeccion::dispatch($request->semana, $request->variedad, $request->modulo)
                ->onQueue('job');
        }
        if ($request->comando == 7) {   // comando ResumenAreaSemanal
            UpdateOtrosGastos::dispatch($request->desde, $request->hasta)
                ->onQueue('job');
        }
        if ($request->comando == 8) {   // comando ResumenAreaSemanal
            UpdateRegalias::dispatch($request->desde, $request->hasta)
                ->onQueue('job');
        }
        if ($request->comando == 9) {   // comando ResumenAreaSemanal
            ResumenCostosSemanal::dispatch($request->desde, $request->hasta)
                ->onQueue('job');
        }

        return ['success' => true];
    }

    /* ========================= INDICADORES ========================== */
    public function indicadores(Request $request)
    {
        return view('adminlte.gestion.db.indicadores', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'indicadores' => getIndicadores(),
        ]);
    }

    public function store_indicador(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:4|unique:indicador',
            'descripcion' => 'required|max:250',
            'valor' => 'required',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'descripcion.required' => 'La descripcón es obligatoria',
            'descripcion.max' => 'La descripcón es muy grande',
            'valor.required' => 'El valor es obligatorio',
        ]);
        if (!$valida->fails()) {
            $model = new Indicador();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 4);
            $model->descripcion = str_limit(espacios($request->descripcion), 250);
            $model->valor = $request->valor;
            $model->estado = $request->estado == 'true' ? 1 : 0;
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Indicador::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo indicador satisfactoriamente</p>'
                    . '</div>';
                bitacora('indicador', $model->id_indicador, 'I', 'Inserción satisfactoria de un nuevo indicador');
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_indicador(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:4',
            'descripcion' => 'required|max:250',
            'valor' => 'required',
            'id' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.max' => 'La descripción es muy grande',
            'valor.required' => 'El valor es obligatorio',
            'id.required' => 'El indicador es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Indicador::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 4))
                    ->where('id_indicador', '!=', $request->id)) == 0) {
                $model = Indicador::find($request->id);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 4);
                $model->descripcion = str_limit(espacios($request->descripcion), 250);
                $model->valor = $request->valor;
                $model->estado = $request->estado == 'true' ? 1 : 0;

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el indicador satisfactoriamente</p>'
                        . '</div>';
                    bitacora('indicador', $model->id_indicador, 'U', 'Actualización satisfactoria de un indicador');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El indicador "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    /* ========================= INTERVALOS INDICADORES ========================== */
    public function intervaloIndicador(Request $request)
    {
        return view('adminlte.gestion.db.intervalos_indicadores.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo' => 'Semaforización', 'subtitulo' => 'módulo de indicadores'],
            'indicadores' => getIndicadores()->where('estado', 1)
        ]);
    }

    public function addIntervaloIndicador(Request $request)
    {
        return view('adminlte.gestion.db.intervalos_indicadores.partials.add_intervalo', [
            'indicador' => $request->id_indicador,
            'intervalos_indicadores' => IntervaloIndicador::where('id_indicador', $request->id_indicador)->get(),

        ]);
    }

    public function addRowIntervaloIndicador(Request $request)
    {

        if ($request->inputs === "rango")
            $view = 'adminlte.gestion.db.intervalos_indicadores.partials.inputs_rango';
        if ($request->inputs === "condicion")
            $view = 'adminlte.gestion.db.intervalos_indicadores.partials.inputs_condicion';

        return view($view, [
            'x' => $request->cant,
            'colores' => Color::where('estado', 1)->get()
        ]);

    }

    public function storeIntervaloIndicador(Request $request)
    {
        //dd($request->all());
        $valida = Validator::make($request->all(), [
            'color.*' => 'required',
            'desde.*' => 'required'
        ], [
            'color.*.required' => 'Hace falta seleccionar colores',
            'desde.*.required' => 'Debe color el número en el campo cantidad o en el campo desde'
        ]);

        if (!$valida->fails()) {
            $dataOld = IntervaloIndicador::where('id_indicador', $request->id_indicador)->select('id_intervalo_indicador')->get();

            foreach ($request->datos as $dato) {
                try {
                    $objIntervaloIndicador = new IntervaloIndicador;
                    $objIntervaloIndicador->id_indicador = $request->id_indicador;
                    $objIntervaloIndicador->tipo = $dato['tipo'];
                    $objIntervaloIndicador->color = $dato['color'];
                    $objIntervaloIndicador->hasta = $dato['hasta'];
                    if ($dato['tipo'] == "I") {
                        $objIntervaloIndicador->desde = $dato['desde'];
                    } else {
                        $objIntervaloIndicador->condicional = $dato['condicional'];
                    }
                    $objIntervaloIndicador->save();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado la información con éxito </p>'
                        . '</div>';
                } catch (\Exception $e) {
                    $success = false;
                    $msg = '<div class="alert alert-danger text-center">' .
                        '<p>  Ha ocurrido el siguiente error al intentar guardar la información <br />"' . $e->getMessage() . '"<br /> Comuníquelo al área de sistemas</p>'
                        . '</div>';
                }

                foreach ($dataOld as $data)
                    IntervaloIndicador::destroy($data->id_intervalo_indicador);
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }
}