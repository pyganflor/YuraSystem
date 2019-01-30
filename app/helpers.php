<?php

use Illuminate\Support\Facades\Session;
use yura\Modelos\Usuario;
use yura\Modelos\Bitacora;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Rol_Submenu;
use yura\Modelos\Submenu;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Recepcion;
use yura\Modelos\Pedido;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\Documento;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Variedad;
use yura\Modelos\DetalleEmpaque;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\ClasificacionUnitaria;
use yura\Modelos\StockApertura;
use yura\Modelos\Semana;
use yura\Modelos\LoteRE;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Consumo;
use yura\Modelos\Grosor;
use yura\Modelos\Empaque;
use yura\Modelos\Envio;
use yura\Modelos\Especificacion;
use yura\Modelos\DetalleEspecificacionEmpaque;
use Illuminate\Pagination\LengthAwarePaginator;
use Request as Resq;
use yura\Modelos\InventarioFrio;

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
define('MESES_ABREVIADOS', serialize(['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic']));
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

function convertDatetimeToText($fecha)
{
    $dia = substr($fecha, 8, 2);
    $mes = substr($fecha, 5, 2);
    $anno = substr($fecha, 0, 4);
    $hora = substr($fecha, 11, 5);

    return $dia . ' de ' . getMeses(TP_COMPLETO, FR_ARREGLO)[intval($mes - 1)] . ' del ' . $anno . ' a las ' . $hora;
}

function convertDateToText($fecha)
{
    $dia = substr($fecha, 8, 2);
    $mes = substr($fecha, 5, 2);
    $anno = substr($fecha, 0, 4);

    return $dia . ' de ' . getMeses(TP_ABREVIADO, FR_ARREGLO)[intval($mes - 1)] . ' del ' . $anno;
}

function opDiasFecha($operador, $dias, $fecha)
{
    $r = strtotime('' . $operador . '' . $dias . ' day', strtotime($fecha));
    return date('Y-m-d', $r);
}

