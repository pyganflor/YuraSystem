<?php

use Illuminate\Support\Facades\Session;
use yura\Modelos\Usuario;
use yura\Modelos\Bitacora;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Rol_Submenu;
use yura\Modelos\Submenu;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Recepcion;
use yura\Modelos\ConfiguracionEmpresa;

/*
 * -------- BITÁCORA DE LAS ACCIONES ECHAS POR EL USUARIO ------
 * INSERTAR (I)
 * ACTUALIZAR (U)
 * BORRAR (D)
 * CANCELAR/DESHABILITAR (R)
 * INICIO DE SESIÓN EN EL SISTEMA (L)
 * CERRAR SESIÓN (C)
 * ERROR DE INICIO DE SESIÓN (E)
 *
 */
function bitacora($tabla, $id, $accion = 'U', $observaciones = '')
{
    $ok = true;
    try {
        $bitacora = new Bitacora();
        $bitacora->tabla = str_limit(strtoupper($tabla), 80);
        $bitacora->codigo = $id;
        $bitacora->accion = str_limit(strtoupper($accion), 1);
        $bitacora->id_usuario = (Session::has('id_usuario') ? Session::get('id_usuario') : -1);
        $bitacora->observacion = str_limit(mb_strtoupper($observaciones), 180);
        $bitacora->ip = \Request::ip();
        $bitacora->save();
    } catch (\Exception $e) {
        $ok = false;
    }
    return $ok;
}

/*
 * ------------ FUNCIONES ÚTILES PARA FECHA Y HORA -------------------
 */
define('FR_CONSULTA', 0);
define('FR_ARREGLO', 1);
define('FR_JSON', 2);
define('FR_OPTION', 3);
define('FR_COMAS', 3);

define('TP_COMPLETO', 0);
define('TP_ABREVIADO', 1);
define('TP_MUY_ABREVIADO', 2);
define('TP_LETRA', 3);

