<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\Marca;
use Validator;
use DB;

class MarcaController extends Controller
{

    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.marcas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Clientes', 'subtitulo' => 'módulo de postcocecha']
            ]);
    }

    public function buscar_marcas(Request $request)
    {
        //dsdd($request->busqueda);
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('marcas as m')
            ->where('m.estado', 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->Where('m.nombre', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('m.nombre', 'asc')->paginate(20);

        $datos = [
            'listado' => $listado
        ];
        return view('adminlte.gestion.postcocecha.marcas.partials.listado', $datos);
    }

    public function add_marcas(Request $request)
    {
        !empty($request->id_marca)
            ? $dataMarca = Marca::where([
            ['id_marca', $request->id_marca],
            ['estado', 1]
        ])->first()
            : $dataMarca = '';

        return view('adminlte.gestion.postcocecha.marcas.forms.add_marcas', [
            'dataMarca' => $dataMarca
        ]);
    }

    public function store_marcas(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'marca' => 'required',
            'descripcion' => 'required',
        ]);

        if (!$valida->fails()) {
            $msg = '';
            if(empty($request->id_marca)){
                $objMarca = new Marca;
                $palabra = 'Inserción';
                $accion   = 'I';
            }else{
                $objMarca = Marca::find($request->id_marca);
                $palabra = 'Actualización';
                $accion   = 'U';
            }

            $objMarca->nombre = $request->marca;
            $objMarca->descripcion = $request->descripcion;

            if ($objMarca->save()) {
                $model = Marca::all()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado la marca ' . $objMarca->nombre . '  exitosamente</p>'
                    . '</div>';
                bitacora('marcas', $model->id_marca, $accion, $palabra . ' satisfactoria de una nueva marca');
            }else{
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
}
