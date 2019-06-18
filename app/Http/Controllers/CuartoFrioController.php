<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Empaque;
use yura\Modelos\InventarioFrio;
use yura\Modelos\Submenu;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;

class CuartoFrioController extends Controller
{
    public function inicio(Request $request)
    {
        $inventarios = [];
        $query = DB::table('inventario_frio as i')
            ->select(DB::raw('sum(i.disponibles) as disponibles'),
                'i.id_variedad', 'i.id_clasificacion_ramo', 'i.id_empaque_p', 'i.tallos_x_ramo', 'i.longitud_ramo', 'i.id_unidad_medida')
            ->join('variedad as v', 'v.id_variedad', '=', 'i.id_variedad')
            ->join('clasificacion_ramo as r', 'r.id_clasificacion_ramo', '=', 'i.id_clasificacion_ramo')
            ->where('i.estado', '=', 1)
            ->where('i.disponibles', '>', 0)
            ->where('i.disponibilidad', '=', 1)
            ->where('i.basura', '=', 0)
            ->groupBy('i.id_variedad', 'i.id_clasificacion_ramo', 'i.id_empaque_p', 'i.tallos_x_ramo', 'i.longitud_ramo', 'i.id_unidad_medida')
            ->orderBy('v.nombre')
            ->orderBy('r.nombre', 'desc')
            ->orderBy('i.fecha_ingreso')
            ->get();

        foreach ($query as $q) {
            $dias = [];
            for ($i = 0; $i <= 9; $i++) {
                $ingresos = DB::table('inventario_frio as i')
                    ->select(DB::raw('sum(i.disponibles) as cant'))
                    ->where('i.estado', '=', 1)
                    ->where('i.disponibles', '>', 0)
                    ->where('i.disponibilidad', '=', 1)
                    ->where('i.basura', '=', 0)
                    ->where('i.id_variedad', '=', $q->id_variedad)
                    ->where('i.id_clasificacion_ramo', '=', $q->id_clasificacion_ramo)
                    ->where('i.id_empaque_p', '=', $q->id_empaque_p)
                    ->where('i.tallos_x_ramo', '=', $q->tallos_x_ramo)
                    ->where('i.longitud_ramo', '=', $q->longitud_ramo)
                    ->where('i.id_unidad_medida', '=', $q->id_unidad_medida);
                if ($i == 9)
                    $ingresos = $ingresos->where('i.fecha_ingreso', '<=', opDiasFecha('-', $i, date('Y-m-d')));
                else
                    $ingresos = $ingresos->where('i.fecha_ingreso', '=', opDiasFecha('-', $i, date('Y-m-d')));
                $ingresos = $ingresos->get();

                array_push($dias, [
                    'dia' => $i,
                    'cantidad' => $ingresos[0]->cant,
                ]);
            }
            array_push($inventarios, [
                'variedad' => Variedad::find($q->id_variedad),
                'peso' => ClasificacionRamo::find($q->id_clasificacion_ramo),
                'presentacion' => Empaque::find($q->id_empaque_p),
                'tallos_x_ramo' => $q->tallos_x_ramo,
                'longitud_ramo' => $q->longitud_ramo,
                'unidad_medida' => UnidadMedida::find($q->id_unidad_medida),
                'disponibles' => $q->disponibles,
                'dias' => $dias
            ]);
        }

        return view('adminlte.gestion.postcocecha.cuarto_frio.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],

            'inventarios' => $inventarios
        ]);
    }

    public function add_inventario(Request $request)
    {
        foreach ($request->add as $data) {
            $fecha = opDiasFecha('-', $data['dia'], date('Y-m-d'));
            /* ============= BLANCO ============= */
            $blanco = ClasificacionBlanco::All()->where('fecha_ingreso', $fecha)->first();
            if ($blanco == '') {
                $blanco = new ClasificacionBlanco();
                $blanco->fecha_ingreso = $fecha;
                $blanco->hora_inicio = ClasificacionBlanco::All()->last()->hora_inicio;
                $blanco->personal = ClasificacionBlanco::All()->last()->personal;

                $blanco->save();
                $blanco = ClasificacionBlanco::All()->last();
            }
            /* ============= INVENTARIO ============= */
            $inventario = new InventarioFrio();
            $inventario->id_clasificacion_blanco = $blanco->id_clasificacion_blanco;
            $inventario->id_variedad = $request->data['variedad'];
            $inventario->id_clasificacion_ramo = $request->data['peso'];
            $inventario->id_empaque_p = $request->data['presentacion'];
            $inventario->tallos_x_ramo = $request->data['tallos_x_ramo'];
            $inventario->longitud_ramo = $request->data['longitud_ramo'];
            $inventario->id_unidad_medida = $request->data['unidad_medida'];
            $inventario->fecha_ingreso = $fecha;
            $inventario->cantidad = $data['valor'];
            $inventario->disponibles = $data['valor'];

            $texto = getVariedad($request->data['variedad'])->siglas;
            $texto .= ' ' . explode('|', ClasificacionRamo::find($request->data['peso'])->nombre)[0];
            $texto .= ClasificacionRamo::find($request->data['peso'])->unidad_medida->siglas;
            $texto .= ' ' . explode('|', getEmpaque($request->data['presentacion'])->nombre)[0];
            $texto .= $request->data['tallos_x_ramo'] != '' ? ', ' . $request->data['tallos_x_ramo'] : '';
            $texto .= $request->data['longitud_ramo'] != '' ? ' ' . $request->data['longitud_ramo'] . getUnidadMedida($request->data['unidad_medida'])->siglas : '';

            $inventario->descripcion = $texto;
            $inventario->save();
            $inventario = InventarioFrio::All()->last();
            bitacora('inventario_frio', $inventario->id_inventario_frio, 'I', 'Insercion de un nuevo inventario a cuarto frio');
        }
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se han ingresado satisfactoriamente los ramos a inventario</div>',
        ];
    }

    public function delete_dia(Request $request)
    {
        $fecha = opDiasFecha('-', $request->dia, date('Y-m-d'));
        $list = InventarioFrio::All()
            ->where('estado', 1)
            ->where('disponibilidad', 1)
            ->where('basura', 0)
            ->where('disponibles', '>', 0);

        if ($request->dia == 9) {
            $list = $list->where('fecha_ingreso', '<=', $fecha);
        } else
            $list = $list->where('fecha_ingreso', $fecha);

        foreach ($list as $inv) {
            $basura = $inv->disponibles;
            $inv->disponibles = 0;
            $inv->disponibilidad = 0;

            $inv->save();
            bitacora('inventario_frio', $inv->id_inventario_frio, 'U', 'Modificacion de un inventario en cuarto frio');

            $basura_inv = new InventarioFrio();
            $basura_inv->basura = 1;
            $basura_inv->fecha_ingreso = $fecha;
            $basura_inv->cantidad = $basura;
            $basura_inv->disponibles = 0;
            $basura_inv->disponibilidad = 0;
            $basura_inv->id_clasificacion_blanco = $inv->id_clasificacion_blanco;
            $basura_inv->id_variedad = $inv->id_variedad;
            $basura_inv->id_clasificacion_ramo = $inv->id_clasificacion_ramo;
            $basura_inv->id_empaque_p = $inv->id_empaque_p;
            $basura_inv->tallos_x_ramo = $inv->tallos_x_ramo;
            $basura_inv->longitud_ramo = $inv->longitud_ramo;
            $basura_inv->id_unidad_medida = $inv->id_unidad_medida;
            $basura_inv->descripcion = $inv->descripcion;

            $basura_inv->save();
            bitacora('inventario_frio', $basura_inv->id_inventario_frio, 'I', 'Inserción de una nueva basura en inventario en cuarto frio');
        }
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se han eliminado satisfactoriamente los ramos del inventario</div>',
        ];
    }

    public function save_dia(Request $request)
    {
        $fecha = opDiasFecha('-', $request->data['dia'], date('Y-m-d'));

        $models = InventarioFrio::where('disponibilidad', 1)
            ->where('estado', 1)
            ->where('basura', 0)
            ->where('disponibles', '>', 0)
            ->where('id_variedad', $request->data['variedad'])
            ->where('id_clasificacion_ramo', $request->data['peso'])
            ->where('tallos_x_ramo', $request->data['tallos_x_ramo'])
            ->where('longitud_ramo', $request->data['longitud_ramo'])
            ->where('id_empaque_p', $request->data['presentacion'])
            ->where('id_unidad_medida', $request->data['unidad_medida'])
            ->get();

        if ($request->data['dia'] == 9) {
            $models = $models->where('fecha_ingreso', '<=', $fecha);
        } else
            $models = $models->where('fecha_ingreso', $fecha);

        $meta = $request->data['editar'];

        foreach ($models as $pos => $model) {
            if ($meta > 0) {
                if ($model->disponibles >= $meta) {
                    $model->disponibles = $model->disponibles - $meta;
                    $meta = 0;
                } else {
                    $meta -= $model->disponibles;
                    $model->disponibles = 0;
                }

                if ($model->disponibles == 0)
                    $model->disponibilidad = 0;

                $model->save();
                bitacora('inventario_frio', $model->id_inventario_frio, 'U', 'Actualizacion de un inventario en frio');
            } else
                break;
        }

        /* ============== INVENTARIOS NUEVOS ============= */
        if ($request->has('arreglo')) {
            foreach ($request->arreglo as $item) {
                $inv = new InventarioFrio();
                $inv->disponibilidad = 1;
                $inv->basura = 0;
                $inv->fecha_ingreso = $fecha;
                $inv->id_variedad = $item['inventario']['variedad'];
                $inv->id_clasificacion_ramo = $item['inventario']['peso'];
                $inv->tallos_x_ramo = $item['inventario']['tallos_x_ramo'];
                $inv->longitud_ramo = $item['inventario']['longitud_ramo'];
                $inv->id_empaque_p = $item['inventario']['presentacion'];
                $inv->id_unidad_medida = $item['inventario']['unidad_medida'];
                $inv->cantidad = $item['inventario']['add'];
                $inv->disponibles = $item['inventario']['add'];

                $texto = getVariedad($item['inventario']['variedad'])->siglas;
                $texto .= ' ' . explode('|', ClasificacionRamo::find($item['inventario']['peso'])->nombre)[0];
                $texto .= ClasificacionRamo::find($item['inventario']['peso'])->unidad_medida->siglas;
                $texto .= ' ' . explode('|', getEmpaque($item['inventario']['presentacion'])->nombre)[0];
                $texto .= $item['inventario']['tallos_x_ramo'] != '' ? ', ' . $item['inventario']['tallos_x_ramo'] : '';
                $texto .= $item['inventario']['longitud_ramo'] != '' ? ' ' . $item['inventario']['longitud_ramo'] . getUnidadMedida($item['inventario']['unidad_medida'])->siglas : '';

                $inv->descripcion = $texto;
                $inv->save();
                $inv = InventarioFrio::All()->last();
                bitacora('inventario_frio', $inv->id_inventario_frio, 'I', 'Insercion de un nuevo inventario a cuarto frio');
            }
        }
        /* ============== BASURA ============= */
        if ($request->basura > 0) {
            $inv = new InventarioFrio();
            $inv->disponibilidad = 0;
            $inv->basura = 1;
            $inv->fecha_ingreso = $fecha;
            $inv->id_variedad = $request->data['variedad'];
            $inv->id_clasificacion_ramo = $request->data['peso'];
            $inv->tallos_x_ramo = $request->data['tallos_x_ramo'];
            $inv->longitud_ramo = $request->data['longitud_ramo'];
            $inv->id_empaque_p = $request->data['presentacion'];
            $inv->id_unidad_medida = $request->data['unidad_medida'];
            $inv->cantidad = $request->basura;
            $inv->disponibles = 0;

            $texto = getVariedad($request->data['variedad'])->siglas;
            $texto .= ' ' . explode('|', ClasificacionRamo::find($request->data['peso'])->nombre)[0];
            $texto .= ClasificacionRamo::find($request->data['peso'])->unidad_medida->siglas;
            $texto .= ' ' . explode('|', getEmpaque($request->data['presentacion'])->nombre)[0];
            $texto .= $request->data['tallos_x_ramo'] != '' ? ', ' . $request->data['tallos_x_ramo'] : '';
            $texto .= $request->data['longitud_ramo'] != '' ? ' ' . $request->data['longitud_ramo'] . getUnidadMedida($request->data['unidad_medida'])->siglas : '';

            $inv->descripcion = $texto;
            $inv->save();
            $inv = InventarioFrio::All()->last();
            bitacora('inventario_frio', $inv->id_inventario_frio, 'I', 'Insercion de un nuevo inventario a cuarto frio');
        }

        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se han modificado satisfactoriamente los ramos en inventario de cuarto frío</div>',
        ];
    }
}