function difFechas($max, $min)
{
    $datetime1 = date_create($min);
    $datetime2 = date_create($max);

    return date_diff($datetime1, $datetime2);
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
function getClasificacionVerde($id)
{
    return ClasificacionVerde::find($id);
}

function getGrosor($id)
{
    return Grosor::find($id);
}

function getRecepcion($id)
{
    return Recepcion::find($id);
}

function getVariedad($id)
{
    return Variedad::find($id);
}

function getVariedades()
{
    return Variedad::All()->where('estado', '=', 1);
}

function getUnitaria($id)
{
    return ClasificacionUnitaria::find($id);
}

function getUnitarias()
{
    return ClasificacionUnitaria::All()->where('estado', '=', 1);
}

function getUnidadesMedida()
{
    return UnidadMedida::All()->where('estado', '=', 1);
}

function getCalibresRamo()
{
    return ClasificacionRamo::All()->where('estado', '=', 1);
}

function getConfiguracionEmpresa()
{
    $r = ConfiguracionEmpresa::All()->where('estado', '=', 1)->first();
    return $r;
}

function getDocumentos($entidad, $codigo)
{
    return Documento::All()
        ->where('entidad', '=', $entidad)->where('codigo', '=', $codigo)->sortBy('nombre_campo');
}

function getTextFromDocumento($documento)
{
    if ($documento->tipo_dato == 'int')
        return $documento->nombre_campo . ": " . $documento->int;
    elseif ($documento->tipo_dato == 'float')
        return $documento->nombre_campo . ": " . $documento->float;
    elseif ($documento->tipo_dato == 'char')
        return $documento->nombre_campo . ": " . $documento->char;
    elseif ($documento->tipo_dato == 'varchar')
        return $documento->nombre_campo . ": " . $documento->varchar;
    elseif ($documento->tipo_dato == 'boolean')
        return $documento->nombre_campo . ": " . $documento->boolean;
    elseif ($documento->tipo_dato == 'date')
        return $documento->nombre_campo . ": " . $documento->date;
    else
        return $documento->nombre_campo . ": " . $documento->datetime;
}

function valida_especificacion($id_variedad, $id_clasificaicon_ramo, $id_empaque, $cantidad)
{
    $resultado = false;
    $objDetalleEmpaque = DetalleEmpaque::where([
        ['id_empaque', $id_empaque],
        ['id_variedad', $id_variedad],
        ['id_clasificacion_ramo', $id_clasificaicon_ramo]
    ])->select('cantidad')->first();
    if (!empty($objDetalleEmpaque)) {
        if ($objDetalleEmpaque->cantidad >= $cantidad) {
            $resultado = true;
        }
    }
    return $resultado;
}

function getPedidos($idPedido)
{
    $dataPedido = DB::table('pedido as p')->where('p.id_pedido', $idPedido)
        ->join('detalle_pedido as dp', 'p.id_pedido', '=', 'dp.id_pedido')
        ->join('cliente_pedido_especificacion as cpe', 'dp.id_cliente_especificacion', '=', 'cpe.id_cliente_pedido_especificacion')
        ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
        ->select('esp.id_especificacion', 'p.id_pedido', 'esp.nombre', 'dp.cantidad')
        ->get();
    return $dataPedido;
}

function getUnidadMedida($id)
{
    return UnidadMedida::where('id_unidad_medida', $id)->select('siglas')->first();
}

function getEspeficiacionesPedido($idEspecificacion)
{
    $dataDetalleEspecificacion = DB::table('especificacion_empaque as espe')
        ->where('id_especificacion', $idEspecificacion)
        ->join('detalle_especificacionempaque as despem', 'espe.id_especificacion_empaque', '=', 'despem.id_especificacion_empaque')
        ->join('empaque as emp', 'espe.id_empaque', '=', 'emp.id_empaque')
        ->join('variedad as v', 'despem.id_variedad', '=', 'v.id_variedad')
        ->join('clasificacion_ramo as cr', 'despem.id_clasificacion_ramo', '=', 'cr.id_clasificacion_ramo')
        ->select('emp.nombre as emNombre',
            'espe.cantidad',
            'v.nombre as vn',
            'v.siglas',
            'cr.nombre as clNombnre')
        ->get();
    return $dataDetalleEspecificacion;
}

function getPedido($pedido)
{
    return Pedido::find($pedido);
}

function getStock($variedad, $unitaria)
{
    $r = 0;
    $l = StockApertura::All()->where('estado', '=', 1)->where('disponibilidad', '=', 1)
        ->where('id_variedad', '=', $variedad)->where('id_clasificacion_unitaria', '=', $unitaria);

    foreach ($l as $item) {
        $r += $item->cantidad_disponible;
    }

    return $r;
}

function getStockById($id)
{
    return StockApertura::find($id);
}

function getStockToFecha($variedad, $unitaria, $fecha, $dias)
{
    $r = 0;
    $l = StockApertura::All()->where('estado', '=', 1)->where('disponibilidad', '=', 1)
        ->where('id_variedad', '=', $variedad)->where('id_clasificacion_unitaria', '=', $unitaria);

    $fecha = strtotime('+' . $dias . ' day', strtotime($fecha));
    $fecha = date('Y-m-d', $fecha);

    foreach ($l as $item) {
        $fecha_fin = strtotime('+' . $item->dias . ' day', strtotime($item->fecha_inicio));
        $fecha_fin = date('Y-m-d', $fecha_fin);

        if ($fecha == $fecha_fin)
            $r += $item->cantidad_disponible;
    }

    return $r;
}

function getSemanaByDate($fecha)
{
    $r = Semana::All()
        ->where('fecha_inicial', '<=', $fecha)
        ->where('fecha_final', '>=', $fecha)->first();

    return $r;
}

function getSemanaByDateVariedad($fecha, $variedad)
{
    $r = Semana::All()
        ->where('id_variedad', '=', $variedad)
        ->where('fecha_inicial', '<=', $fecha)
        ->where('fecha_final', '>=', $fecha)->first();

    return $r;
}

function getLoteREById($id)
{
    return LoteRE::find($id);
}

function getEmpaque($id)
{
    return Empaque::find($id);
}


/*function getResumenPedidosByFecha($fecha, $variedad)
{
    $pedidos = Pedido::All()->where('estado', '=', 1)->where('empaquetado', '=', 0)
        ->where('fecha_pedido', '=', $fecha);
    $r = [];
    foreach ($pedidos as $pedido) {
        array_push($r, $pedido->id_pedido);
    }
    $query = DB::table('pedido as p')
        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
        ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
        ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
        ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
        ->join('variedad as v', 'v.id_variedad', '=', 'dee.id_variedad')
        ->join('clasificacion_ramo as cr', 'cr.id_clasificacion_ramo', '=', 'dee.id_clasificacion_ramo')
        ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.cantidad')
        ->where('dee.id_variedad', '=', $variedad)
        ->whereIn('dp.id_pedido', $r)
        ->get();
    dd($query);
    //return $pedidos;
}*/
function getCalibreRamoById($id)
{
    return ClasificacionRamo::find($id);
}

function getResumenPedidosByFecha($fecha, $variedad)
{
    $pedidos = Pedido::All()->where('estado', '=', 1)->where('empaquetado', '=', 0)
        ->where('fecha_pedido', '=', $fecha);
    $r = [];
    foreach ($pedidos as $pedido) {
        array_push($r, $pedido->id_pedido);
    }
    $query = DB::table('pedido as p')
        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
        ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
        ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
        ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
        ->join('variedad as v', 'v.id_variedad', '=', 'dee.id_variedad')
        ->join('clasificacion_ramo as cr', 'cr.id_clasificacion_ramo', '=', 'dee.id_clasificacion_ramo')
        ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))
        ->where('dee.id_variedad', '=', $variedad)
        ->whereNull('dee.tallos_x_ramos')
        ->whereIn('p.id_pedido', $r)
        ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo')
        ->get();
    return $query;
}

