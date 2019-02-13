<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Menu;
use yura\Modelos\Rol;
use yura\Modelos\Rol_Submenu;
use yura\Modelos\Submenu;
use Validator;
use yura\Modelos\Usuario;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.permisos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'roles' => Rol::All()
        ]);
    }

    public function select_rol_submenus(Request $request)
    {
        $rol = Rol::find($request->id_rol);
        return view('adminlte.gestion.permisos.partials.listado_submenu', [
            'rol' => $rol
        ]);
    }

    public function select_rol_usuarios(Request $request)
    {
        $rol = Rol::find($request->id_rol);
        return view('adminlte.gestion.permisos.partials.listado_usuario', [
            'rol' => $rol
        ]);
    }

    public function listar_menus_x_grupo(Request $request)
    {
        $menus = [];
        if ($request->id_grupo_menu != '') {
            $g = GrupoMenu::find($request->id_grupo_menu);
            if ($g != '')
                $menus = $g->menus;
        }
       //dd($menus);
        return view('adminlte.gestion.permisos.forms.partials.select_menus', [
            'menus' => $menus
        ]);
    }

    public function listar_submenus_x_menu(Request $request)
    {
        $submenus = [];
        if ($request->id_menu != '') {
            $m = Menu::find($request->id_menu);
            $existing = DB::table('rol_submenu as rs')
                ->select('rs.id_submenu')->distinct()
                ->where('rs.id_rol', '=', $request->id_rol)->get();
            $ex = [];
            foreach ($existing as $item) {
                $ex[] = $item->id_submenu;
            }
            if ($m != '')
                $submenus = $m->submenus->whereNotIn('id_submenu', $ex);
        }
        return view('adminlte.gestion.permisos.forms.partials.select_submenus', [
            'submenus' => $submenus
        ]);
    }


    /* =====================================================*/

    public function add_rol(Request $request)
    {
        return view('adminlte.gestion.permisos.forms.add_rol');
    }

    public function store_rol(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25|unique:rol',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = new Rol();
            $model->nombre = str_limit(strtoupper(espacios($request->nombre)), 25);
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Rol::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo rol satisfactoriamente</p>'
                    . '</div>';
                bitacora('rol', $model->id_rol, 'I', 'Inserción satisfactoria de un nuevo rol');
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

    public function add_submenu(Request $request)
    {
        return view('adminlte.gestion.permisos.forms.add_submenu', [
            'grupos' => GrupoMenu::All()->where('estado', '=', 'A')
        ]);
    }

    public function store_submenu(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_submenu' => 'required|',
            'id_rol' => 'required|',
        ], [
            'id_submenu.unique' => 'El submenú ya existe',
            'id_rol.required' => 'El rol es obligatorio',
        ]);

        if (!$valida->fails()) {
            if (count(Rol_Submenu::All()->where('id_rol', '=', $request->id_rol)
                    ->where('id_submenu', '=', $request->id_submenu)) == 0) {
                $model = new Rol_Submenu();
                $model->id_rol = $request->id_rol;
                $model->id_submenu = $request->id_submenu;
                $model->fecha_registro = date('Y-m-d H:i:s');

                if ($model->save()) {
                    $model = Rol_Submenu::All()->last();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha asociado el submenú al rol satisfactoriamente</p>'
                        . '</div>';
                    bitacora('rol_submenu', $model->id_rol_submenu, 'I', 'Inserción satisfactoria de un nuevo rol_submenu');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El submenú ya está asociado a este rol</p>'
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

    public function add_usuario(Request $request)
    {
        $ex = [];
        $existing = DB::table('usuario as u')
            ->select('u.id_usuario')->distinct()
            ->where('id_rol', '=', $request->id_rol)->get();
        foreach ($existing as $item) {
            $ex[] = $item->id_usuario;
        }
        return view('adminlte.gestion.permisos.forms.add_usuario', [
            'usuarios' => Usuario::All()->where('estado', '=', 'A')->whereNotIn('id_usuario', $ex)
        ]);
    }

    public function store_usuario(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_usuario' => 'required|',
            'id_rol' => 'required|',
        ], [
            'id_usuario.unique' => 'El usuario ya existe',
            'id_rol.required' => 'El rol es obligatorio',
        ]);

        if (!$valida->fails()) {
            $model = Usuario::find($request->id_usuario);
            $model->id_rol = $request->id_rol;
            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha asociado el rol al usuario satisfactoriamente</p>'
                    . '</div>';
                bitacora('usuario', $model->id_usuario, 'U', 'Actualización satisfactoria de un rol a un usuario');
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

    /* ======================================================== */

    public function cambiar_estado_rol(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_rol')) {
            $r = Rol::find($request->id_rol);
            if ($r != '') {
                $r->estado = $request->estado;
                if ($r->save()) {
                    $texto = $request->estado == 'A' ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('rol', $r->id_rol, 'U', 'Cambio de estado de un rol');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el grupo de menú en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un grupo de menú</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

    public function cambiar_estado_rol_submenu(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_rol_submenu')) {
            $rs = Rol_Submenu::find($request->id_rol_submenu);
            if ($rs != '') {
                $rs->estado = $request->estado;
                if ($rs->save()) {
                    $texto = $request->estado == 'A' ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('rol_submenu', $rs->id_rol_submenu, 'U', 'Cambio de estado de un submenú');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el grupo de menú en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un grupo de menú</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

}
