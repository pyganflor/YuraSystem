<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use phpseclib\Crypt\RSA;
use yura\Modelos\Rol;
use yura\Modelos\Submenu;
use yura\Modelos\Usuario;
use Validator;
use Storage as Almacenamiento;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Worksheet_MemoryDrawing;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;

class UsuarioController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.usuarios.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function buscar_usuarios(Request $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = DB::table('usuario as u')
            ->join('rol as r', 'r.id_rol', '=', 'u.id_rol')
            ->select('u.*', 'r.nombre as rol');

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($mi_busqueda_toupper, $mi_busqueda_tolower) {
            $q->Where('u.nombre_completo', 'like', '%' . $mi_busqueda_toupper . '%')
                ->orWhere('u.correo', 'like', '%' . $mi_busqueda_tolower . '%')
                ->orWhere('u.username', 'like', '%' . $mi_busqueda_toupper . '%')
                ->orWhere('r.nombre', 'like', '%' . $mi_busqueda_toupper . '%');
        });

        $listado = $listado->orderBy('u.nombre_completo', 'asc')->orderBy('r.nombre', 'asc')
            ->get();

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.usuarios.partials.listado', $datos);
    }

    public function eliminar_usuarios(Request $request)
    {
        $model = Usuario::find($request->id_usuario);
        if ($model != '') {
            $model->estado = $model->estado == 'A' ? 'I' : 'A';
            if ($model->save()) {
                bitacora('usuario', $model->id_usuario, 'U', 'Actualización satisfactoria del estado del usuario');

                return [
                    'success' => true,
                    'estado' => $model->estado == 'A' ? true : false,
                    'mensaje' => '',
                ];
            } else {
                return [
                    'success' => false,
                    'estado' => '',
                    'mensaje' => '<div class="alert alert-info text-center">Ha ocurrido un problema al guardar en el sistema</div>',
                ];
            }
        } else {
            return [
                'success' => false,
                'estado' => '',
                'mensaje' => '<div class="alert alert-info text-center">No se ha encontrado en el sistema el parámetro</div>',
            ];
        }
    }

    public function add_usuarios(Request $request)
    {
        $rsa = new RSA();

        $rsa->loadKey(Session::get('key_privada'));
        $raw = $rsa->getPublicKey(RSA::PUBLIC_FORMAT_RAW);

        return view('adminlte.gestion.usuarios.forms.add_usuario', [
            'key' => $raw['n']->toHex(),
            'roles' => Rol::All()
        ]);
    }

    public function store_usuarios(Request $request)
    {
        $msg = '';
        $success = true;

        $valida = Validator::make($request->all(), [
            'nombre_completo' => 'required|min:3|unique:usuario|max:250',
            'correo' => 'required|email|unique:usuario|max:250',
            'username' => 'required|unique:usuario|max:250',
            'id_rol' => 'required|',
            'h_clave' => 'required|',
        ], [
            'nombre_completo.required' => 'El nombre es obligatorio',
            'correo.required' => 'El correo es obligatorio',
            'username.required' => 'El nombre de usuario es obligatorio',
            'id_rol.required' => 'El rol es obligatorio',
            'h_clave.required' => 'La contraseña es obligatoria',
            'nombre_completo.unique' => 'El nombre ya existe',
            'username.unique' => 'El nombre de usuario ya existe',
            'correo.unique' => 'El correo ya existe',
            'correo.email' => 'El correo es inválido',
            'correo.max' => 'El correo es muy grande',
            'username.max' => 'El nombre de usuario es muy grande',
            'nombre_completo.max' => 'El nombre es muy grande',
            'nombre_completo.min' => 'El nombre es muy corto',
        ]);
        if (!$valida->fails()) {
            $model = new Usuario();

            $model->estado = 'A';
            $model->id_rol = $request->id_rol;
            $model->correo = str_limit(mb_strtolower(espacios($request->correo)), 250);
            $model->nombre_completo = str_limit(mb_strtoupper(espacios($request->nombre_completo)), 250);
            $model->username = str_limit(mb_strtolower(espacios($request->username)), 250);
            $model->fecha_registro = date('Y-m-d H:i:s');

            $pwd1 = $this->decrypt(Session::get('key_privada'), $request->h_clave);

            $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/";
            if (trim($pwd1) != '' && preg_match($patron, $pwd1)) {
                $pwd1 = Hash::make($pwd1);
                $model->password = $pwd1;

                //------------------------------    GRABAR LA IMAGEN DE PERFIL  -----------------------------------------
                try {
                    if ($request->hasFile('imagen_perfil')) {
                        $archivo = $request->file('imagen_perfil');
                        $input = array('image' => $archivo);
                        $reglas = array('image' => 'required|image|mimes:jpeg,jpeg|max:2000');
                        $validacion = Validator::make($input, $reglas);

                        if ($validacion->fails()) {
                            return [
                                'mensaje' => '<div class="alert alert-danger text-center">' .
                                    '<p>¡Imagen no válida!</p>' .
                                    '</div>',
                                'success' => false
                            ];
                        } else {
                            $nombre_original = $archivo->getClientOriginalName();
                            $extension = $archivo->getClientOriginalExtension();
                            $imagen = "imagen_perfil_" . date('Y_d_m_H_i_s') . "-" . $model->username . "." . $extension;
                            $r1 = Almacenamiento::disk('imagenes/especificaciones')->put($imagen, \File::get($archivo));
                            if (!$r1) {
                                return [
                                    'mensaje' => '<div class="alert alert-danger text-center">' .
                                        '<p>¡No se pudo subir la imagen!</p>' .
                                        '</div>',
                                    'success' => false
                                ];
                            } else {
                                $model->imagen_perfil = $imagen;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    return [
                        'mensaje' => '<div class="alert alert-danger text-center">' .
                            '<p>¡Ha ocurrido un problema al guardar la imagen en el sistema!</p>' .
                            $e->getMessage() .
                            '</div>',
                        'success' => false
                    ];
                }

                if ($model->save()) {
                    $model = Usuario::All()->last();
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado un nuevo usuario satisfactoriamente</p>'
                        . '</div>';
                    bitacora('usuario', $model->id_usuario, 'I', 'Inserción satisfactoria de un nuevo usuario');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }

            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Contraseña mal estructurada. (Mínimo 6 caracteres, incluyendo 1 dígito, 1 letra minúscula y 1 letra mayúscula)</p>'
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
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    function decrypt($privatekey, $encrypted)
    {
        $rsa = new RSA();

        $encrypted = pack('H*', $encrypted);

        $rsa->loadKey($privatekey);
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        return $rsa->decrypt($encrypted);
    }

    public function ver_usuario(Request $request)
    {
        if ($request->has('id_usuario')) {
            $u = Usuario::find($request->id_usuario);
            if ($u != '') {
                $rsa = new RSA();

                $rsa->loadKey(Session::get('key_privada'));
                $raw = $rsa->getPublicKey(RSA::PUBLIC_FORMAT_RAW);

                return view('adminlte.gestion.usuarios.partials.detalles', [
                    'usuario' => $u,
                    'key' => $raw['n']->toHex(),
                    'roles' => Rol::All()
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el usuario en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ningún usuario</div>';
        }
    }

    public function update_usuario(Request $request)
    {
        $msg = '';
        $success = true;

        $valida = Validator::make($request->all(), [
            'nombre_completo' => 'required|min:3|max:250',
            'correo' => 'required|email|max:250',
            'username' => 'required|max:250',
            'id_rol' => 'required|',
            'id_usuario' => 'required|',
        ], [
            'nombre_completo.required' => 'El nombre es obligatorio',
            'correo.required' => 'El correo es obligatorio',
            'username.required' => 'El nombre de usuario es obligatorio',
            'id_rol.required' => 'El rol es obligatorio',
            'id_usuario.required' => 'El usuario es obligatorio',
            'correo.email' => 'El correo es inválido',
            'correo.max' => 'El correo es muy grande',
            'username.max' => 'El nombre de usuario es muy grande',
            'nombre_completo.max' => 'El nombre es muy grande',
            'nombre_completo.min' => 'El nombre es muy corto',
        ]);
        if (!$valida->fails()) {
            if (count(Usuario::All()
                    ->where('nombre_completo', '=', str_limit(mb_strtoupper(espacios($request->nombre_completo)), 250))
                    ->where('username', '=', str_limit(mb_strtolower(espacios($request->username)), 250))
                    ->where('correo', '=', str_limit(mb_strtolower(espacios($request->correo)), 250))
                    ->where('id_usuario', '!=', $request->id_usuario)
                ) == 0) {

                $model = Usuario::find($request->id_usuario);

                $model->id_rol = $request->id_rol;
                $model->correo = str_limit(mb_strtolower(espacios($request->correo)), 250);
                $model->nombre_completo = str_limit(mb_strtoupper(espacios($request->nombre_completo)), 250);
                $model->username = str_limit(mb_strtolower(espacios($request->username)), 250);

                if ($model->save()) {
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado el usuario satisfactoriamente</p>'
                        . '</div>';
                    bitacora('usuario', $model->id_usuario, 'U', 'Actualización satisfactoria de un usuario');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }

            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ingresado datos de usuario que ya existen</p>'
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
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function update_image_perfil(Request $request)
    {
        dd(55);
        $model = Usuario::find($request->id_usuario);
        //------------------------------    GRABAR LA IMAGEN DE PERFIL  -----------------------------------------
        try {
            if ($request->hasFile('imagen_perfil')) {
                $archivo = $request->file('imagen_perfil');
                $input = array('image' => $archivo);
                $reglas = array('image' => 'required|image|mimes:jpeg,jpeg|max:2000');
                $validacion = Validator::make($input, $reglas);

                if ($validacion->fails()) {
                    return [
                        'mensaje' => '<div class="alert alert-danger text-center">' .
                            '<p>¡Imagen no válida!</p>' .
                            '</div>',
                        'success' => false
                    ];
                } else {
                    $nombre_original = $archivo->getClientOriginalName();
                    $extension = $archivo->getClientOriginalExtension();
                    $imagen = "imagen_perfil_" . date('Y_d_m_H_i_s') . "-" . $model->username . "." . $extension;
                    $r1 = Almacenamiento::disk('imagenes')->put($imagen, \File::get($archivo));
                    if (!$r1) {
                        return [
                            'mensaje' => '<div class="alert alert-danger text-center">' .
                                '<p>¡No se pudo subir la imagen!</p>' .
                                '</div>',
                            'success' => false
                        ];
                    } else {
                        if ($model->imagen_perfil != 'logo_usuario.png') {
                            $r1 = Almacenamiento::disk('imagenes')->delete($model->imagen_perfil);
                            if (!$r1) {
                                return [
                                    'mensaje' => '<div class="alert alert-danger text-center">' .
                                        '<p>¡No se pudo eliminar la imagen anterior!</p>' .
                                        '</div>',
                                    'success' => false
                                ];
                            }
                        }
                        $model->imagen_perfil = $imagen;
                    }
                }
            } else {
                if ($model->imagen_perfil != 'logo_usuario.png') {
                    $r1 = Almacenamiento::disk('imagenes')->delete($model->imagen_perfil);
                    if (!$r1) {
                        return [
                            'mensaje' => '<div class="alert alert-danger text-center">' .
                                '<p>¡No se pudo eliminar la imagen anterior!</p>' .
                                '</div>',
                            'success' => false
                        ];
                    }
                }
                $model->imagen_perfil = 'logo_usuario.png';
            }

            if ($model->save()) {
                bitacora('usuario', $model->id_usuario, 'U', 'Actualización de la imagen de un usuario');
                return [
                    'mensaje' => '<div class="alert alert-success text-center">' .
                        '<p>Se ha actualizado satisfactoriamente la imagen de perfil</p>' .
                        '</div>',
                    'success' => true
                ];
            } else {
                return [
                    'mensaje' => '<div class="alert alert-danger text-center">' .
                        '<p>No se ha podido actualizar la imagen de perfil</p>' .
                        '</div>',
                    'success' => false
                ];
            }
        } catch (\Exception $e) {
            return [
                'mensaje' => '<div class="alert alert-danger text-center">' .
                    '<p>¡Ha ocurrido un problema al guardar la imagen en el sistema!</p>' .
                    $e->getMessage() .
                    '</div>',
                'success' => false
            ];
        }
    }

    public function get_usuario_json(Request $request)
    {
        $user = Usuario::find($request->id_usuario);
        return [
            'user' => $user
        ];
    }

    public function update_password(Request $request)
    {
        $msg = '';
        $success = true;

        $valida = Validator::make($request->all(), [
            'passw' => 'required',
            'id_usuario' => 'required',
        ], [
            'passw.required' => 'La contraseña es obligatoria',
            'id_usuario.required' => 'El usuario es obligatoria',
        ]);
        if (!$valida->fails()) {
            $model = Usuario::find($request->id_usuario);

            $pwd1 = $this->decrypt(Session::get('key_privada'), $request->passw);

            $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/";
            if (trim($pwd1) != '' && preg_match($patron, $pwd1)) {
                $pwd1 = Hash::make($pwd1);
                $model->password = $pwd1;

                if ($model->save()) {
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado la nueva contraseña satisfactoriamente</p>'
                        . '</div>';
                    bitacora('usuario', $model->id_usuario, 'U', 'Actualización satisfactoria de la contraseña de un usuario');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }

            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Contraseña mal estructurada. (Mínimo 6 caracteres, incluyendo 1 dígito, 1 letra minúscula y 1 letra mayúscula)</p>'
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
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function exportar_usuarios(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja_usuarios($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte usuarios.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function excel_hoja_usuarios($objPHPExcel, $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = DB::table('usuario as u')
            ->join('rol as r', 'r.id_rol', '=', 'u.id_rol')
            ->select('u.*', 'r.nombre as rol');

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($mi_busqueda_toupper, $mi_busqueda_tolower) {
            $q->Where('u.nombre_completo', 'like', '%' . $mi_busqueda_toupper . '%')
                ->orWhere('u.correo', 'like', '%' . $mi_busqueda_tolower . '%')
                ->orWhere('u.username', 'like', '%' . $mi_busqueda_toupper . '%')
                ->orWhere('r.nombre', 'like', '%' . $mi_busqueda_toupper . '%');
        });

        $listado = $listado->orderBy('u.nombre_completo', 'asc')->orderBy('r.nombre', 'asc')
            ->get();

        if (count($listado) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Usuarios');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:D1');
            $objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:D1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Listado de usuarios');

            $objSheet->getCell('A3')->setValue('Nombre completo');
            $objSheet->getCell('B3')->setValue('Nombre de usuario');
            $objSheet->getCell('C3')->setValue('Correo');
            $objSheet->getCell('D3')->setValue('Rol');

            $objSheet->getStyle('A3:D3')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A3:D3')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A3:D3')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            //--------------------------- LLENAR LA TABLA ---------------------------------------------
            for ($i = 0; $i < sizeof($listado); $i++) {
                $objSheet->getCell('A' . ($i + 4))->setValue($listado[$i]->nombre_completo);
                $objSheet->getCell('B' . ($i + 4))->setValue($listado[$i]->username);
                $objSheet->getCell('C' . ($i + 4))->setValue($listado[$i]->correo);
                $objSheet->getCell('D' . ($i + 4))->setValue(getUsuario($listado[$i]->id_usuario)->rol()->nombre);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
        } else {
            return '<div>No se han encontrado coincidencias para exportar</div>';
        }
    }
}