function getResumenPedidosByFechaOfTallos($fecha, $variedad)
{
    $pedidos = Pedido::All()->where('estado', '=', 1)->where('empaquetado', '=', 0)
        ->where('fecha_pedido', '=', $fecha);
    $r = [];
    foreach ($pedidos as $pedido) {
        array_push($r, $pedido->id_pedido);
    }
    $query = DB::table('pedido as p')
        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
        ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
        ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
        ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
        ->join('variedad as v', 'v.id_variedad', '=', 'dee.id_variedad')
        ->join('clasificacion_ramo as cr', 'cr.id_clasificacion_ramo', '=', 'dee.id_clasificacion_ramo')
        ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos',
            DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))
        ->where('dee.id_variedad', '=', $variedad)
        ->whereNotNull('dee.tallos_x_ramos')
        ->whereIn('p.id_pedido', $r)
        ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos')
        ->get();
    return $query;
}

function getCalibreRamoEstandar()
{
    $r = ClasificacionRamo::All()->where('estado', '=', 1)->where('estandar', '=', 1)->first();

    return $r;
}

function getDetalleEspecificacion($id_especificacion){
    $data =  Especificacion::where('especificacion.id_especificacion',$id_especificacion)
        ->join('especificacion_empaque as eemp','especificacion.id_especificacion','eemp.id_especificacion')
        ->join('empaque as e','eemp.id_empaque','e.id_empaque')
        ->join('detalle_especificacionempaque as deemp','eemp.id_especificacion_empaque','deemp.id_especificacion_empaque')
        ->join('variedad as v','deemp.id_variedad','v.id_variedad')
        ->join('empaque as empE','deemp.id_empaque_e','empE.id_empaque')
        ->join('empaque as empP','deemp.id_empaque_p','empP.id_empaque')
        ->join('clasificacion_ramo as cr','deemp.id_clasificacion_ramo','cr.id_clasificacion_ramo')
        ->join('unidad_medida as umCR','cr.id_unidad_medida','umCR.id_unidad_medida')
        ->join('especificacion_empaque as espemp','especificacion.id_especificacion','espemp.id_especificacion')
        ->join('especificacion_empaque as EspeEmpa','e.id_empaque','EspeEmpa.id_empaque');

        $existUnidadMedidida = DetalleEspecificacionEmpaque::where('id_detalle_especificacionempaque',$data->first()->id_detalle_especificacionempaque)->first();
        $a = 0;
        if($existUnidadMedidida->id_unidad_medida != null){
            $data->join('unidad_medida as um','deemp.id_unidad_medida','um.id_unidad_medida');
            $a =1;
        }
        $data = $data->select('especificacion.id_especificacion','v.id_variedad','v.nombre as nombre_variedad','empE.nombre as envoltura', 'empP.nombre as presentacion','deemp.cantidad as cantidad_ramos','deemp.tallos_x_ramos','deemp.longitud_ramo', $a==1 ? 'um.siglas as siglas_unidad_medida_longitud': 'e.nombre as nombre_empaque','cr.nombre as nombre_clasificacion_ramo','umCR.siglas as siglas_unidad_medida_clasificacion_ramo','espemp.cantidad as cantidad_cajas','e.nombre as nombre_empaque')->first();
        return $data->cantidad_cajas." ".$data->nombre_empaque." con " .$data->cantidad_ramos. " ramos c/u de ".$data->nombre_clasificacion_ramo." ". $data->siglas_unidad_medida_clasificacion_ramo ." ".$data->nombre_variedad." ".$data->envoltura." ". $data->presentacion ." ". $data->tallos_x_ramos." tallos por ramo ". $data->longitud_ramo." ". $data->siglas_unidad_medida_longitud;
}

