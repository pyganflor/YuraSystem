<?php

namespace yura\Http\Controllers;

use Greggilbert\Recaptcha\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use phpseclib\Crypt\RSA;
use yura\Modelos\Ciclo;
use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\ConfiguracionUser;
use yura\Modelos\Cosecha;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\Pedido;
use yura\Modelos\Rol;
use yura\Modelos\StockApertura;
use yura\Modelos\Submenu;
use yura\Modelos\Usuario;
use Validator;
use Storage as Almacenamiento;

class YuraController extends Controller
{
    public function inicio(Request $request)
    {
        //dd(opDiasFecha('+', 120, '2018-12-26'));
        if (count(getUsuario(Session::get('id_usuario'))->rol()->getSubmenusByTipo('C')) > 0) {
            /* ========= CALIBRE ========= */
            $labels = DB::table('clasificacion_verde as v')
                ->select('v.fecha_ingreso as dia')->distinct()
                ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 7, date('Y-m-d')))
                ->where('v.fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')))
                ->get();
            $calibre = 0;
            $tallos = 0;
            $ramos = 0;
            $cant_verde = 0;
            foreach ($labels as $dia) {
                $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                if ($verde != '') {
                    $ped_ramos_estandar = $verde->getTotalRamosEstandar();
                    $calibre += $ped_ramos_estandar > 0 ? round($verde->total_tallos() / $ped_ramos_estandar, 2) : 0;
                    $tallos += $verde->total_tallos();
                    $ramos += $ped_ramos_estandar;
                    $cant_verde++;
                }
            }
            $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;

            /* ======== PRECIO x RAMO ======== */
            $pedidos_semanal = Pedido::All()->where('estado', 1)
                ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
                ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')));
            $valor = 0;
            $cajas = 0;
            foreach ($pedidos_semanal as $p) {
                $valor += $p->getPrecio();
                $cajas += $p->getCajas();
            }
            $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
            $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;

            /* ========= RENDIMIENTO DESECHO =========== */
            $fechas = [];
            for ($i = 1; $i <= 7; $i++) {
                array_push($fechas, opDiasFecha('-', $i, date('Y-m-d')));
            }

            $r_ver = 0;
            $r_ver_r = 0;
            $d_ver = 0;
            $count_ver = 0;
            $r_bla = 0;
            $d_bla = 0;
            $count_bla = 0;
            foreach ($fechas as $f) {
                $verde = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                $blanco = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

                if ($verde != '') {
                    $r_ver += $verde->getRendimiento();
                    $r_ver_r += $verde->getRendimientoRamos();
                    $d_ver += $verde->desecho();
                    $count_ver++;
                }
                if ($blanco != '') {
                    $r_bla += $blanco->getRendimiento();
                    $d_bla += $blanco->getDesecho();
                    $count_bla++;
                }
            }

            $rendimiento_desecho = [
                'verde' => [
                    'rendimiento' => $count_ver > 0 ? round($r_ver / $count_ver, 2) : 0,
                    'rendimiento_ramos' => $count_ver > 0 ? round($r_ver_r / $count_ver, 2) : 0,
                    'desecho' => $count_ver > 0 ? round($d_ver / $count_ver, 2) : 0
                ],
                'blanco' => [
                    'rendimiento' => $count_bla > 0 ? round($r_bla / $count_bla, 2) : 0,
                    'desecho' => $count_bla > 0 ? round($d_bla / $count_bla, 2) : 0
                ]
            ];

            /* ============ AREA =========== */
            $desde = opDiasFecha('-', 28, date('Y-m-d'));
            $hasta = opDiasFecha('-', 7, date('Y-m-d'));

            $semanas_4 = DB::table('semana as s')
                ->select('s.codigo as semana')->distinct()
                ->Where(function ($q) use ($desde, $hasta) {
                    $q->where('s.fecha_inicial', '>=', $desde)
                        ->where('s.fecha_inicial', '<=', $hasta);
                })
                ->orWhere(function ($q) use ($desde, $hasta) {
                    $q->where('s.fecha_final', '>=', $desde)
                        ->Where('s.fecha_final', '<=', $hasta);
                })
                ->orderBy('codigo')
                ->get();

            $area = 0;
            $data_4semanas = getAreaCiclosByRango($semanas_4[0]->semana, $semanas_4[3]->semana, 'T');

            foreach ($data_4semanas['variedades'] as $var) {
                foreach ($var['ciclos'] as $c) {
                    foreach ($c['areas'] as $a) {
                        $area += $a;
                    }
                }
            }

            $data_ciclos = getCiclosCerradosByRango($semanas_4[0]->semana, $semanas_4[3]->semana, 'T');
            $ciclo = $data_ciclos['ciclo'];
            $area_cerrada = $data_ciclos['area_cerrada'];
            $tallos_ciclo = $data_ciclos['tallos_cosechados'];

            $data_cosecha = getCosechaByRango($semanas_4[0]->semana, $semanas_4[3]->semana, 'T');
            $calibre_ciclo = $data_cosecha['calibre'];
            $calibre_ciclo > 0 ? $ramos_ciclo = round($tallos_ciclo / $calibre_ciclo, 2) : $ramos_ciclo = 0;

            $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

            $mensual = [
                'ciclo_ano' => $ciclo_ano,
                'area' => round($area / count($semanas_4), 2),
                'ciclo' => $ciclo,
                'area_cerrada' => $area_cerrada,
                'tallos' => $area_cerrada > 0 ? round($tallos_ciclo / $area_cerrada, 2) : 0,
                'ramos' => $area_cerrada > 0 ? round($ramos_ciclo / $area_cerrada, 2) : 0,
                'ramos_anno' => $area_cerrada > 0 ? round($ciclo_ano * round($ramos_ciclo / $area_cerrada, 2), 2) : 0,
            ];

            /* ================= venta/m2/año en 4 meses =================== */
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('-4 month'));

            $data_venta_mensual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $data_venta_mensual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }

            /* ================= venta en 1 año =================== */
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('last year'));

            $data_venta_anual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $data_venta_anual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }
            $semana_desde = getSemanaByDate(opDiasFecha('-', 91, date('Y-m-d')));
            $semana_hasta = getSemanaByDate(date('Y-m-d'));
            $data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, 'T');
            $data_area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']);

            return view('adminlte.inicio', [
                'calibre' => $calibre,
                'tallos' => $tallos,
                'precio_x_ramo' => $precio_x_ramo,
                'valor' => $valor,
                'rendimiento_desecho' => $rendimiento_desecho,
                'area' => $mensual,
                'venta_mensual' => $data_venta_mensual,
                'venta_anual' => $data_venta_anual,
                'area_anual' => $data_area_anual,
            ]);
        }

        return view('adminlte.inicio');
    }

    public function login(Request $request)
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_RAW);
        $k = $rsa->createKey();

        Session::put('key_publica', $k['publickey']);
        Session::put('key_privada', $k['privatekey']);

        if (!$request->session()->has('logeado')) { // Si no tiene variable logeado la session? logeado = false;
            Session::put('logeado', false);
        };

        if (!$request->session()->get('logeado')) { // Si no está logeado

            $rsa->loadKey(Session::get('key_privada'));
            $raw = $rsa->getPublicKey(RSA::PUBLIC_FORMAT_RAW);

            return view('login.login', [
                'key' => $raw['n']->toHex(),
            ]);
        };

        return redirect('/');   // Si está logeado redirect a inicio
    }

    public function verificaUsuario(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'username' => 'required|max:250',
            'h_clave' => 'required',
            'g-recaptcha-response' => 'required|captcha'
            //'captcha' => 'required|captcha',
        ], [
            'username.max' => 'El nombre de usuario es muy grande',
            'username.required' => 'El nombre de usuario es obligatorio',
            'h_clave.required' => 'La contraseña es obligatoria',
            'g-recaptcha-response.required' => 'Haga clic en el captcha de seguridad y espere a que verifique que noe s un robot',
            'g-recaptcha-response.captcha' => 'El código de verificación es incorrecto',
            //'captcha.required' => 'El código de verificación es obligatorio',
            //'captcha.captcha' => 'El código de verificación es incorrecto',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {
            $correo = '' . espacios(strtolower($request->usuario));
            $clave = $this->decrypt(Session::get('key_privada'), $request->h_clave);
            $datos = Usuario::Where('username', '=', $request->username)->get();
            $err_usr = true;
            $err_pss = true;

            if (isset($datos[0])) {
                $err_usr = false;
                $usuarios = $datos[0];
                if ($usuarios->estado == 'A') {
                    if (Hash::check($clave, $usuarios->password)) {

                        if ($usuarios->configuracion == '') {
                            $configuracion = new ConfiguracionUser();
                            $configuracion->id_usuario = $usuarios->id_usuario;
                            $configuracion->save();
                            $configuracion = ConfiguracionUser::All()->last();
                            bitacora('configuracion_user', $configuracion->id_configuracion_user, 'I', 'Creación satisfactoria de una nueva configuracion de usuario');
                        }

                        $err_pss = false;
                        Session::put('logeado', true);
                        Session::put('last_quest', date('Y-m-d H:i:s'));

                        Session::put('id_usuario', $usuarios->id_usuario);

                        bitacora('usuario', $datos[0]->id_usuario, 'L', 'Inicio de sesión satisfactorio. Usuario:' . $datos[0]->nombre_completo);

                    }
                } else {
                    $err_usr = false;
                    $err_pss = false;
                    $msg = '<div class="alert alert-danger text-center">Su usario ha sido desactivado. 
                            Póngase en contacto con el administrador al correo <strong>' . env('MAIL_ADMIN') . '</strong></div>';
                    $success = false;
                }
            }
            if ($err_usr) {
                $msg = 'Fallo de inicio de sesión con el usuario . Error de contraseña o de usuario';
                $success = false;
                bitacora('usuario', -1, 'E', 'Fallo de inicio de sesión con el usuario ' . $correo . '. No existe el usuario en el sistema');
            } elseif ($err_pss) {
                $msg = 'Fallo de inicio de sesión con el usuario . Error de contraseña o de usuario';
                $success = false;
                bitacora('usuario', $datos[0]->id_usuario, 'E', 'Fallo de inicio de sesión con el usuario ' . $correo . '. Contraseña incorrecta');
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

    public function logout(Request $request)
    {
        if (!$request->session()->has('logeado')) {
            Session::put('logeado', false);
        };
        if (Session::has('id_usuario')) bitacora('usuario', Session::get('id_usuario'), 'C', 'Cerrado de session satisfactorio');
        Session::put('logeado', false);
        Session::flush();
        DB::disconnect();
        return redirect('');
    }

    public function save_config_user(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'fixed_layout' => 'required|',
            'boxed_layout' => 'required',
            'color_config' => 'required|',
            'config_online' => 'required|',
            'skin' => 'required|max:25',
        ], [
            'skin.max' => 'El tema es muy grande',
            'config_online.required' => 'Visibilidad online es obligatorio',
            'fixed_layout.required' => 'Diseño compacto es obligatorio',
            'boxed_layout.required' => 'Diseño en caja es obligatorio',
            'color_config.required' => 'El color del panel de control es obligatorio',
            'skin.captcha' => 'El tema es obligatorio',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {
            $config = ConfiguracionUser::find(getUsuario(Session::get('id_usuario'))->configuracion->id_configuracion_user);
            $config->fixed_layout = $request->fixed_layout == 'true' ? 'S' : 'N';
            $config->boxed_layout = $request->boxed_layout == 'true' ? 'S' : 'N';
            $config->toggle_color_config = $request->color_config == 'true' ? 'S' : 'N';
            $config->config_online = $request->config_online == 'true' ? 'S' : 'N';
            $config->skin = $request->skin;
            if ($config->save()) {
                $msg = '<div class="alert alert-success text-center">Se ha guardado satisfactoriamente la configuración</div>';

                bitacora('configuracion_user', $config->id_configuracion_user, 'U', 'Actualización satisfactoria de la configuración');
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la configuración en el sistema</div>';
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

    public function perfil(Request $request)
    {
        $rsa = new RSA();

        $rsa->loadKey(Session::get('key_privada'));
        $raw = $rsa->getPublicKey(RSA::PUBLIC_FORMAT_RAW);

        return view('perfil.inicio', [
            'usuario' => Usuario::find(Session::get('id_usuario')),
            'key' => $raw['n']->toHex(),
            'roles' => Rol::All(),
        ]);
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
                    '<p>¡Ha ocurrido un problema al guardar la imagen en el sistema! **</p>' .
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
            'passw_current' => 'required',
            'id_usuario' => 'required',
        ], [
            'passw_current.required' => 'La contraseña actual es obligatoria',
            'passw.required' => 'La contraseña es obligatoria',
            'id_usuario.required' => 'El usuario es obligatoria',
        ]);
        if (!$valida->fails()) {
            $model = Usuario::find($request->id_usuario);

            $pwd = $this->decrypt(Session::get('key_privada'), $request->passw_current);

            if (Hash::check($pwd, $model->password)) {
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
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Contraseña incorrecta</p>'
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

    public function buscar_saldos(Request $request)
    {
        $arreglo = [];
        $antes = $request->antes != '' ? $request->antes : 3;
        $despues = $request->despues != '' ? $request->despues : 3;
        if ($request->fecha >= date('Y-m-d')) {
            for ($i = 1; $i <= $antes; $i++) {
                $fecha = opDiasFecha('-', $i, $request->fecha);
                array_push($arreglo, $fecha);
            }
            array_push($arreglo, $request->fecha);
            for ($i = 1; $i <= $despues; $i++) {
                $fecha = opDiasFecha('+', $i, $request->fecha);
                array_push($arreglo, $fecha);
            }
        }
        $arreglo = array_sort($arreglo);
        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.saldos', [
            'fechas' => $arreglo,
            'fecha' => $request->fecha,
            'antes' => $antes,
            'despues' => $despues,
        ]);
    }

    public function select_planta(Request $request)
    {
        return getVariedadesByPlanta($request->planta);
    }
}
