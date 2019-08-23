<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\Documento;

class DocumentoController extends Controller
{
    public function add_documento(Request $request)
    {
        return view('documento.add_documento', [
            'entidad' => $request->entidad,
            'codigo' => $request->codigo,
        ]);
    }

    public function load_input(Request $request)
    {
        return view('documento.inputs.' . $request->input, [
            'number' => $request->number,
            'input' => $request->input
        ]);
    }

    public function store_documento(Request $request)
    {
        //dd($request->request);
        $msg = '';
        $success = true;
        $valida = Validator::make($request->all(), [
            'entidad' => 'required|max:250',
            'codigo' => 'required',
        ], [
            'entidad.required' => 'La entidad es obligatoria',
            'codigo.required' => 'El código es obligatorio',
            'entidad.max' => 'La entidad es muy grande',
        ]);
        if (!$valida->fails()) {
            foreach ($request->arreglo as $item) {
                $documento = new Documento();
                $documento->fecha_registro = date('Y-m-d H:i:s');
                $documento->entidad = $request->entidad;
                $documento->codigo = $request->codigo;
                $documento->nombre_campo = str_limit((espacios($item['nombre_campo'])), 250);
                $documento->descripcion = str_limit((espacios($item['descripcion'])), 4000);
                $documento->tipo_dato = $item['tipo_dato'];
                if ($item['tipo_dato'] == 'int')
                    $documento->int = $item['valor'];
                if ($item['tipo_dato'] == 'float')
                    $documento->float = $item['valor'];
                if ($item['tipo_dato'] == 'char')
                    $documento->char = $item['valor'];
                if ($item['tipo_dato'] == 'varchar')
                    $documento->varchar = $item['valor'];
                if ($item['tipo_dato'] == 'boolean')
                    $documento->boolean = $item['valor'];
                if ($item['tipo_dato'] == 'date')
                    $documento->date = $item['valor'];
                if ($item['tipo_dato'] == 'datetime')
                    $documento->datetime = $item['valor'];

                if ($documento->save()) {
                    $documento = Documento::All()->last();
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado una nueva información satisfactoriamente: "' . $item['nombre_campo'] . '"</p>'
                        . '</div>';
                    bitacora('documento', $documento->id_documento, 'I', 'Inserción satisfactoria de un nuevo documento');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar el campo ' . $item['nombre_campo'] . ' en el sistema</p>'
                        . '</div>';
                }
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

    public function ver_documentos(Request $request)
    {
        return view('documento.ver_documentos', [
            'documentos' => getDocumentos($request->entidad, $request->codigo)
        ]);
    }

    public function update_documento(Request $request)
    {
        //dd($request->request);
        $msg = '';
        $success = true;
        $valida = Validator::make($request->all(), [
            'id_documento' => 'required',
            'valor' => 'required',
            'nombre_campo' => 'required',
            //'descripcion' => 'required',
        ], [
            'id_documento.required' => 'El documento es obligatorio',
            'valor.required' => 'El valor es obligatorio',
            //'descripcion.required' => 'La descripción es obligatoria',
            'nombre_campo.required' => 'El nombre del campo es obligatorio',
        ]);
        if (!$valida->fails()) {
            $documento = Documento::find($request->id_documento);
            $documento->nombre_campo = str_limit((espacios($request->nombre_campo)), 250);
            $documento->descripcion = str_limit((espacios($request->descripcion)), 4000);
            if ($documento->tipo_dato == 'int')
                $documento->int = $request->valor;
            if ($documento->tipo_dato == 'float')
                $documento->float = $request->valor;
            if ($documento->tipo_dato == 'char')
                $documento->char = $request->valor;
            if ($documento->tipo_dato == 'varchar')
                $documento->varchar = $request->valor;
            if ($documento->tipo_dato == 'boolean')
                $documento->boolean = $request->valor;
            if ($documento->tipo_dato == 'date')
                $documento->date = $request->valor;
            if ($documento->tipo_dato == 'datetime')
                $documento->datetime = $request->valor;

            if ($documento->save()) {
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado satisfactoriamente</p>'
                    . '</div>';
                bitacora('documento', $documento->id_documento, 'U', 'Actualización satisfactoria de un documento');
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información en el sistema</p>'
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

    public function delete_documento(Request $request)
    {
        $documento = Documento::find($request->id_documento);
        if ($documento->delete()) {
            bitacora('documento', $request->id_documento, 'D', 'Eliminacion de un documento');
            return [
                'success' => true,
                'mensaje' => '<div class="alert alert-success text-center">' .
                    'Se ha eliminado satisfactoriamente la información del sistema</div>'
            ];
        } else {
            return [
                'success' => true,
                'mensaje' => '<div class="alert alert-success text-center">' .
                    'Se ha eliminado satisfactoriamente la información del sistema</div>'
            ];
        }
    }
}