/* ============ Obtener los ramos sacados de apertura para los pedidos de un "fecha" ==============*/
function getDestinadosToFrioByFecha($fecha, $variedad)
{
    $consumo = Consumo::All()->where('fecha_pedidos', '=', $fecha)->where('estado', '=', 1)->first();
    if ($consumo != '')
        return $consumo->getDestinados($variedad);
    return 0;
}

function manualPagination($arrData,$perPage){
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = array_slice($arrData, $perPage * ($currentPage - 1), $perPage);
    $data = new LengthAwarePaginator($currentItems, count($arrData), $perPage, $currentPage);
    return $data->setPath(Resq::path());
}

//==================  Funciones involucradas en la facturacion electrónica ======================//
function generaDigitoVerificador($cadena){
    $arr_num = str_split($cadena);
    $cant_cadena = count($arr_num);
    $total =0;
    if($cant_cadena===48){
        $x=2;
        for($i=47;$i>=0;$i--){
            $cantidad = $arr_num[$i] * $x;
            $total += $cantidad;
            $x++;
            if($x == 8)
                $x=2;
        }
        $cociente = $total/11 ;
        $producto = ((int)$cociente)*11;
        $resultado = $total-$producto;
        $digito_verificador = 11-$resultado;

        if((11*(int)$cociente)+$resultado === $total){
            if($digito_verificador == 10)
                $digito_verificador = 1;
            elseif($digito_verificador == 11)
                $digito_verificador = 0;

            return $digito_verificador;
        }else{
            return false;
        }
    }
}

function firmarComprobanteXml($archivo_xml){
    exec('java -Dfile.encoding=UTF-8 -jar '.env('PATH_JAR_FIRMADOR').' '
        .env('PATH_XML_GENERADOS')." "
        .$archivo_xml." "
        .env('PATH_XML_FIRMADOS')." "
        .env('PATH_FIRMA_DIGITAL')." "
        .env('CONTRASENA_FIRMA_DIGITAL')." "
        .env('NOMBRE_ARCHIVO_FIRMA_DIGITAL')." ",
        $salida,$var);
    if($var == 0)
        return $salida[0];
    if($var != 0)
        return false;
}

function mensajeFirmaElectronica($indice, $id_envio){
    $mensaje = [
        0 => "No se ha obtenido el archivo de la firma digital correctamente, verifique que el path propocionado en la variable de entorno 'PATH_FIRMA_DIGITAL' en el archivo .env coincida con la ubicación actual del archivo la firma digital y el String pasado a la variable 'NOMBRE_ARCHIVO_FIRMA_DIGITAL' corresponda con el nombre del archivo), una vez corregido el error puede filtrar por 'GENERADOS' y proceder a realizar la firma del mismo",
        1 => "Verificar lo explicado en el Índice 0 de este apartado y a su vez verificar que exista el certificado como archivo físico, una vez corregido el error puede filtrar por 'GENERADOS' y proceder a realizar la firma del mismo",
        2 => "No se pudo acceder al contenido del archivo del certificado electrónico, verifique los indicies 0 y 1 de este apartado  y a su vez que el String pasado en la variable 'CONTRASENA_FIRMA_DIGITAL' en el archivo .env coincida con la propocionada por el ente certificador, una vez corregido el error puede filtrar por 'GENERADOS' y proceder a realizar la firma del mismo",
        3 => "Se produjo un error al momento de generar la firma electrónica del xml pertenciente a la factura del envío N# ".$id_envio.", por favor comunicarse con el deparatmento de tecnología, una vez corregido el error puede filtrar por 'GENERADOS' y proceder a realizar la firma del mismo",
        4 => "El archivo firamado xml pertenciente a la factura del envío N# ".$id_envio." no pudo ser guardado en su respectiva carpeta, verifique que el path propocionado en la variable de entorno 'PATH_XML_FIRMADOS' en el archivo .env coincida con la carpeta creada en esa ruta, una vez corregido el error puede filtrar por 'GENERADOS' y proceder a realizar la firma del mismo",
        5 => "La factura pertenciente al envío N# ".$id_envio." se ha generado y firmado con exito",
    ];
    return $mensaje[$indice];
}