define('DIAS_SEMANA', serialize(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']));
define('DIAS_SEMANA_ABREVIADOS', serialize(['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sáb', 'Dom']));
define('DIAS_SEMANA_MUY_ABREVIADOS', serialize(['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do']));
define('DIAS_SEMANA_LETRA', serialize(['L', 'M', 'M', 'J', 'V', 'S', 'D']));
define('MESES', serialize(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']));
define('MESES_ABREVIADOS', serialize(['En', 'Febr', 'Mzo', 'Abr', 'My', 'Jun', 'Jul', 'Ag', 'Sept', 'Oct', 'Nov', 'Dic']));
define('MESES_MUY_ABREVIADOS', serialize(['En', 'Fb', 'Mz', 'Ab', 'My', 'Jn', 'Jl', 'Ag', 'Sp', 'Oc', 'Nv', 'Dc']));
define('MESES_LETRA', serialize(['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D']));
define('A_Z', serialize(range('A', 'Z')));


function getMeses($tipo = TP_COMPLETO, $formato = FR_ARREGLO)
{  //Devuelve los meses del año o como arreglo o como cadena para formar arreglo
    $resultado = '';
    switch ($tipo) {
        case TP_ABREVIADO:
            $valor = unserialize(MESES_ABREVIADOS);
            break;

        case TP_MUY_ABREVIADO:
            $valor = unserialize(MESES_MUY_ABREVIADOS);
            break;

        case TP_LETRA:
            $valor = unserialize(MESES_LETRA);
            break;
        default:
            $valor = unserialize(MESES);
            break;
    }
    switch ($formato) {
        case FR_COMAS:
            foreach ($valor as $item) {
                $resultado .= (($resultado == '') ? '\'' . $item : ',' . '\'' . $item) . '\'';
            }
            $resultado = '[' . $resultado . ']';
            break;

        default:
            $resultado = $valor;
            break;
    }

    return $resultado;
}

function getDias($tipo = TP_COMPLETO, $formato = FR_ARREGLO, $primeroDomingo = false)
{ //Devuelve los días de la semanas o como arreglo o como cadena para formar arreglo
    $resultado = '';
    switch ($tipo) {
        case TP_ABREVIADO:
            $valor = unserialize(DIAS_SEMANA_ABREVIADOS);
            break;

        case TP_MUY_ABREVIADO:
            $valor = unserialize(DIAS_SEMANA_MUY_ABREVIADOS);
            break;

        case TP_LETRA:
            $valor = unserialize(DIAS_SEMANA_LETRA);
            break;
        default:
            $valor = unserialize(DIAS_SEMANA);
            break;
    }
    if ($primeroDomingo) {
        $valor = array_merge([array_last($valor)], $valor);
        array_pop($valor);
    }
    switch ($formato) {
        case FR_COMAS:
            foreach ($valor as $item) {
                $resultado .= (($resultado == '') ? '\'' . $item : ',' . '\'' . $item) . '\'';
            }
            $resultado = '[' . $resultado . ']';
            break;

        default:
            $resultado = $valor;
            break;
    }

    return $resultado;
}

function semanasAno($year) //---- Devuelve la cantidad de semanas que tiene el año dado
{
    $date = new \DateTime;
    $date->setISODate($year, 53);
    return ($date->format("W") === "53" ? 53 : 52);
}

function primeraSemanaMes($mes, $ano) //---- Devuelve el número de la primera semanas del año y el mes dado
{
    return (int)date('W', strtotime($ano . '-' . $mes . '-1'));
}

function ultimaSemanaMes($mes, $ano) //---- Devuelve el número de la última semanas del año y el mes dado
{
    return (int)date('W', strtotime(date('Y-m-t', strtotime($ano . '-' . $mes . '-1'))));
}

function mesSemana($semana, $ano) //---- Devuelve el número del mes de la semanas y el año dado
{
    return (int)date("n", strtotime('+ ' . $semana . ' weeks', mktime(0, 0, 0, 1, 1, $ano, -1)));
}

function primerDiaSemana($fecha)
{

    $diaInicio = "Monday";

    $strFecha = strtotime((is_string($fecha)) ? $fecha : date('Y-m-d H:i', $fecha));
    if (date('w', $strFecha) == 1) {
        $strFecha = strtotime(date('Y-m-d H:i', strtotime(date('Y-m-d H:i', $strFecha) . ' +1 day')));
    }
    $fechaInicio = date('Y-m-d 0:0', strtotime('last ' . $diaInicio, $strFecha));
    /*
        if (date("l", $strFecha) == $diaInicio) {
            $fechaInicio = date("Y-m-d 0:0", $strFecha);
        } */

    return strtotime($fechaInicio);
}

function ultimoDiaSemana($fecha)
{

    $diaFin = "Sunday";

    $strFecha = strtotime((is_string($fecha)) ? $fecha : date('Y-m-d H:i', $fecha));

    $fechaFin = date('Y-m-d 23:59', strtotime('next ' . $diaFin, $strFecha));


    if (date("l", $strFecha) == $diaFin) {
        $fechaFin = date("Y-m-d 23:59", $strFecha);
    }
    return strtotime($fechaFin);
}

function edad($fecha_nacimiento, $meses = false)
{ //Calcula la edad de la persona dada la fecha de nacimiento
    $edad = '-';
    if (!empty($fecha_nacimiento)) {
        $strFecha = is_string($fecha_nacimiento) ? $fecha_nacimiento : date('Y-m-d H:i', $fecha_nacimiento);
        $cumpleanos = new \DateTime($strFecha);
        $hoy = new \DateTime();
        $t_transcurrido = $hoy->diff($cumpleanos);
        $c_agnos = $t_transcurrido->y;
        $c_meses = $t_transcurrido->m;
        $edad = '';
        if ($meses) {
            if ($c_agnos > 0) $edad .= $c_agnos . ' año' . ($c_agnos == 1 ? ' ' : 's ');
            if ($c_meses > 0) $edad .= $c_meses . ' mes' . ($c_meses == 1 ? ' ' : 'es ');
            if ($edad == '') $edad = '-';
        } else {
            $edad = $c_agnos . ' año' . ($c_agnos == 1 ? ' ' : 's ');
        }
    }

    return $edad;

}

function bussiness_days($begin_date, $end_date, $type = 'array') //---- Devuelve un arreglo bidimensional con los meses y dias laborables en un rango de fechas
{
    $date_1 = date_create($begin_date);
    $date_2 = date_create($end_date);
    if ($date_1 > $date_2) return FALSE;
    $bussiness_days = array();
    while ($date_1 <= $date_2) {
        $day_week = $date_1->format('w');
        if ($day_week > 0 && $day_week < 6) {
            $bussiness_days[$date_1->format('Y-m')][] = $date_1->format('d');
        }
        date_add($date_1, date_interval_create_from_date_string('1 day'));
    }
    if (strtolower($type) === 'sum') {
        array_map(function ($k) use (&$bussiness_days) {
            $bussiness_days[$k] = count($bussiness_days[$k]);
        }, array_keys($bussiness_days));
    }
    return $bussiness_days;
}

//---------------------------------------------------------------------------------------

/*
 * ------------------ FUNCIONES ÚTILES PARA STRINGS ------------------
 */

function espacios($texto) //---- Elimina espacios de los extremos de la cadena y deja un solo espacio entre palabras
{
    $txt = trim($texto);
    $arr_variantes = ['  ' => ' ', ' ,' => ',', ' .' => '.', ' :' => ':', ' ?' => '?', '¿ ' => '¿', ' !' => '!', '¡ ' => '¡',
        '( ' => '(', ' )' => ')', '[ ' => '[', ' ]' => ']', '{ ' => '{', ' }' => '}', '" ' => '"', ' "' => '"'];
    foreach ($arr_variantes as $variante => $substituto) {
        while ($txt != str_replace($variante, $substituto, $txt)) $txt = str_replace($variante, $substituto, $txt);
    }

    return $txt;
}

function substituir_caracteres_raros($cadena_original, $es_nombre = false)
{ // Subtituye caracteres especiales por su equivalente
    $arr_raros = ['ﾑ' => 'Ñ', 'ﾓ' => 'Ó', 'ﾚ' => 'Ú', 'ﾉ' => 'É', 'ﾁ' => 'Á', 'ﾍ' => 'Í', 'ﾌ' => 'I', 'ｴ' => ''];
    $arr_nombre = ['|', '(', ')', '[', ']', '{', '}', '+', '*', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        '/', '\\', '@', '&', '%', '<', '>', '^', '#', '$', '=', '¿', '?', '¡', '!', ':'];
    $cadena_resultante = $cadena_original;
    foreach ($arr_raros as $raro => $substituto) {
        $cadena_resultante = str_replace($raro, $substituto, $cadena_resultante);
    };
    if ($es_nombre) {
        foreach ($arr_nombre as $caracter) {
            $cadena_resultante = str_replace($caracter, '', $cadena_resultante);
        }
    };
    return $cadena_resultante;
}


//---------------------------------------------------------------------------------------

/*
 * ----------------- FUNCIONES PARA OBTENER REGIONES DEL ECUADOR -----------------------------

function getProvincias($formato = FR_CONSULTA, $selecionada = -1) //Obtiene el listado de Provincias en diferentes formatos
{
    $provincias = Provincia::where('prov_estado', 'A')->where('prov_codigo', '>', 0)->orderBy('prov_descripcion')->get();
    switch ($formato) {
        case FR_CONSULTA:
            $listado = $provincias;
            break;

        case FR_ARREGLO:
            $listado = [];
            foreach ($provincias as $provincia) {
                $listado[] = [$provincia->prov_codigo, $provincia->prov_descripcion];
            }
            break;

        case FR_JSON:
            $listado_tmp = [];
            foreach ($provincias as $provincia) {
                $listado_tmp[] = ['id' => $provincia->prov_codigo, 'descripcion' => $provincia->prov_descripcion];
            }
            $listado = json_encode($listado_tmp);
            break;

        case FR_OPTION:
            $listado = (count($provincias) > 0) ? '<option value="">Seleccione</option>' : '';
            foreach ($provincias as $provincia) {
                $listado .= '<option value=' . $provincia->prov_codigo . ($provincia->prov_codigo == $selecionada ? ' selected' : '') . '>' .
                    $provincia->prov_descripcion . '</option>';
            }
            break;

        default:
            $listado = $provincias;
    }

    return $listado;
}

function getCiudades($provincia, $formato = FR_CONSULTA, $selecionada = -1) //Obtiene el listado de Ciudades de una provincia en diferentes formatos
{
    $ciudades = Ciudad::where('prov_codigo', $provincia)
        ->where('ciud_codigo', '>', 0)->orderBy('ciud_descripcion')->get();
    switch ($formato) {
        case FR_CONSULTA:
            $listado = $ciudades;
            break;

        case FR_ARREGLO:
            $listado = [];
            foreach ($ciudades as $ciudad) {
                $listado[] = [$ciudad->ciud_codigo, $ciudad->ciud_descripcion];
            }
            break;

        case FR_JSON:
            $listado_tmp = [];
            foreach ($ciudades as $ciudad) {
                $listado_tmp[] = ['id' => $ciudad->ciud_codigo, 'descripcion' => $ciudad->ciud_descripcion];
            }
            $listado = json_encode($listado_tmp);
            break;

        case FR_OPTION:
            $listado = (count($ciudades) > 0) ? '<option value="">Seleccione</option>' : '';
            foreach ($ciudades as $ciudad) {
                $listado .= '<option value=' . $ciudad->ciud_codigo . ($ciudad->ciud_codigo == $selecionada ? ' selected' : '') . '>' .
                    $ciudad->ciud_descripcion . '</option>';
            }
            break;

        default:
            $listado = $ciudades;
    }

    return $listado;
}

function getSectores($ciudad, $formato = FR_CONSULTA, $selecionada = -1) //Obtiene el listado de Sectores de una ciudad en diferentes formatos
{
    $sectores = Sector::where('ciud_codigo', $ciudad)
        ->where('secu_secuencial', '>', 0)->orderBy('secu_descripcion')->get();
    switch ($formato) {
        case FR_CONSULTA:
            $listado = $sectores;
            break;

        case FR_ARREGLO:
            $listado = [];
            foreach ($sectores as $sector) {
                $listado[] = [$sector->secu_secuencial, $sector->secu_descripcion];
            }
            break;

        case FR_JSON:
            $listado_tmp = [];
            foreach ($sectores as $sector) {
                $listado_tmp[] = ['id' => $sector->secu_secuencial, 'descripcion' => $sector->secu_descripcion];
            }
            $listado = json_encode($listado_tmp);
            break;

        case FR_OPTION:
            $listado = (count($sectores) > 0) ? '<option value="">Seleccione</option>' : '';
            foreach ($sectores as $sector) {
                $listado .= '<option value=' . $sector->secu_secuencial . ($sector->secu_secuencial == $selecionada ? ' selected' : '') . '>' .
                    $sector->secu_descripcion . '</option>';
            }
            break;

        default:
            $listado = $sectores;
    }

    return $listado;
}

function getParroquias($ciudad, $formato = FR_CONSULTA, $selecionada = -1)  //Obtiene el listado de Parroquias de una ciudad en diferentes formatos
{
    $parroquias = Parroquia::where('ciud_codigo', $ciudad)
        ->where('parr_secuencial', '>', 0)->orderBy('parr_descripcion')->get();
    switch ($formato) {
        case FR_CONSULTA:
            $listado = $parroquias;
            break;

        case FR_ARREGLO:
            $listado = [];
            foreach ($parroquias as $parroquia) {
                $listado[] = [$parroquia->parr_secuencial, $parroquia->parr_descripcion];
            }
            break;

        case FR_JSON:
            $listado_tmp = [];
            foreach ($parroquias as $parroquia) {
                $listado_tmp[] = ['id' => $parroquia->parr_secuencial, 'descripcion' => $parroquia->parr_descripcion];
            }
            $listado = json_encode($listado_tmp);
            break;

        case FR_OPTION:
            $listado = (count($parroquias) > 0) ? '<option value="">Seleccione</option>' : '';
            foreach ($parroquias as $parroquia) {
                $listado .= '<option value=' . $parroquia->parr_secuencial . ($parroquia->parr_secuencial == $selecionada ? ' selected' : '') . '>' .
                    $parroquia->parr_descripcion . '</option>';
            }
            break;

        default:
            $listado = $parroquias;
    }

    return $listado;
}
*/
//---------------------------------------------------------------------------------------

/*
 * --------------------FUNCIONES PARA OBTENER LOS DATOS DE PROVINCIAS, CIUDADES, SECTORES Y PARROQUIAS ------------------------------


function getProvincia($id = -1)
{
    if ($id != -1)
        return Provincia::find($id);
    return false;
}

function getCiudad($id)
{
    if ($id != -1)
        return Ciudad::find($id);
    return false;
}

function getSector($id)
{
    if ($id != -1)
        return Sector::find($id);
    return false;
}

function getParroquia($id)
{
    if ($id != -1)
        return Parroquia::find($id);
    return false;
}

// Verificar si el usuario tiene permiso
function verifica_permiso($arr_permisos_verificar)
{
    $arr_permisos = str_split(Session::get('tipo_usuario'));
    $permiso = in_array('S', $arr_permisos);
    $i = 0;
    while (!$permiso && $i < count($arr_permisos)) {
        $permiso = in_array($arr_permisos[$i], $arr_permisos_verificar);
        $i++;
    }
    return $permiso;
}
*/
//--------------------------------------------------------------------------------------------------------

// Obtener usuario
function getUsuario($usuario)
{
    $r = Usuario::find($usuario);
    return $r;
}

function getGrupoMenusOfUser($usuario)
{
    $grupos = DB::table('grupo_menu as g')
        ->join('menu as m', 'g.id_grupo_menu', '=', 'm.id_grupo_menu')
        ->join('submenu as s', 'm.id_menu', '=', 's.id_menu')
        ->join('rol_submenu as rs', 's.id_submenu', '=', 'rs.id_submenu')
        ->select('g.id_grupo_menu')->distinct()
        ->where('rs.id_rol', '=', getUsuario(Session::get('id_usuario'))->id_rol)
        ->where('g.estado', '=', 'A')
        ->orderBy('g.nombre')->get();
    $g = [];
    foreach ($grupos as $item) {
        $g[] = GrupoMenu::find($item->id_grupo_menu);
    }
    return $g;
}

function isActive_action($s)
{
    $s = Rol_Submenu::All()->where('id_rol', '=', getUsuario(Session::get('id_usuario'))->id_rol)
        ->where('id_submenu', '=', Submenu::find($s)->id_submenu)->first();
    if ($s != '')
        if ($s->estado == 'A')
            return true;
    return false;
}

// Funciones para detectar navegador, version del navegador y sistema operativo
function detect()
{
    $browser = array("IE", "OPERA", "MOZILLA", "NETSCAPE", "FIREFOX", "SAFARI", "CHROME");
    $os = array("WIN", "MAC", "LINUX", "ANDROID", "IPHONE OS");

    # definimos unos valores por defecto para el navegador y el sistema operativo
    $info['browser'] = "OTHER";
    $info['os'] = "OTHER";

    # buscamos el navegador con su sistema operativo
    foreach ($browser as $parent) {
        $s = strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $parent);
        $f = $s + strlen($parent);
        $version = substr($_SERVER['HTTP_USER_AGENT'], $f, 15);
        $version = preg_replace('/[^0-9,.]/', '', $version);
        if ($s) {
            $info['browser'] = $parent;
            $info['version'] = $version;
        }
    }

    # obtenemos el sistema operativo
    foreach ($os as $val) {
        if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $val) !== false)
            $info['os'] = $val;
    }

    # devolvemos el array de valores
    return $info;
}

function isPC()
{
    return !(detect()['os'] == 'ANDROID' || detect()['os'] == 'IPHONE OS');
}

/* ================ LA FUNCION date('w', fecha) DE PHP DEVUELVE 0=>DOMINGO, 6=>SABADO =====================*/
function transformDiaPhp($dia)
{
    if ($dia == 0)
        return 6;
    else if ($dia == 1)
        return 0;
    else if ($dia == 2)
        return 1;
    else if ($dia == 3)
        return 2;
    else if ($dia == 4)
        return 3;
    else if ($dia == 5)
        return 4;
    else
        return 5;
}

/* ================ FUNCION PARA SABER SI UNA FECHA YA SE ENCUENTRA REGISTRADA EN UNA SEMANA ======================*/
function existInSemana($fecha, $variedad, $anno)
{
    $r = DB::table('semana')->select('*')
        ->where('id_variedad', '=', $variedad)
        ->where('anno', '=', $anno)
        ->where('fecha_inicial', '>=', $fecha)
        ->where('fecha_final', '<=', $fecha)
        ->get();
    if (count($r) != 0)
        return false;
    return true;
}

/* =================== FUNCIONES DE YURA ================*/
function getRecepcion($id)
{
    return Recepcion::find($id);
}

function getConfiguracionEmpresa()
{
    return ConfiguracionEmpresa::All()->where('estado', '=', 1)->first();
}
