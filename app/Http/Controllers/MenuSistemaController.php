<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Icon;
use yura\Modelos\Menu;
use yura\Modelos\Submenu;
use Validator;

class MenuSistemaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.menu_sistema.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All()
        ]);
    }

    public function select_grupo_menu(Request $request)
    {
        $g = GrupoMenu::find($request->id_grupo_menu);
        return view('adminlte.gestion.menu_sistema.partials.listado_menu', [
            'menus' => $g->menus
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
        return view('adminlte.gestion.menu_sistema.forms.partials.select_menus', [
            'menus' => $menus
        ]);
    }

    public function select_menu(Request $request)
    {
        $m = Menu::find($request->id_menu);
        return view('adminlte.gestion.menu_sistema.partials.listado_submenu', [
            'submenus' => $m->submenus
        ]);
    }

    /* =====================================================*/

    public function add_grupo_menu(Request $request)
    {
        return view('adminlte.gestion.menu_sistema.forms.add_grupo_menu');
    }

    public function store_grupo_menu(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25|unique:grupo_menu',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = new GrupoMenu();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = GrupoMenu::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo grupo de menú satisfactoriamente</p>'
                    . '</div>';
                bitacora('grupo_menu', $model->id_grupo_menu, 'I', 'Inserción satisfactoria de un nuevo grupo de menú');
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

    public function add_menu(Request $request)
    {
        return view('adminlte.gestion.menu_sistema.forms.add_menu', [
            'grupos' => GrupoMenu::All()->where('estado', '=', 'A'),
            'iconos' => Icon::All()->where('estado', '=', 'A')->sortBy('nombre')
        ]);
    }

    public function store_menu(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_grupo_menu' => 'required|',
            'id_icono' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_grupo_menu.required' => 'El grupo es obligatorio',
            'id_icono.required' => 'El ícono es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Menu::All()->where('nombre', '=', espacios($request->nombre))
                    ->where('id_grupo_menu', '=', $request->id_grupo_menu)) == 0) {
                $model = new Menu();
                $model->nombre = str_limit((espacios($request->nombre)), 25);
                $model->id_grupo_menu = $request->id_grupo_menu;
                $model->id_icono = $request->id_icono;
                $model->fecha_registro = date('Y-m-d H:i:s');

                if ($model->save()) {
                    $model = Menu::All()->last();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado un nuevo menú satisfactoriamente</p>'
                        . '</div>';
                    bitacora('menu', $model->id_menu, 'I', 'Inserción satisfactoria de un nuevo menú');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El menú "' . espacios($request->nombre) . '" ya se encuentra en este grupo de menús</p>'
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
        return view('adminlte.gestion.menu_sistema.forms.add_submenu', [
            'grupos' => GrupoMenu::All()->where('estado', '=', 'A'),
        ]);
    }

    public function store_submenu(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
            'url' => 'required|max:25',
            'id_menu' => 'required|',
            'tipo' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'url.required' => 'La ruta es obligatoria',
            'id_menu.required' => 'El menú es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'url.max' => 'La ruta es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Submenu::All()->where('nombre', '=', espacios($request->nombre))
                    ->where('id_menu', '=', $request->id_menu)) == 0) {
                $model = new Submenu();
                $model->nombre = str_limit((espacios($request->nombre)), 50);
                $model->url = str_limit(mb_strtolower(espacios($request->url)), 25);
                $model->id_menu = $request->id_menu;
                $model->tipo = $request->tipo;
                $model->fecha_registro = date('Y-m-d H:i:s');

                if ($model->save()) {
                    $model = Submenu::All()->last();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado un nuevo submenú satisfactoriamente</p>'
                        . '</div>';
                    bitacora('submenu', $model->id_submenu, 'I', 'Inserción satisfactoria de un nuevo submenú');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El submenú "' . espacios($request->nombre) . '" ya se encuentra en este menú</p>'
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

    /* =====================================================*/

    public function edit_grupo_menu(Request $request)
    {
        if ($request->has('id_grupo_menu')) {
            $g = GrupoMenu::find($request->id_grupo_menu);
            if ($g != '') {
                return view('adminlte.gestion.menu_sistema.forms.edit_grupo_menu', [
                    'grupo' => $g
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el grupo de menú en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado un grupo de menú</div>';
        }
    }

    public function update_grupo_menu(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_grupo_menu' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_grupo_menu.required' => 'El grupo de menú es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(GrupoMenu::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 25))
                    ->where('id_grupo_menu', '!=', $request->id_grupo_menu)) == 0) {
                $model = GrupoMenu::find($request->id_grupo_menu);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el grupo de menú satisfactoriamente</p>'
                        . '</div>';
                    bitacora('grupo_menu', $model->id_grupo_menu, 'U', 'Actualización satisfactoria de un grupo de menú');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El grupo de menú "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
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

    public function cambiar_estado_grupo_menu(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_grupo_menu')) {
            $g = GrupoMenu::find($request->id_grupo_menu);
            if ($g != '') {
                $g->estado = $request->estado;
                if ($g->save()) {
                    $texto = $request->estado == 'A' ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('grupo_menu', $g->id_grupo_menu, 'U', 'Cambio de estado de un grupo de menú');
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

    public function edit_menu(Request $request)
    {
        if ($request->has('id_menu')) {
            $m = Menu::find($request->id_menu);
            if ($m != '') {
                return view('adminlte.gestion.menu_sistema.forms.edit_menu', [
                    'menu' => $m,
                    'grupos' => GrupoMenu::All()->where('estado', '=', 'A'),
                    'iconos' => Icon::All()->where('estado', '=', 'A')->sortBy('nombre'),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el menú en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado un menú</div>';
        }
    }

    public function update_menu(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_menu' => 'required|',
            'id_grupo_menu' => 'required|',
            'id_icono' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_icono.required' => 'El ícono es obligatorio',
            'id_menu.required' => 'El menú es obligatorio',
            'id_grupo_menu.required' => 'El grupo de menú es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Menu::All()->where('nombre', '=', espacios($request->nombre))
                    ->where('id_grupo_menu', '=', $request->id_grupo_menu)
                    ->where('id_menu', '!=', $request->id_menu)) == 0) {
                $model = Menu::find($request->id_menu);
                $model->nombre = str_limit((espacios($request->nombre)), 25);
                $model->id_grupo_menu = $request->id_grupo_menu;
                $model->id_icono = $request->id_icono;

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el menú satisfactoriamente</p>'
                        . '</div>';
                    bitacora('menu', $model->id_menu, 'U', 'Actualización satisfactoria de un menú');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El menú "' . espacios($request->nombre) . '" ya se encuentra en este grupo de menús</p>'
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

    public function cambiar_estado_menu(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_menu')) {
            $m = Menu::find($request->id_menu);
            if ($m != '') {
                $m->estado = $request->estado;
                if ($m->save()) {
                    $texto = $request->estado == 'A' ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('menu', $m->id_menu, 'U', 'Cambio de estado de un menú');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el menú en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un menú</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

    public function edit_submenu(Request $request)
    {
        if ($request->has('id_submenu')) {
            $s = Submenu::find($request->id_submenu);
            if ($s != '') {
                return view('adminlte.gestion.menu_sistema.forms.edit_submenu', [
                    'submenu' => $s,
                    'grupos' => GrupoMenu::All()->where('estado', '=', 'A')
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el submenú en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado un submenú</div>';
        }
    }

    public function update_submenu(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
            'url' => 'required|max:25',
            'id_menu' => 'required|',
            'id_submenu' => 'required|',
            'tipo' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'url.required' => 'La ruta es obligatoria',
            'id_menu.required' => 'El menú es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
            'id_submenu.required' => 'El submenú es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'url.max' => 'La ruta es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Submenu::All()->where('nombre', '=', espacios($request->nombre))
                    ->where('id_menu', '=', $request->id_menu)
                    ->where('id_submenu', '!=', $request->id_submenu)) == 0) {
                $model = Submenu::find($request->id_submenu);
                $model->nombre = str_limit((espacios($request->nombre)), 50);
                $model->url = str_limit(mb_strtolower(espacios($request->url)), 25);
                $model->id_menu = $request->id_menu;
                $model->tipo = $request->tipo;
                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el submenú satisfactoriamente</p>'
                        . '</div>';
                    bitacora('submenu', $model->id_submenu, 'U', 'Actualización satisfactoria de un submenú');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El submenú "' . espacios($request->nombre) . '" ya se encuentra en este menú</p>'
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

    public function cambiar_estado_submenu(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_submenu')) {
            $s = Submenu::find($request->id_submenu);
            if ($s != '') {
                $s->estado = $request->estado;
                if ($s->save()) {
                    $texto = $request->estado == 'A' ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('submenu', $s->id_submenu, 'U', 'Cambio de estado de un submenú');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el submenú en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un submenú</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

}