function enviarComprobante($comprobante_xml, $clave_acceso){
    ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
    exec('java -Dfile.encoding=UTF-8 -jar '.env('PATH_JAR_ENVIADOR').' '
        .env('PATH_XML_FIRMADOS')." "
        .$comprobante_xml." "
        .env('PATH_XML_ENVIADOS')." "
        .env('PATH_XML_RECHAZADOS')." "
        .env('PATH_XML_AUTORIZADOS')." "
        .env('PATH_XML_NO_AUTORIZADOS')." "
        .env('URL_WS_RECEPCION')." "
        .env('URL_WS_ATURIZACION')." "
        .$clave_acceso." ",
        $salida,$var);
    if($var == 0)
        return $salida;
    if($var != 0)
        return false;
}

function mensaje_envio_comprobante($indice){
    $mensaje = [
        0 => "El comprobante fue enviado pero devuelto por el SRI, filtre por 'DEVUELTOS' para verificar la causa de la devolución",
        1 => "El comprobante fue enviado y recibido con éxito por el SRI",
        2 => "Fallo en la conexión con el web service del SRI, intente nuevamente",
    ];
    return $mensaje[$indice];
}

function getDatosFacturaEnvio($id_envio){
    return Envio::where([
        ['envio.id_envio',$id_envio],
        ['dc.estado',1]
    ])->join('detalle_envio as de', 'envio.id_envio','de.id_envio')
        ->join('pedido as p', 'envio.id_pedido','p.id_pedido')
        ->join('detalle_cliente as dc','p.id_cliente','dc.id_cliente')
        ->join('especificacion as e','de.id_especificacion','e.id_especificacion')
        ->join('especificacion_empaque as eemp', 'e.id_especificacion','eemp.id_especificacion')
        ->join('detalle_especificacionempaque as deemp','eemp.id_especificacion_empaque','deemp.id_especificacion_empaque')
        ->join('empaque as emp','eemp.id_empaque','emp.id_empaque')
        ->join('configuracion_empresa as ce','emp.id_configuracion_empresa','ce.id_configuracion_empresa')
        ->join('agencia_transporte as at','de.id_agencia_transporte','at.id_agencia_transporte')
        ->join('tipo_impuesto as ti','dc.codigo_porcentaje_impuesto','ti.codigo')
        ->join('variedad as v','deemp.id_variedad','v.id_variedad')
        ->join('planta as pl','v.id_planta','pl.id_planta')
        ->join('clasificacion_ramo as cr','deemp.id_clasificacion_ramo','cr.id_clasificacion_ramo')
        ->join('unidad_medida as umPR','cr.id_unidad_medida','umPR.id_unidad_medida')
        ->join('unidad_medida as umLR','deemp.id_unidad_medida','umLR.id_unidad_medida')
        ->select('ce.nombre as nombre_empresa','ce.razon_social','ce.direccion_matriz','ce.direccion_establecimiento','dc.codigo_identificacion','dc.ruc as identificacion','dc.nombre as nombre_cliente','dc.direccion','dc.provincia','dc.telefono','dc.correo','dc.codigo_impuesto','deemp.id_variedad','deemp.id_clasificacion_ramo','de.cantidad as cantidad_detalles','dc.codigo_porcentaje_impuesto as codigo_porcentaje','ti.porcentaje as porcntaje_iva','deemp.cantidad as cantidad_ramos','eemp.cantidad as cantidad_cajas','v.nombre as nombre_variedad','v.siglas as siglas_variedad','cr.nombre as nombre_clasificacion','umPR.siglas as siglas_unidad_medida_peso_ramo','pl.nombre as nombre_planta','deemp.longitud_ramo','umLR.siglas as siglas_unidad_medida_lognitud_ramo');
}


/* ============ Calcular la cantidad de cajas equivalentes segun grosor_variedad ==============*/
function getEquivalentesByGrosorVariedad($fecha, $grosor, $variedad)
{
    $r = 0;
    $listado = DB::table('pedido as p')
        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
        ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
        ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
        ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
        ->select('dee.cantidad as cantidad_ramos', 'ee.cantidad as cantidad_empaques', 'dp.cantidad as cantidad_pedidos')
        ->where('p.estado', '=', 1)
        ->where('p.empaquetado', '=', 0)
        ->where('p.fecha_pedido', '=', $fecha)
        ->where('dee.id_grosor_ramo', '=', $grosor)
        ->where('dee.id_variedad', '=', $variedad)
        ->get();

    foreach ($listado as $item) {
        $ramos = $item->cantidad_ramos * $item->cantidad_empaques * $item->cantidad_pedidos;
        $r += round($ramos / getConfiguracionEmpresa()->ramos_x_caja, 2);
    }
    return $r;

}

/* ============ Cantidad de ramos pedidos segun fecha, variedad, clasificacion_ramo, envoltura, presentacion, tallos_x_ramo, longitud_ramo, unidad_medida ==============*/
function getCantidadRamosPedidosForCB($fecha, $variedad, $clasificacion_ramo, $envoltura, $presentacion, $tallos_x_ramos, $longitud_ramo, $unidad_medida)
{
    $r = DB::table('pedido as p')
        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
        ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
        ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
        ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
        ->join('variedad as v', 'v.id_variedad', '=', 'dee.id_variedad')
        ->select(DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))
        ->where('p.estado', '=', 1)
        ->where('p.empaquetado', '=', 0)
        ->where('p.fecha_pedido', '=', $fecha)
        ->where('dee.id_variedad', '=', $variedad)
        ->where('dee.id_clasificacion_ramo', '=', $clasificacion_ramo)
        ->where('dee.id_empaque_e', '=', $envoltura)
        ->where('dee.id_empaque_p', '=', $presentacion);

    if ($tallos_x_ramos != '')
        $r = $r->where('dee.tallos_x_ramos', '=', $tallos_x_ramos);
    if ($longitud_ramo != '')
        $r = $r->where('dee.longitud_ramo', '=', $longitud_ramo);
    if ($unidad_medida != '')
        $r = $r->where('dee.id_unidad_medida', '=', $unidad_medida);

    if (count($r->get()) > 0)
        return $r->get()[0]->cantidad != '' ? $r->get()[0]->cantidad : 0;
    else
        return 0;
}

/* ============ Obtener Inventario en frío ==============*/
function getDisponibleInventarioFrio($variedad, $clasificacion_ramo, $envoltura, $presentacion, $tallos_x_ramos, $longitud_ramo, $unidad_medida)
{
    $r = DB::table('inventario_frio as if')
        ->select(DB::raw('sum(if.disponibles) as cantidad'))
        ->where('estado', '=', 1)
        ->where('disponibilidad', '=', 1)
        ->where('id_variedad', '=', $variedad)
        ->where('id_clasificacion_ramo', '=', $clasificacion_ramo)
        ->where('id_empaque_e', '=', $envoltura)
        ->where('id_empaque_p', '=', $presentacion);

    if ($tallos_x_ramos != '')
        $r = $r->where('if.tallos_x_ramo', '=', $tallos_x_ramos);
    if ($longitud_ramo != '')
        $r = $r->where('if.longitud_ramo', '=', $longitud_ramo);
    if ($unidad_medida != '')
        $r = $r->where('if.id_unidad_medida', '=', $unidad_medida);

    if (count($r->get()) > 0)
        return $r->get()[0]->cantidad != '' ? $r->get()[0]->cantidad : 0;
    else
        return 0;
}
