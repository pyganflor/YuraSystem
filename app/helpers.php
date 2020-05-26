<?php

use Barryvdh\DomPDF\Facade as PDF;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Request as Resq;
use yura\Mail\CorreoErrorEnvioComprobanteElectronico;
use yura\Mail\CorreoFactura;
use yura\Modelos\Aerolinea;
use yura\Modelos\AgenciaCarga;
use yura\Modelos\Bitacora;
use yura\Modelos\Camion;
use yura\Modelos\Ciclo;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ClasificacionUnitaria;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cliente;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\CodigoDae;
use yura\Modelos\Color;
use yura\Modelos\Coloracion;
use yura\Modelos\Comprobante;
use yura\Modelos\Conductor;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\Consumo;
use yura\Modelos\Cosecha;
use yura\Modelos\DesgloseEnvioFactura;
use yura\Modelos\Despacho;
use yura\Modelos\DetalleDespacho;
use yura\Modelos\DetalleEmpaque;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\DetalleFactura;
use yura\Modelos\DetalleGuiaRemision;
use yura\Modelos\DetallePedido;
use yura\Modelos\DetallePedidoDatoExportacion;
use yura\Modelos\Distribucion;
use yura\Modelos\Documento;
use yura\Modelos\Empaque;
use yura\Modelos\Envio;
use yura\Modelos\Especificacion;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\FacturaClienteTercero;
use yura\Modelos\Grosor;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Indicador;
use yura\Modelos\LoteRE;
use yura\Modelos\Marcacion;
use yura\Modelos\Modulo;
use yura\Modelos\Pais;
use yura\Modelos\Pedido;
use yura\Modelos\Planta;
use yura\Modelos\Precio;
use yura\Modelos\ProductoYuraVenture;
use yura\Modelos\Recepcion;
use yura\Modelos\Rol_Submenu;
use yura\Modelos\Semana;
use yura\Modelos\StockApertura;
use yura\Modelos\Submenu;
use yura\Modelos\TipoIdentificacion;
use yura\Modelos\TipoImpuesto;
use yura\Modelos\Transportista;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Usuario;
use yura\Modelos\Variedad;
use yura\Modelos\DetalleEspecificacionEmpaqueRamosCaja;


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
        $bitacora->fecha_registro = date('Y-m-d H:i:s');
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
define('TP_NUMERO', 4);

define('DIAS_SEMANA', serialize(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']));
define('DIAS_SEMANA_ABREVIADOS', serialize(['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sáb', 'Dom']));
define('DIAS_SEMANA_MUY_ABREVIADOS', serialize(['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do']));
define('DIAS_SEMANA_LETRA', serialize(['L', 'M', 'M', 'J', 'V', 'S', 'D']));
define('MESES', serialize(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']));
define('MESES_ABREVIADOS', serialize(['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic']));
define('MESES_MUY_ABREVIADOS', serialize(['En', 'Fb', 'Mz', 'Ab', 'My', 'Jn', 'Jl', 'Ag', 'Sp', 'Oc', 'Nv', 'Dc']));
define('MESES_LETRA', serialize(['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D']));
define('MESES_NUMERO', serialize(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']));
define('A_Z', serialize(range('A', 'Z')));

function getListColores()
{
    return ['#ff0000', '#1000ff', '#33ff00', '#ff851b', '#00c0ef', '#e000ef', '#ebef00', '#b400ef'];
}

function getHorasDiarias()
{
    return ['00:01', '01:02', '02:03', '03:04', '04:05', '05:06', '06:07', '07:08', '08:09', '09:10', '10:11', '11:12', '12:13', '13:14', '14:15',
        '15:16', '16:17', '17:18', '18:19', '19:20', '20:21', '21:22', '22:23', '23:00'];
}

function getIntervalosHorasDiarias()
{
    return [['inicio' => '00:00', 'fin' => '01:00'], ['inicio' => '01:00', 'fin' => '02:00'], ['inicio' => '02:00', 'fin' => '03:00'],
        ['inicio' => '03:00', 'fin' => '04:00'], ['inicio' => '04:00', 'fin' => '05:00'], ['inicio' => '05:00', 'fin' => '06:00'],
        ['inicio' => '06:00', 'fin' => '07:00'], ['inicio' => '07:00', 'fin' => '08:00'], ['inicio' => '08:00', 'fin' => '09:00'],
        ['inicio' => '09:00', 'fin' => '10:00'], ['inicio' => '10:00', 'fin' => '11:00'], ['inicio' => '11:00', 'fin' => '12:00'],
        ['inicio' => '12:00', 'fin' => '13:00'], ['inicio' => '13:00', 'fin' => '14:00'], ['inicio' => '14:00', 'fin' => '15:00'],
        ['inicio' => '15:00', 'fin' => '16:00'], ['inicio' => '16:00', 'fin' => '17:00'], ['inicio' => '17:00', 'fin' => '18:00'],
        ['inicio' => '18:00', 'fin' => '19:00'], ['inicio' => '19:00', 'fin' => '20:00'], ['inicio' => '20:00', 'fin' => '21:00'],
        ['inicio' => '21:00', 'fin' => '22:00'], ['inicio' => '22:00', 'fin' => '23:00'], ['inicio' => '23:00', 'fin' => '00:00']];
}

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

        case TP_NUMERO:
            $valor = unserialize(MESES_NUMERO);
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
        ->where('rs.estado', '=', 'A')
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

function opHorasFecha($operador, $horas, $fecha)
{
    $r = strtotime('' . $operador . '' . $horas . ' hour', strtotime($fecha));
    return date('Y-m-d H:i', $r);
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

function getCliente($id)
{
    return Cliente::find($id);
}

function getClientes()
{
    $l = DB::table('cliente as c')
        ->select('c.id_cliente')
        ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'c.id_cliente')
        ->where('c.estado', '=', 1)
        ->where('dc.estado', '=', 1)
        ->orderBy('dc.nombre', 'asc')
        ->get();
    $r = [];
    foreach ($l as $item) {
        array_push($r, getCliente($item->id_cliente));
    }
    return $r;
}

function getDatosCliente($id_cliente)
{
    return Cliente::where('cliente.id_cliente', $id_cliente)->join('detalle_cliente as dc', 'cliente.id_cliente', 'dc.id_cliente')
        ->where('dc.estado', 1);
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

function getVariedadesByPlanta($p, $formato = 'option')
{
    $p = Planta::find($p);
    if ($formato == 'option') {
        $r = '';
        foreach ($p->variedades as $v) {
            $selected = $v->id_variedad == 2 ? "selected" : "";
            $r .= '<option value="' . $v->id_variedad . '" ' . $selected . '>' . $v->nombre . '</option>';
        }
        return $r;
    } else {
        return $p->variedades;
    }
}

function getPlantas()
{
    return Planta::All()->where('estado', 1);
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
    return ClasificacionRamo::All()->where('estado', '=', 1)->sortBy('nombre');
}

function getConfiguracionEmpresa($id = null, $all = false)
{
    if ($all) {
        return ConfiguracionEmpresa::get();
    } else {
        isset($id)
            ? $empresa = ConfiguracionEmpresa::where('id_configuracion_empresa', $id)
            : $empresa = ConfiguracionEmpresa::where('estado', 1);

        return $empresa->first();
    }
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
        ->select('emp.nombre as emNombre', 'espe.cantidad', 'v.nombre as vn', 'v.siglas', 'cr.nombre as clNombnre')->get();
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

function getModuloById($id)
{
    return Modulo::find($id);
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

function getModulos($option = null)
{
    if ($option == 'A') {   // modulos activos
        return DB::table('ciclo as c')
            ->join('modulo as m', 'm.id_modulo', '=', 'c.id_modulo')
            ->select('m.*')
            ->where('m.estado', 1)
            ->where('c.estado', 1)
            ->where('c.activo', 1)
            ->orderBy('m.nombre')
            ->get();
    }
    return Modulo::All()->where('estado', '=', 1);
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

function getDetalleEspecificacion($id_especificacion)
{
    $data = getEspecificacion($id_especificacion);
    $arrData = [];
    foreach ($data->especificacionesEmpaque as $espEmp)
        foreach ($espEmp->detalles as $det)
            $arrData[] = [
                'variedad' => $det->variedad->nombre,
                'id_variedad' => $det->variedad->id_variedad,
                'calibre' => $det->clasificacion_ramo->nombre,
                'caja' => explode("|", $espEmp->empaque->nombre)[0],
                'rxc' => $det->cantidad,
                'presentacion' => $det->empaque_p->nombre,
                'txr' => $det->tallos_x_ramos,
                'longitud' => $det->longitud_ramo,
                'unidad_medida_longitud' => isset($det->unidad_medida->siglas) ? $det->unidad_medida->siglas : null,
                'id_especificacion_empaque' => $espEmp->id_especificacion_empaque
            ];
    return $arrData;
}

function getCantDetEspEmp($idEsp)
{
    $data = getEspecificacion($idEsp);
    $a = 0;
    foreach ($data->especificacionesEmpaque as $espEmp)
        $a += count($espEmp->detalles);
    return $a;
}

function getEspecificacion($idEspecificacion)
{
    return Especificacion::find($idEspecificacion);
}

function getCantidadDetallesEspecificacionByPedido($id_pedido)
{
    $r = 0;
    foreach (getPedido($id_pedido)->detalles as $det_ped)
        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
            foreach ($esp_emp->detalles as $det_esp)
                $r++;
    return $r;
}

function getCantidadDetallesByEspecificacion($id_especificacion)
{
    $r = 0;
    foreach (Especificacion::find($id_especificacion)->especificacionesEmpaque as $esp_emp)
        foreach ($esp_emp->detalles as $det_esp)
            $r++;
    return $r;
}

function getPrecioByClienteDetEspEmp($cliente, $det_esp)
{
    $precio = Precio::All()->where('id_cliente', $cliente)->where('id_detalle_especificacionempaque', $det_esp)->first();
    return $precio;
}

function getPrecioByDetEsp($string, $det_esp)
{
    foreach (explode('|', $string) as $x) {
        if (count(explode(';', $x)) > 0) {
            if (explode(';', $x)[1] > 0) {  //explode(';', $x)[1] == 0 CUANDO UN PEDIDO SEA EN TALLOS
                if (explode(';', $x)[1] == $det_esp)
                    return explode(';', $x)[0];
            } else {
                return explode(';', $x)[0]; //POR QUE SOLO REALIZARAN PEIDOS EN TALLOS CON UN SOLO DETALLE ESPECIFICACION EMPAQUE, CAMBIAR EN CASO DE QUE NO SE VAYA A HACER ASÍ
            }
        }
    }
    return 1;
}

function havePrecioByDetEsp($string, $det_esp)
{
    foreach (explode('|', $string) as $x) {
        if (count(explode(';', $x)) > 0) {
            if (explode(';', $x)[1] > 0) {  //explode(';', $x)[1] == 0 CUANDO UN PEDIDO SEA EN TALLOS
                if (explode(';', $x)[1] == $det_esp)
                    return true;
            } else {
                return true; //POR QUE SOLO REALIZARAN PEIDOS EN TALLOS CON UN SOLO DETALLE ESPECIFICACION EMPAQUE, CAMBIAR EN CASO DE QUE NO SE VAYA A HACER ASÍ
            }
        }
    }
    return false;
}

/* ============ Obtener los ramos sacados de apertura para los pedidos de un "fecha" ==============*/
function getDestinadosToFrioByFecha($fecha, $variedad)
{
    $consumo = Consumo::All()->where('fecha_pedidos', '=', $fecha)->where('estado', '=', 1)->first();
    if ($consumo != '')
        return $consumo->getDestinados($variedad);
    return 0;
}

function manualPagination($arrData, $perPage)
{
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = array_slice($arrData, $perPage * ($currentPage - 1), $perPage);
    $data = new LengthAwarePaginator($currentItems, count($arrData), $perPage, $currentPage);
    return $data->setPath(Resq::path());
}

function getPais($codigo)
{
    return Pais::where('codigo', $codigo)->select('nombre')->first();
}

//==================  Funciones involucradas en la facturación electrónica ======================//
function generaDigitoVerificador($cadena)
{
    $arr_num = str_split($cadena);
    $cant_cadena = count($arr_num);
    $total = 0.00;
    if ($cant_cadena === 48) {
        $x = 2;
        for ($i = 47; $i >= 0; $i--) {
            $cantidad = $arr_num[$i] * $x;
            $total += $cantidad;
            $x++;
            if ($x == 8)
                $x = 2;
        }
        $cociente = $total / 11;
        $producto = ((int)$cociente) * 11;
        $resultado = $total - $producto;
        $digito_verificador = 11 - $resultado;

        if ((11 * (int)$cociente) + $resultado === $total) {
            if ($digito_verificador == 10)
                $digito_verificador = 1;
            elseif ($digito_verificador == 11)
                $digito_verificador = 0;
            return $digito_verificador;
        } else {
            return false;
        }
    }
}

function firmarComprobanteXml($archivo_xml, $carpeta, $firma_electronica, $contrasena_firma)
{
    exec("java -Dfile.encoding=UTF-8 -jar " . env('PATH_JAR_FIRMADOR') . " "
        . env('PATH_XML_GENERADOS') . $carpeta . " "
        . $archivo_xml . " "
        . env('PATH_XML_FIRMADOS') . $carpeta . " "
        . env('PATH_FIRMA_DIGITAL') . " "
        . $contrasena_firma . " "
        . $firma_electronica . " ",
        $salida, $var);

    if ($var == 0)
        return $salida[0];
    if ($var != 0)
        return false;
}

function mensajeFirmaElectronica($indice, $archivo)
{
    $mensaje = [
        0 => "No se ha obtenido el archivo de la firma digital correctamente, verifique que el archivo deste debidamente cargado en el sistema, una vez corregido el error puede filtrar por 'NO FIRMADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
        1 => "Verificar lo explicado en el Índice 0 de este apartado y a su vez verificar que exista el certificado como archivo físico, una vez corregido el error puede filtrar por 'NO FIRMADOS' y proceder a realizar la firma del mismo",
        2 => "No se pudo acceder al contenido del archivo del certificado electrónico, verifique los indicies 0 y 1 de este apartado  y a su vez que el String pasado en la variable 'CONTRASENA_FIRMA_DIGITAL' en el archivo .env coincida con la propocionada por el ente certificador, una vez corregido el error puede filtrar por 'NO FIRMADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
        3 => "Se produjo un error al momento de generar la firma electrónica del xml " . $archivo . ", por favor comunicarse con el deparatmento de tecnología, una vez corregido el error puede filtrar por 'NO FIRMADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
        4 => "El archivo firmado xml N# " . $archivo . " no pudo ser guardado en su respectiva carpeta, verifique que el path propocionado en la variable de entorno 'PATH_XML_FIRMADOS' en el archivo .env coincida con la carpeta creada en esa ruta, una vez corregido el error puede filtrar por 'GENERADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
        5 => "El comprobante N# " . $archivo . " se ha generado y firmado con exito",
    ];
    return $mensaje[$indice];
}

function enviarComprobante($comprobante_xml, $clave_acceso, $carpeta)
{
    ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
    exec('java -Dfile.encoding=UTF-8 -jar ' . env('PATH_JAR_ENVIADOR') . ' '
        . env('PATH_XML_FIRMADOS') . $carpeta . " "
        . $comprobante_xml . " "
        . env('PATH_XML_ENVIADOS') . $carpeta . " "
        . env('PATH_XML_RECHAZADOS') . $carpeta . " "
        . env('PATH_XML_AUTORIZADOS') . $carpeta . " "
        . env('PATH_XML_NO_AUTORIZADOS') . $carpeta . " "
        . env('URL_WS_RECEPCION') . " "
        . env('URL_WS_ATURIZACION') . " "
        . $clave_acceso . " ",
        $salida, $var);
    if ($var == 0)
        return $salida;
    if ($var != 0)
        return false;
}

function mensaje_envio_comprobante($indice)
{
    $mensaje = [
        0 => "El comprobante fue enviado pero rechazado por el SRI, Intente nuevamente",
        1 => "Se ha generado con exito la factura y enviado el  mail correspondiente",
        2 => "Fallo en la conexión con el web service del SRI, intente nuevamente",
    ];
    return $mensaje[$indice];
}

function getCodigoDae($codigoPais, $mes, $anno, $idConfiguracionEmpresa)
{
    return CodigoDae::where([
        ['mes', $mes],
        ['anno', $anno],
        ['codigo_pais', $codigoPais],
        ['id_configuracion_empresa', $idConfiguracionEmpresa],
        ['estado', 1]
    ])->first();
}

function getFacturado($idEnvio, $estado)
{
    $f = Comprobante::where([
        ['id_envio', $idEnvio],
        ['estado', $estado],
        ['comprobante.habilitado', true]
    ])->count();
    ($f > 0) ? $facturado = true : $facturado = null;
    return $facturado;
}

function respuesta_autorizacion_comprobante($clave_acceso_lote, $sub_carpeta, $envio_correo)
{
    $cliente = new SoapClient(env('URL_WS_ATURIZACION'));
    $response = $cliente->autorizacionComprobanteLote(["claveAccesoLote" => $clave_acceso_lote]);
    /*$message = "<div class='alert text-center  alert-danger'>" .
        "<p>No se pudo consultar la factura enviada al SRI</p>"
        . "</div>";*/
    if ($response->RespuestaAutorizacionLote->numeroComprobantesLote == 0) {
        //CORREO SISTEMAS
        Mail::to("pruebas-c26453@inbox.mailtrap.io")->send(new CorreoErrorEnvioComprobanteElectronico($response->RespuestaAutorizacionLote->claveAccesoLoteConsultada, $sub_carpeta));
        $response = "<div class='alert text-center  alert-warning'>" .
            "<p>EL SRI no ha aprobado el comprobante enviado, un correo electrónico ha sido enviado al áera de sistema para poder solucionar el inconveniente</p>"
            . "</div>";
    } else {
        if ($response->RespuestaAutorizacionLote->autorizaciones != "") {
            if ($response->RespuestaAutorizacionLote->numeroComprobantesLote > 0) {
                is_array($response->RespuestaAutorizacionLote->autorizaciones->autorizacion)
                    ? $autorizaciones = $response->RespuestaAutorizacionLote->autorizaciones->autorizacion
                    : $autorizaciones = [$response->RespuestaAutorizacionLote->autorizaciones->autorizacion];

                $response = '';
                foreach ($autorizaciones as $autorizacion) {
                    $estado = $autorizacion->estado;
                    $xmlEnviado = simplexml_load_string($autorizacion->comprobante);
                    $claveAcceso = (string)$xmlEnviado->infoTributaria->claveAcceso;
                    $tipoDocumento = (string)$xmlEnviado->infoTributaria->codDoc;
                    $mailCliente = (string)$xmlEnviado->infoAdicional->campoAdicional[1];
                    $nombreCliente = (string)$xmlEnviado->infoFactura->razonSocialComprador;
                    ($envio_correo = "true")
                        ? $msg_correo = "y se ha enviado el correo correspondiente al cliente"
                        : $msg_correo = "";
                    if ($estado === "AUTORIZADO") {
                        $msg = "La factura del comprobante " . $claveAcceso . " ha sido aprobada por el SRI " . $msg_correo;
                        $response .= accionAutorizacion($autorizacion, env('PATH_XML_AUTORIZADOS') . $sub_carpeta, $msg, $tipoDocumento, $mailCliente, $nombreCliente, $envio_correo);
                    } else if ($estado === "RECHAZADA" || $estado === "DEVUELTA") {
                        $msg = "La factura del comprobante " . $claveAcceso . " ha sido rechazada por el SRI, verifique la causa en el listado de pdf y realice nuevamente el proceso de facturación del envío";
                        $response .= accionAutorizacion($autorizacion, env('PATH_XML_RECHAZADOS') . $sub_carpeta, $msg);
                    } else if ($estado === "NO AUTORIZADO") {
                        $msg = "La factura del comprobante " . $claveAcceso . " no ha sido aprobada por el SRI, verifique la causa en el listado de pdf y realice nuevamente el proceso de facturación del envío";
                        $response .= accionAutorizacion($autorizacion, env('PATH_XML_NO_AUTORIZADOS') . $sub_carpeta, $msg);
                    }
                }
            }
        }
    }
    return $response;
}

function accionAutorizacion($autorizacion, $path, $msg, $tipoDocumento = false, $mailCliente = false, $nombreCliente = false, $envio_correo = false)
{

    $numeroAutorizacion = (String)$autorizacion->numeroAutorizacion;
    $fechaAutorizacion = (String)$autorizacion->fechaAutorizacion;
    $ambiente = (String)$autorizacion->ambiente;
    $dataXML = (String)$autorizacion->comprobante;

    $actualizaEstado = 1;

    if ((String)$autorizacion->estado === "AUTORIZADO") {
        $actualizaEstado = 5;
        $numeroComprobante = "001-" . getDetallesClaveAcceso($numeroAutorizacion, 'PUNTO_ACCESO') . "-" . getDetallesClaveAcceso($numeroAutorizacion, 'SECUENCIAL');
        $class = 'success';
        generaDocumentoPDF($autorizacion, $tipoDocumento);
    } else {
        $class = 'danger';
        $actualizaEstado = 4;
        $numeroComprobante = null;
        $causa = "";
        foreach ($autorizacion->mensajes as $mensaje)
            $causa .= $mensaje->mensaje . ": " . $mensaje->informacionAdicional . ", Tipo: " . $mensaje->tipo . ", ";
    }

    $objComprobante = Comprobante::where([
        ['clave_acceso', $numeroAutorizacion],
        ['tipo_comprobante', '01']
    ]);
    $autorizacion->estado !== "AUTORIZADO"
        ? $objComprobante->update(['cuasa' => $causa])
        : $objComprobante->update(['estado' => $actualizaEstado, 'numero_comprobante' => $numeroComprobante]);

    $xml = new DOMDocument(1.0, 'UTF-8');
    $xml->loadXML($dataXML);
    if ($tipoDocumento == "01")
        $nodo = $xml->getElementsByTagName("factura")->item(0);

    if ($tipoDocumento == "06")
        $nodo = $xml->getElementsByTagName("guiaRemision")->item(0);

    $nuevoXml = new DOMDocument(1.0, 'UTF-8');
    $nuevoXml->formatOutput = true;
    if ($autorizacion->estado === "AUTORIZADO") {
        $nuevoXml->loadXML("<xmlAutorizado><estado>" . $autorizacion->estado . "</estado><ambiente>" . $ambiente . "</ambiente><fechaAutorizacion>" . $fechaAutorizacion . "</fechaAutorizacion><numeroAutorizacion>" . $numeroAutorizacion . "</numeroAutorizacion></xmlAutorizado>");
    } else {
        $nuevoXml->loadXML("<xmlNoAutorizado><estado>" . $autorizacion->estado . "</estado><ambiente>" . $ambiente . "</ambiente><fechaAutorizacion>" . $autorizacion->fechaAutorizacion . "</fechaAutorizacion><numeroAutorizacion>" . $autorizacion->estado . "</numeroAutorizacion><causa>" . $causa . "</causa></xmlNoAutorizado>");
    }

    $node = $nuevoXml->importNode($nodo, true);
    $nuevoXml->documentElement->appendChild($node);
    $nuevoXml->saveXML();
    $nuevoXml->save($path . $numeroAutorizacion . ".xml");

    if ((String)$autorizacion->estado === "AUTORIZADO" && $envio_correo == "true")
        enviarMailComprobanteCliente($tipoDocumento, $mailCliente, $nombreCliente, $numeroAutorizacion, $numeroComprobante);

    return "<div class='alert text-center  alert-" . $class . "'>" .
        "<p>" . $msg . "</p>"
        . "</div>";
}

function generaDocumentoPDF($autorizacion, $tipo_documento, $pre_factura = false)
{
    if ($tipo_documento == "01")
        $dataComprobante = Comprobante::where([
            ['clave_acceso', isset($autorizacion->numeroAutorizacion) ? (String)$autorizacion->numeroAutorizacion : (String)$autorizacion->infoTributaria->claveAcceso],
            ['tipo_comprobante', '01']
        ])->select('id_envio')->first();

    if ($tipo_documento == "06")
        $dataComprobante = Comprobante::where([
            ['clave_acceso', isset($autorizacion->numeroAutorizacion) ? (String)$autorizacion->numeroAutorizacion : (String)$autorizacion->infoTributaria->claveAcceso],
            ['tipo_comprobante', '06']
        ])->join('detalle_guia_remision as dgr', 'comprobante.id_comprobante', 'dgr.id_comprobante')->select('id_comprobante_relacionado')->first();

    $data = [
        'autorizacion' => $autorizacion,
        'img_clave_acceso' => $pre_factura == false ? generateCodeBarGs1128((String)$autorizacion->numeroAutorizacion) : null,
        'obj_xml' => isset($autorizacion->comprobante) ? simplexml_load_string($autorizacion->comprobante) : $autorizacion,
        'numeroComprobante' => getDetallesClaveAcceso((String)$autorizacion->numeroAutorizacion, 'SERIE') . getDetallesClaveAcceso((String)$autorizacion->numeroAutorizacion, 'SECUENCIAL'),
        'detalles_envio' => $tipo_documento == "01" ? getEnvio($dataComprobante->id_envio)->detalles : "",
        'pedido' => $tipo_documento == "06" ? getComprobante($dataComprobante->id_comprobante_relacionado)->envio->pedido : ""
    ];
    if ($tipo_documento == "01")
        PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura', compact('data'))->save(env('PDF_FACTURAS') . (isset($autorizacion->numeroAutorizacion) ? $autorizacion->numeroAutorizacion : (String)$autorizacion->infoTributaria->claveAcceso) . ".pdf");
        PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura_cliente', compact('data'))->save(env('PDF_FACTURAS') . "cliente_" . (isset($autorizacion->numeroAutorizacion) ? $autorizacion->numeroAutorizacion : (String)$autorizacion->infoTributaria->claveAcceso) . ".pdf");
    if ($tipo_documento == "06")
        PDF::loadView('adminlte.gestion.comprobante.partials.pdf.guia', compact('data'))->save(env('PATH_PDF_GUIAS') . $autorizacion->numeroAutorizacion . ".pdf");

}

function enviarMailComprobanteCliente($tipoDocumento, $correoCliente, $nombreCliente, $nombreArchivo, $numeroComprobante, $preFactura = false, $correosExtra = false)
{
    //dd($correo_extra);
    if ($tipoDocumento == "01") {
        //$correoCliente
        Mail::to("pruebas-c26453@inbox.mailtrap.io")->send(new CorreoFactura($correoCliente, $nombreCliente, $nombreArchivo, $numeroComprobante, $preFactura, $correosExtra));
    }
}

function enviarMailComprobanteAgenciaCarga($tipoDocumento, $correoAgenciCarga, $nombreAgenciaCarga, $nombreArchivo, $numeroComprobante, $preFactura = false)
{
    if ($tipoDocumento == "01") {
        //$correoAgenciCarga
        Mail::to("pruebas-c26453@inbox.mailtrap.io")->send(new CorreoFactura($correoAgenciCarga, $nombreAgenciaCarga, $nombreArchivo, $numeroComprobante, $preFactura, $correosExtra = false));
    }
}

function getDetallesClaveAcceso($numeroAutorizacion, $detalle)
{
    switch ($detalle) {
        case 'RUC':
            $resultado = substr($numeroAutorizacion, 10, 13);
            break;
        case 'FECHA_EMISION':
            $resultado = substr($numeroAutorizacion, 0, 8);
            break;
        case 'ENTORNO':
            $resultado = substr($numeroAutorizacion, 22, 1);
            break;
        case 'SERIE':
            $resultado = substr($numeroAutorizacion, 24, 6);
            break;
        case 'SECUENCIAL':
            $resultado = substr($numeroAutorizacion, 30, 9);
            break;
        case 'TIPO_EMISION':
            $resultado = substr($numeroAutorizacion, 47, 1);
            break;
        case 'CODIGO_NUMERICO':
            $resultado = substr($numeroAutorizacion, 39, 8);
            break;
        case 'TIPO_COMPROBANTE':
            $resultado = substr($numeroAutorizacion, 8, 2);
            break;
        case 'PUNTO_ACCESO':
            $resultado = substr($numeroAutorizacion, 27, 3);
    }
    return $resultado;
}

function getSecuencial($tipoComprobante, $configuracionEmpresa)
{
    switch ($tipoComprobante) {
        case '01':
            $inicio_secuencial = $configuracionEmpresa->inicial_factura;
            break;
        case '06':
            $inicio_secuencial = $configuracionEmpresa->inicial_guia_remision;
            break;
        case '00':
            $inicio_secuencial = $configuracionEmpresa->inicial_lote;
            break;
    }
    $secuencial = $inicio_secuencial + 1;
    $cant_reg = Comprobante::where([
        ['comprobante.tipo_comprobante', $tipoComprobante],
        ['ficticio',false]
    ]);

    if ($tipoComprobante == "01")
        $cant_reg->join('envio as e', 'comprobante.id_envio', 'e.id_envio')
            ->join('pedido as p', 'e.id_pedido', 'p.id_pedido')
            ->where('p.id_configuracion_empresa', $configuracionEmpresa->id_configuracion_empresa);

    if ($cant_reg->count() > 0)
        $secuencial = $cant_reg->count() + $inicio_secuencial + 1;


    return str_pad($secuencial, 9, "0", STR_PAD_LEFT);
}

function getPuntoAcceso()
{
    return Usuario::where('id_usuario', Session::get('id_usuario'))->select('punto_acceso')->first()->punto_acceso;
}

function generateCodeBarGs1128($numero_autorizacion)
{
    $barcode = new BarcodeGenerator();
    $barcode->setText($numero_autorizacion);
    $barcode->setType(BarcodeGenerator::Code128);

    return $barcode->generate();
}

function getSubCarpetaArchivo($clave_acceso, $tipo_comprobante = false)
{
    ($clave_acceso != false)
        ? $tipo_comprobante = getDetallesClaveAcceso($clave_acceso, "TIPO_COMPROBANTE")
        : $tipo_comprobante = $tipo_comprobante;

    switch ($tipo_comprobante) {
        case '01':
            $carpeta = '/facturas/';
            break;
        case '06':
            $carpeta = '/guias_remision/';
            break;
    }
    return $carpeta;
}

function getDetallesVerdeByFecha($fecha)
{
    $listado = DB::table('detalle_clasificacion_verde')
        ->where('estado', '=', 1)
        ->where('fecha_ingreso', 'like', $fecha . '%')
        ->get();
    return $listado;
}

function getDetallesVerdeByFechaVariedad($fecha, $variedad)
{
    $listado = DB::table('detalle_clasificacion_verde')
        ->where('id_variedad', '=', $variedad)
        ->where('estado', '=', 1)
        ->where('fecha_ingreso', 'like', $fecha . '%')
        ->get();
    return $listado;
}

function convertToEstandar($ramos, $calibre)
{
    $estandar = getCalibreRamoEstandar()->nombre;
    $factor = round($calibre / $estandar, 2);
    return round($ramos * $factor);
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
function getCantidadRamosPedidosForCB($fecha, $variedad, $clasificacion_ramo, /*$envoltura, */
                                      $presentacion, $tallos_x_ramos, $longitud_ramo, $unidad_medida)
{
    $pedidos_ok = getIdPedidosValidosByRangoFecha($fecha, $fecha);
    $r = DB::table('pedido as p')
        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
        ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
        ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
        ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
        ->join('variedad as v', 'v.id_variedad', '=', 'dee.id_variedad')
        ->select(DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))
        ->whereIn('p.id_pedido', $pedidos_ok)
        ->where('p.estado', '=', 1)
        ->where('p.empaquetado', '=', 0)
        ->where('p.fecha_pedido', '=', $fecha)
        ->where('dee.id_variedad', '=', $variedad)
        ->where('dee.id_clasificacion_ramo', '=', $clasificacion_ramo)
        //->where('dee.id_empaque_e', '=', $envoltura)
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

function getIdPedidosValidosByRangoFecha($desde, $hasta)
{
    $query = DB::table('pedido as p')
        ->select('p.id_pedido')->distinct()
        ->where('p.estado', '=', 1)
        ->where('p.fecha_pedido', '>=', $desde)
        ->where('p.fecha_pedido', '<=', $hasta)
        ->get();
    $r = [];
    foreach ($query as $item)
        if (!getFacturaAnulada($item->id_pedido) && !in_array($item->id_pedido, $r))
            array_push($r, $item->id_pedido);

    return $r;
}

/* ============ Obtener Inventario en frío ==============*/
function getDisponibleInventarioFrio($variedad, $clasificacion_ramo, /*$envoltura, */
                                     $presentacion, $tallos_x_ramos, $longitud_ramo, $unidad_medida)
{
    $r = DB::table('inventario_frio as if')
        ->select(DB::raw('sum(if.disponibles) as cantidad'))
        ->where('estado', '=', 1)
        ->where('disponibilidad', '=', 1)
        ->where('id_variedad', '=', $variedad)
        ->where('id_clasificacion_ramo', '=', $clasificacion_ramo)
        //->where('id_empaque_e', '=', $envoltura)
        ->where('id_empaque_p', '=', $presentacion);

    if ($tallos_x_ramos != '')
        $r = $r->where('if.tallos_x_ramo', '=', $tallos_x_ramos);
    if ($longitud_ramo != '')
        $r = $r->where('if.longitud_ramo', '=', $longitud_ramo);
    else
        $r = $r->whereNull('if.longitud_ramo');
    if ($unidad_medida != '')
        $r = $r->where('if.id_unidad_medida', '=', $unidad_medida);

    if (count($r->get()) > 0)
        return $r->get()[0]->cantidad != '' ? $r->get()[0]->cantidad : 0;
    else
        return 0;
}

function getAgenciaCarga($idAgenciaCarga)
{
    return AgenciaCarga::find($idAgenciaCarga);
}

function getCantidadCajas($idPedido)
{
    return DetallePedido::where('id_pedido', $idPedido)
        ->join('cliente_pedido_especificacion as cpe', 'detalle_pedido.id_cliente_especificacion', 'cpe.id_cliente_pedido_especificacion')
        ->join('especificacion as esp', 'cpe.id_especificacion', 'esp.id_especificacion')
        ->join('especificacion_empaque as eemp', 'esp.id_especificacion', 'eemp.id_especificacion')->count();
}

function getOptionsPrecios($idCliente, $idEspecificacion)
{
    $data = ClientePedidoEspecificacion::where([
        ['id_cliente', $idCliente],
        ['id_especificacion', $idEspecificacion]
    ])->select('precio')->first();
    return explode("|", $data->precio);
}

function getDatosExportacion($id_detalle_pedido, $id_dato_exportacion)
{
    return DetallePedidoDatoExportacion::where([
        ['id_detalle_pedido', $id_detalle_pedido],
        ['id_dato_exportacion', $id_dato_exportacion]
    ])->first();
}

function getClienteEspecificacion($id_cliente, $id_especificacion)
{
    return ClientePedidoEspecificacion::where([
        ['id_cliente', $id_cliente],
        ['id_especificacion', $id_especificacion]
    ])->select('id_cliente_pedido_especificacion')->first();
}

function getSubmenusByTipo($tipo)
{
    return Submenu::All()->where('tipo', $tipo);
}

function porcentaje($a, $b, $tipo)
{
    if ($tipo == 1) { // a es el % de b
        $r = $b > 0 ? round(($a / $b) * 100, 2) : 0;
    }
    return $r;
}

function getMarcacion($id)
{
    return Marcacion::find($id);
}

function getColor($id)
{
    return Color::find($id);
}

function getColores()
{
    return Color::All()->where('estado', 1)->sortBy('nombre');
}

function getDatosExportacionCliente($idDetallePedido)
{
    return DetallePedidoDatoExportacion::where('id_detalle_pedido', $idDetallePedido)
        ->join('dato_exportacion as de', 'detallepedido_datoexportacion.id_dato_exportacion', 'de.id_dato_exportacion')->get();
}

function getEnvio($idEnvio)
{
    return Envio::find($idEnvio);
}

function getAgenciaCargaCliente($idCliente)
{
    return DB::table('cliente_agenciacarga as cac')
        ->join('agencia_carga as ac', 'cac.id_agencia_carga', 'ac.id_agencia_carga')
        ->where([
            ['cac.id_cliente', $idCliente],
            ['cac.estado', 1]
        ])->get();
}

function getTipoImpuesto($codigoImpuesto, $codigoPorcentajeIpuesto)
{
    return TipoImpuesto::where([
        ['codigo_impuesto', $codigoImpuesto],
        ['tipo_impuesto.codigo', $codigoPorcentajeIpuesto],
        ['tipo_impuesto.estado', 1]
    ])->join('impuesto as imp', 'tipo_impuesto.codigo_impuesto', 'imp.codigo')->first();
}

function getTipoIdentificacion($codigoIdentificacion)
{
    return TipoIdentificacion::where('codigo', $codigoIdentificacion)->first();
}

function getClasificacionRamo($idClasificacionRamo)
{
    return ClasificacionRamo::find($idClasificacionRamo);
}

function getAgenciaTransporte($idAgenciaTranporte)
{
    return Aerolinea::find($idAgenciaTranporte);
}

function getColoracion($id)
{
    return Coloracion::find($id);
}

function getEspecificacionEmpaque($id)
{
    return EspecificacionEmpaque::find($id);
}

function getFacturaClienteTercero($idEnvio)
{
    return FacturaClienteTercero::where('id_envio', $idEnvio)->first();
}

function getSecuenciaDespacho($configuracion_empresa)
{
    return $configuracion_empresa->inicial_despacho + Despacho::where('id_configuracion_empresa', $configuracion_empresa->id_configuracion_empresa)->count() + 1;
}

function getTransportista($idTransportista)
{
    return Transportista::find($idTransportista);
}

function getCamion($idCamion)
{
    return Camion::find($idCamion);
}

function getChofer($idChofer)
{
    return Conductor::find($idChofer);
}

function getCantDespacho($idPedido)
{
    return Despacho::join('detalle_despacho as dd', 'despacho.id_despacho', 'dd.id_despacho')->where([
        ['dd.id_pedido', $idPedido],
        ['despacho.estado', 1]
    ])->count();
}

function getImpuestosDesglosesFacturas($idComprobante)
{
    return DesgloseEnvioFactura::where('id_comprobante', $idComprobante)
        ->join('impuesto_desglose_envio_factura as idef', 'desglose_envio_factura.id_desglose_envio_factura', 'idef.id_desglose_envio_factura')
        ->get();
}

function getComprobante($idComprobante)
{
    return Comprobante::find($idComprobante);
}

function getDetalleDespacho($idPedido)
{
    return DetalleDespacho::where('id_pedido', $idPedido)->first();
}

function getComprobanteRelacionadoGuia($idComprobante)
{
    return DetalleGuiaRemision::where('id_comprobante', $idComprobante)->first();
}

function getComprobanteRelacionadFactura($idComprobante)
{
    return DetalleGuiaRemision::where('id_comprobante_relacionado', $idComprobante)->first();
}

function getDetallePedido($idDetallePedido)
{
    return DetallePedido::find($idDetallePedido);
}

function getDetalleEspecificacionEmpaque($idEspecificacionEmpaque)
{
    return DetalleEspecificacionEmpaque::find($idEspecificacionEmpaque);
}

function getDae($dae = null, $codigo = null)
{
    if ($dae != null)
        return CodigoDae::where('dae', $dae)->first();
    if ($codigo != null)
        return CodigoDae::where('codigo_dae', $codigo)->first();
}

function getAerolinea($idAerolinea)
{
    return Aerolinea::find($idAerolinea);
}

function getColoracionByDetPed($id_det_ped)
{
    return Coloracion::where('id_detalle_pedido', $id_det_ped)->get();
}

function getDatosExportacionByDetPed($id_detalle_pedido)
{
    return DetallePedidoDatoExportacion::where('id_detalle_pedido', $id_detalle_pedido)->get();
}

function getAreaCiclosByRango($semana_ini, $semana_fin, $variedad)
{
    $variedades = [];
    $semanas = [];
    for ($i = $semana_ini; $i <= $semana_fin; $i++) {
        $sem = Semana::All()->where('codigo', $i)->first();
        if ($sem != '')
            array_push($semanas, $sem);
    }

    if (count($semanas) > 0) {
        $desde = $semanas[0];
        $hasta = $semanas[count($semanas) - 1];

        foreach (getVariedades() as $var) {
            if ($var->id_variedad == $variedad || $variedad == 'T') {
                $ciclos = [];
                $query = DB::table('ciclo')
                    ->select('id_ciclo as id')->distinct()
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $var->id_variedad)
                    ->Where(function ($q) use ($desde, $hasta) {
                        $q->where('fecha_fin', '>=', $desde->fecha_inicial)
                            ->where('fecha_fin', '<=', $hasta->fecha_final)
                            ->orWhere(function ($q) use ($desde, $hasta) {
                                $q->where('fecha_inicio', '>=', $desde->fecha_inicial)
                                    ->where('fecha_inicio', '<=', $hasta->fecha_final);
                            })
                            ->orWhere(function ($q) use ($desde, $hasta) {
                                $q->where('fecha_inicio', '<', $desde->fecha_inicial)
                                    ->where('fecha_fin', '>', $hasta->fecha_final);
                            });
                    })
                    ->orderBy('fecha_inicio')
                    ->get();

                foreach ($query as $q) {
                    $flag = false;
                    $ciclo = Ciclo::find($q->id);
                    $areas = [];
                    foreach ($semanas as $sem) {
                        if (($ciclo->fecha_fin >= $sem->fecha_inicial && $ciclo->fecha_fin <= $sem->fecha_final) ||
                            ($ciclo->fecha_inicio >= $sem->fecha_inicial && $ciclo->fecha_inicio <= $sem->fecha_final) ||
                            ($ciclo->fecha_inicio < $sem->fecha_inicial && $ciclo->fecha_fin > $sem->fecha_final)) {
                            $exist_other = DB::table('ciclo')
                                ->select('*')
                                ->where('estado', '=', 1)
                                ->where('id_modulo', '=', $ciclo->id_modulo)
                                ->where('id_variedad', '=', $var->id_variedad)
                                ->where('id_ciclo', '!=', $ciclo->id_ciclo)
                                ->Where(function ($q) use ($sem) {
                                    $q->where('fecha_inicio', '>=', $sem->fecha_inicial)
                                        ->where('fecha_inicio', '<=', $sem->fecha_final);
                                })
                                ->get();
                            if (count($exist_other) > 0) {
                                $area = 0;
                            } else {
                                $area = $ciclo->area;
                                $flag = true;
                            }
                        } else
                            $area = 0;
                        array_push($areas, round($area, 2));
                    }
                    if ($flag)
                        array_push($ciclos, [
                            'ciclo' => $ciclo,
                            'areas' => $areas
                        ]);
                }
                array_push($variedades, [
                    'variedad' => $var,
                    'ciclos' => $ciclos
                ]);
            }
        }
    }
    return [
        'variedades' => $variedades,
        'semanas' => $semanas,
    ];
}

function getAreaActivaFromData($variedades, $semanas)
{
    $grafica = [];
    foreach ($variedades as $pos_var => $variedad) {
        $total_variedad = [];
        foreach ($semanas as $pos_sem => $semana)
            $total_variedad[] = 0;
        foreach ($variedad['ciclos'] as $ciclo)
            foreach ($ciclo['areas'] as $pos_area => $area)
                $total_variedad[$pos_area] += $area;
        array_push($grafica, [
            'variedad' => $variedad['variedad'],
            'valores' => $total_variedad
        ]);
    }

    $totales_semanas = [];
    foreach ($semanas as $pos_sem => $semana)
        $totales_semanas[] = 0;

    foreach ($variedades as $pos => $var) {
        $total_parcial = 0;
        foreach ($grafica[$pos]['valores'] as $pos_area => $area) {
            $totales_semanas[$pos_area] += $area;
            $total_parcial += $area;
        }
    }

    $total_parcial = 0;
    foreach ($totales_semanas as $valor)
        $total_parcial += $valor;

    return round(($total_parcial / 10000) / count($semanas), 2);
}

function getCiclosCerradosByRango($semana_ini, $semana_fin, $variedad, $by_semana = true)
{
    if ($by_semana) {
        $semana_ini = Semana::All()->where('codigo', $semana_ini)->first()->fecha_inicial;
        $semana_fin = Semana::All()->where('codigo', $semana_fin)->first()->fecha_final;
    }

    $ciclos_fin = Ciclo::All()
        ->where('estado', 1)
        ->where('activo', 0)
        ->where('fecha_fin', '>=', $semana_ini)
        ->where('fecha_fin', '<=', $semana_fin)
        ->sortBy('fecha_fin');

    if ($variedad != 'T')   // T => Todas
        $ciclos_fin = $ciclos_fin->where('id_variedad', $variedad);

    $ciclo = 0;
    $area_cerrada = 0;
    $tallos_cosechados = 0;

    foreach ($ciclos_fin as $c) {
        $area_cerrada += $c->area;
        $fin = date('Y-m-d');
        if ($c->fecha_fin != '')
            $fin = $c->fecha_fin;
        $ciclo += difFechas($fin, $c->fecha_inicio)->days;
        $tallos_cosechados += $c->getTallosCosechados();
    }

    return [
        'ciclos' => $ciclos_fin,
        'ciclo' => count($ciclos_fin) > 0 ? round($ciclo / count($ciclos_fin), 2) : 0,
        'area_cerrada' => $area_cerrada,
        'tallos_cosechados' => $tallos_cosechados,
    ];
}

function getCiclosCerradosByRangoVariedades($semana_ini, $semana_fin)
{
    $semana_ini = Semana::All()->where('codigo', $semana_ini)->first();
    $semana_fin = Semana::All()->where('codigo', $semana_fin)->first();

    $variedades = [];
    foreach (getVariedades() as $v) {
        $ciclos_fin = Ciclo::All()
            ->where('estado', 1)
            ->where('activo', 0)
            ->where('id_variedad', $v->id_variedad)
            ->where('fecha_fin', '>=', $semana_ini->fecha_inicial)
            ->where('fecha_fin', '<=', $semana_fin->fecha_final)
            ->sortBy('fecha_fin');

        $ciclo = 0;
        $area_cerrada = 0;

        foreach ($ciclos_fin as $c) {
            $area_cerrada += $c->area;
            $fin = date('Y-m-d');
            if ($c->fecha_fin != '')
                $fin = $c->fecha_fin;
            $ciclo += difFechas($fin, $c->fecha_inicio)->days;
        }

        array_push($variedades, [
            'variedad' => $v,
            'ciclos' => $ciclos_fin,
            'ciclo' => count($ciclos_fin) > 0 ? round($ciclo / count($ciclos_fin), 2) : 0,
            'area_cerrada' => $area_cerrada,
        ]);
    }

    return [
        'variedades' => $variedades
    ];
}

function getCosechaByRango($semana_ini, $semana_fin, $variedad)
{
    $semana_ini = Semana::All()->where('codigo', $semana_ini)->first();
    $semana_fin = Semana::All()->where('codigo', $semana_fin)->first();

    $query = DB::table('clasificacion_verde as v')
        ->select('v.fecha_ingreso as dia')->distinct()
        ->where('v.fecha_ingreso', '>=', $semana_ini->fecha_inicial)
        ->where('v.fecha_ingreso', '<=', $semana_fin->fecha_final)
        ->get();

    $ramos_estandar = 0;
    $tallos_cosechados = 0;
    $calibre = 0;

    $cant_verdes = 0;
    foreach ($query as $dia) {
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $dia->dia)->first();
        if ($verde != '') {
            if ($variedad == 'T') { // Todas las variedades
                $ramos_estandar += $verde->getTotalRamosEstandar();
                $tallos_cosechados += $cosecha->getTotalTallos();
                $calibre += $verde->getCalibre();
            } else {    // por variedad
                $ramos_estandar += $verde->getTotalRamosEstandarByVariedad($variedad);
                $tallos_cosechados += $cosecha->getTotalTallosByVariedad($variedad);
                $calibre += $verde->calibreByVariedad($variedad);
            }
            $cant_verdes++;
        }
    }
    return [
        'ramos_estandar' => $ramos_estandar,
        'tallos_cosechados' => $tallos_cosechados,
        'calibre' => $cant_verdes > 0 ? round($calibre / $cant_verdes, 2) : 0,
    ];
}

function getVentaByRango($semana_ini, $semana_fin, $variedad)
{
    $semana_ini = Semana::All()->where('codigo', $semana_ini)->first();
    $semana_fin = Semana::All()->where('codigo', $semana_fin)->first();

    $query = Pedido::All()
        ->where('fecha_pedido', '>=', $semana_ini->fecha_inicial)
        ->where('fecha_pedido', '<=', $semana_fin->fecha_final);

    $r = 0;
    foreach ($query as $p) {
        if ($variedad == 'T') {
            $r += $p->getPrecio();
        } else {
            $r += $p->getPrecioByVariedad($variedad);
        }
    }
    return [
        'valor' => $r
    ];
}

function getHistoricoVentaByMes($mes, $anno, $variedad = 'T')
{
    $r = DB::table('historico_ventas')
        ->select(DB::raw('sum(valor) as cant'))
        ->where('mes', $mes)
        ->where('anno', $anno);
    if ($variedad != 'T')
        $r = $r->where('id_variedad', $variedad);

    $r = $r->get()[0]->cant;

    return round($r, 2);
}

function getDetalleFactura($idComprobante)
{
    return DetalleFactura::where('id_comprobante', $idComprobante)->first();
}

function getDistribucion($idDistribucion)
{
    return Distribucion::find($idDistribucion);
}

function getCodigoArticuloVenture($idConfiguracionempresa = 1)
{
    $codigosVenture = [
        1 => [ // id_configracion_empresa Dasalflor
            '0011601010001' => 'GYP. MS. 250 Gr 50 cm',
            '0011601010002' => 'GYP. MS. 250 Gr 60 cm',
            '0011601010003' => 'GYP. MS. 250 Gr 75 cm',
            '0011601010004' => 'GYP. MS. 500 Gr 60 cm',
            '0011601010005' => 'GYP. MS. 500 Gr 75 cm',
            '0011601010006' => 'GYP. MS. 75 Gr 75 cm',
            '0011601010007' => 'GYP. MS. 40 Gr 50 cm',
            '0011601010008' => 'GYP. MS. 150 Gr 60 cm',
            '0011601010009' => 'GYP. MS. 750 Gr 75 cm',
            '0011601010010' => 'GYP. MS. 875 Gr 75 cm',
            '0011601010011' => 'GYP. MS. 1000 Gr 75 cm',
            '0011601010012' => 'GYP. MS. 1250 Gr 75 cm',
            '0011601010013' => 'GYP. MS. 250 Gr 65 cm',
            '0011601010014' => 'GYP. MS. 250 Gr 80 cm',
            '0011601010015' => 'GYP. MS. 200 Gr 60 cm',
            '0011601010016' => 'GYP. MS. 450 Gr 15 cm',
            '0011601010017' => 'GYP. MS. 650 Gr 80 cm',
            '0011601010018' => 'GYP. MS. 150 Gr 75 cm',
            '0011601010019' => 'GYP. MS. 100 Gr 75 cm',
            '0011601010020' => 'GYP. MS. 580 Gr 75 cm',
            '0011601010021' => 'GYP. MS. 180 Gr 75 cm',
            '0011601010022' => 'GYP. MS. 9 Tallos',
            '0011601010023' => 'GYP. MS. 680 Gr',
            '0011601010024' => 'GYP. MS. 350 Gr',
            '0011601010025' => 'GYP. MS. 5 Tallo',
            '0011601010026' => 'GYP. MS. 550 Gr',
            '0011601010027' => 'GYP. MS. 280 Gr',
            '0011601010028' => 'GYP. MS. 5 Tallos Corta 60 cm',
            '0011601010029' => 'GYP. MS. 220 Gr',
            '0011601010030' => 'GYP. MS. 2 Tallos larga 75 cm',
            '0011601010034' => 'GYP. MS. 3 Tallos Larga 75 cm',
            '0011601010037' => 'GYP. MS. 280 Gr Corta 60 cm',
            '0011601010040' => 'GYP. MS. 220 Gr Corta 60 cm',
            '0011601010041' => 'GYP. MS. 280 Gr Larga 15 Tallos',
            '0011601010042' => 'GYP. MS. 7 Tallos Larga',
            '0011601010043' => 'GYP. MS. 1500 Hojas',
            '0011601010044' => 'GYP. MS. 650 Gr Hojas 70 cm',
            '0011601010045' => 'GYP. MS.  850 Gr Hojas',
            '0011601010046' => 'GYP. MS. 175 Gr Larga',
            '0011601010047' => 'GYP. MS. 170 Gr Larga',
            '0011601010048' => 'GYP. MS. 520 Gr Gr Larga',
            '0011601010049' => 'GYP. MS. 120 Gr Larga',
            '0011601010050' => 'GYP. MS. 1 Tallo 60 cm',
            '0011601010051' => 'GYP. MS. 400 Gr Larga',
            '0011601010052' => 'GYP. MS. 400 Gr Corta',
            '0011601030001' => 'GYP. XLENCE  1250',
            '0011601030002' => 'GYP. XLENCE 1000',
            '0011601030003' => 'GYP. XLENCE 750',
            '0011601030004' => 'GYP. XLENCE 250 Corta',
            '0011601030005' => 'GYP. XLENCE 250 Larga',
            '0011601030006' => 'GYP. XLENCE 180 Larga',
            '0011601030007' => 'GYP. XLENCE 500 Larga',
            '0011601030008' => 'GYP. XLENCE 550',
            '0011601030009' => 'GYP. XLENCE 420',
            '0011601030010' => 'GYP. XLENCE 580',
            '0011601030011' => 'GYP. XLENCE 75',
            '0011601030012' => 'GYP. XLENCE 5 Tallos',
            '0011601030013' => 'GYP. XLENCE 2 Tallos',
            '0011601030014' => 'GYP. XLENCE 120',
            '0011601030015' => 'GYP. XLENCE 400 Larga',
            '0011601030016' => 'GYP. XLENCE 170',
            '0011601030017' => 'GYP. XLENCE 250',
            '0011601030018' => 'GYP. XLENCE 200',
            '0011601030019' => 'GYP. XLENCE 40 Gr',
            '0011601030020' => 'GYP. XLENCE 175',
            '0011601030021' => 'GYP. XLENCE 125',
            '0011601030022' => 'GYP. XLENCE 650 Hojas',
            '0011601030023' => 'GYP. XLENCE 400 Corta',
            '0011601030024' => 'GYP. XLENCE 220',
            '0011601030025' => 'GYP. XLENCE 850',
            '0011601030026' => 'GYP. XLENCE 550 Corta',
            '0011601030027' => 'GYP. XLENCE 875',
            '0011601030028' => 'GYP. XLENCE 200 Corta',
            '0011601030029' => 'GYP. XLENCE 4 Tallos',
            '0011601030030' => 'GYP. XLENCE 450',
            '001160401' => 'BROMELIAS 90 cm',
            '001160402' => 'BROMELIAS 80 cm',
            '001160403' => 'BROMELIAS 70 cm'
        ],
        2 => [ //id_configracion_empresa Intraescorp

        ]
    ];

    return $codigosVenture[$idConfiguracionempresa];
}

function getProductosVinculadosYuraVenture($idConfiguracionEmpresa, $tipo)
{
    return ProductoYuraVenture::where([
        ['id_configuracion_empresa', $idConfiguracionEmpresa],
        ['tipo', $tipo]
    ])->get();
}

function getCodigoVenturePresentacion($idPlanta, $idVariedad, $idClasificacionRamo, $clasificacionRamoIdUnidadMedida, $tallosXramos, $longitudRamo, $longitudRamoIdUnidadMedida, $idConfiguracionempresa, $tipo = "N")
{
    $prductosVinculados = getProductosVinculadosYuraVenture($idConfiguracionempresa, $tipo);
    foreach ($prductosVinculados as $prductoVinculado) {
        $datos = explode("|", $prductoVinculado->presentacion_yura);

        if ($datos[0] == $idPlanta && $datos[1] == $idVariedad && $datos[2] == $idClasificacionRamo && $datos[3] == $clasificacionRamoIdUnidadMedida && $datos[4] == $tallosXramos && $datos[5] == $longitudRamo && $datos[6] == $longitudRamoIdUnidadMedida) {
            return $prductoVinculado->codigo_venture;
        }
    }
}

function getPrductoVenture($codigoVenture)
{
    return getCodigoArticuloVenture()[$codigoVenture];
}

function getFacturaAnulada($idPedido)
{
    $success = false;
    if (isset(getPedido($idPedido)->envios[0]->comprobante) && getPedido($idPedido)->envios[0]->comprobante->estado === 6)
        $success = true;

    return $success;
}

function getLastPedido()
{
    return Pedido::all()->last();
}

function getTallosCosechadosByModSemVar($mod = null, $semana, $variedad)
{
    $sem = Semana::All()
        ->where('codigo', '=', $semana)
        ->where('id_variedad', '=', $variedad)
        ->first();

    if ($sem != '') {
        $r = DB::table('desglose_recepcion as dr')
            ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cantidad'))
            ->join('recepcion as r', 'dr.id_recepcion', '=', 'r.id_recepcion')
            ->join('cosecha as c', 'c.id_cosecha', '=', 'r.id_cosecha')
            ->where('c.estado', '=', 1)
            ->where('r.estado', '=', 1)
            ->where('dr.estado', '=', 1)
            ->where('dr.id_variedad', '=', $variedad)
            ->where('c.fecha_ingreso', '>=', $sem->fecha_inicial)
            ->where('c.fecha_ingreso', '<=', $sem->fecha_final);
        //->get()[0]->cantidad;

        if (isset($mod))
            $r->where('dr.id_modulo', '=', $mod);

        $r = $r->get()[0]->cantidad;
        return $r;
    } else
        return 0;
}

function getCalibreByRangoVariedad($desde, $hasta, $variedad)
{
    $query = DB::table('clasificacion_verde as v')
        ->select('v.fecha_ingreso as dia')->distinct()
        ->where('v.fecha_ingreso', '>=', $desde)
        ->where('v.fecha_ingreso', '<=', $hasta)
        ->get();

    $calibre = 0;

    $cant_verdes = 0;
    foreach ($query as $dia) {
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
        $value = 0;
        if ($verde != '') {
            if ($variedad == 'T') { // Todas las variedades
                $value = $verde->getCalibre();
                $calibre += $value;
            } else {    // por variedad
                $value = $verde->calibreByVariedad($variedad);
                $calibre += $value;
            }
        }
        if ($value > 0)
            $cant_verdes++;
    }
    return $cant_verdes > 0 ? round($calibre / $cant_verdes, 2) : 0;
}

function getCajasByRangoVariedad($desde, $hasta, $variedad)
{
    $query = DB::table('clasificacion_verde as v')
        ->select('v.fecha_ingreso as dia')->distinct()
        ->where('v.fecha_ingreso', '>=', $desde)
        ->where('v.fecha_ingreso', '<=', $hasta)
        ->get();

    $ramos = 0;

    $cant_verdes = 0;
    foreach ($query as $dia) {
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
        if ($verde != '') {
            if ($variedad == 'T') { // Todas las variedades
                $ramos += $verde->getTotalRamosEstandar();
            } else {    // por variedad
                $ramos += $verde->getTotalRamosEstandarByVariedad($variedad);
            }
            $cant_verdes++;
        }
    }
    return round($ramos / getConfiguracionEmpresa()->ramos_x_caja, 2);
}

function getLastSemanaByVariedad($variedad)
{
    return Semana::All()
        ->where('estado', 1)
        ->where('id_variedad', $variedad)
        ->sortBy('codigo')
        ->last();
}

function getObjSemana($codigo)
{
    return Semana::where('codigo', $codigo)->first();
}

function getRendimientoVerdeByRangoVariedad($desde, $hasta, $variedad)
{
    $query = DB::table('clasificacion_verde as v')
        ->select('v.fecha_ingreso as dia')->distinct()
        ->where('v.fecha_ingreso', '>=', $desde)
        ->where('v.fecha_ingreso', '<=', $hasta)
        ->get();

    $rend = 0;
    $cant_verde = 0;
    foreach ($query as $fecha) {
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $fecha->dia)->first();
        if ($verde != '') {
            if ($variedad == 'T') { // Todas las variedades
                $rend += $verde->getRendimiento();
            } else {    // por variedad
                $rend += $verde->getRendimientoByVariedad($variedad);
            }
            $cant_verde++;
        }
    }

    return $cant_verde > 0 ? round($rend / $cant_verde, 2) : 0;
}

function getPersonalVerdeByRango($desde, $hasta)
{
    $query = DB::table('clasificacion_verde as v')
        ->select('v.fecha_ingreso as dia')->distinct()
        ->where('v.fecha_ingreso', '>=', $desde)
        ->where('v.fecha_ingreso', '<=', $hasta)
        ->get();

    $personal = 0;
    $cant_verde = 0;
    foreach ($query as $fecha) {
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $fecha->dia)->first();
        if ($verde != '') {
            $personal += $verde->personal;
            $cant_verde++;
        }
    }

    return $cant_verde > 0 ? round($personal / $cant_verde, 2) : 0;
}

function getRendimientoCosechaByRangoVariedad($desde, $hasta, $variedad)
{
    $query = DB::table('cosecha as c')
        ->select('c.fecha_ingreso as dia')->distinct()
        ->where('c.fecha_ingreso', '>=', $desde)
        ->where('c.fecha_ingreso', '<=', $hasta)
        ->get();

    $rend = 0;
    $cant_cosecha = 0;
    foreach ($query as $fecha) {
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $fecha->dia)->first();
        if ($cosecha != '') {
            if ($variedad == 'T') { // Todas las variedades
                $rend += $cosecha->getRendimiento();
            } else {    // por variedad
                $rend += $cosecha->getRendimientoByVariedad($variedad);
            }
            $cant_cosecha++;
        }
    }

    return $cant_cosecha > 0 ? round($rend / $cant_cosecha, 2) : 0;
}

function getPersonalCosechaByRango($desde, $hasta)
{
    $query = DB::table('cosecha as c')
        ->select('c.fecha_ingreso as dia')->distinct()
        ->where('c.fecha_ingreso', '>=', $desde)
        ->where('c.fecha_ingreso', '<=', $hasta)
        ->get();

    $personal = 0;
    $cant_cosecha = 0;
    foreach ($query as $fecha) {
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $fecha->dia)->first();
        if ($cosecha != '') {
            $personal += $cosecha->personal;
            $cant_cosecha++;
        }
    }

    return $cant_cosecha > 0 ? round($personal / $cant_cosecha, 2) : 0;
}

function getCurrentDateDB()
{
    return DB::table('usuario')
        ->select(DB::raw('current_timestamp() as fecha_hora'))->distinct()
        ->get()[0]->fecha_hora;
}

function getIndicadores()
{
    return Indicador::orderBy('estado', 'desc')->orderBy('nombre', 'asc')->get();
}

function getIndicadorByName($nombre)
{
    return Indicador::All()
        ->where('estado', 1)
        ->where('nombre', $nombre)
        ->first();
}

function getTallosClasificadosByRangoVariedad($desde, $hasta, $variedad = 'T')
{
    $verdes = ClasificacionVerde::where('estado', 1)
        ->where('fecha_ingreso', '>=', $desde)
        ->where('fecha_ingreso', '<=', $hasta)
        ->orderBy('fecha_ingreso')
        ->get();

    $valor = 0;
    foreach ($verdes as $v) {
        if ($variedad == 'T')
            $valor += $v->total_tallos();
        else
            $valor += $v->tallos_x_variedad($variedad);
    }

    return $valor;
}

function getIntervalosIndicador($nombre)
{
    $indicador = Indicador::where('nombre', $nombre)->select('id_indicador')->first();
    $data = [];
    foreach ($indicador->intervalos->sortBy('desde') as $intervalo) {
        $obj = new stdClass();
        $obj->desde = $intervalo->desde;
        $obj->hasta = $intervalo->hasta;
        $obj->color = $intervalo->color;
        $data[] = $obj;
    }
    return $data;
}

function getColorByIndicador($nombre)
{
    $indicador = getIndicadorByName($nombre);
    $rangos = getIntervalosIndicador($nombre);
    if (count($rangos) > 0) {
        if ($indicador->valor < $rangos[0]->desde)
            return $rangos[0]->color;
        if ($indicador->valor > $rangos[count($rangos) - 1]->hasta)
            return $rangos[count($rangos) - 1]->color;
        foreach ($rangos as $rango) {
            if ($indicador->valor >= $rango->desde && $indicador->valor <= $rango->hasta)
                return $rango->color;
        }
    }
    return '#fff';
}

function getColorByIndicadorVariedad($nombre, $variedad)
{
    $indicador = getIndicadorByName($nombre);
    $valor = $indicador->getVariedad($variedad)->valor;
    $rangos = getIntervalosIndicador($nombre);
    if (count($rangos) > 0) {
        if ($valor < $rangos[0]->desde)
            return $rangos[0]->color;
        if ($valor > $rangos[count($rangos) - 1]->hasta)
            return $rangos[count($rangos) - 1]->color;
        foreach ($rangos as $rango) {
            if ($valor >= $rango->desde && $valor <= $rango->hasta)
                return $rango->color;
        }
    }
    return '#fff';
}

function getRendimientoVerdeByFechaMesa($fecha, $mesa)
{
    $tallos = DB::table('detalle_clasificacion_verde')
        ->select(DB::raw('sum(cantidad_ramos * tallos_x_ramos) as cant'))
        ->where('estado', 1)
        ->where('fecha_ingreso', 'like', $fecha . '%')
        ->where('mesa', $mesa)
        ->get()[0]->cant;
    $getCantidadHorasTrabajoVerde = getCantidadHorasTrabajoVerde($fecha);

    if ($getCantidadHorasTrabajoVerde > 0)
        return $tallos / $getCantidadHorasTrabajoVerde;
    return 0;
}

function getCantidadHorasTrabajoVerde($fecha)
{
    $getFechaHoraInicio = DB::table('detalle_clasificacion_verde')
        ->select(DB::raw('min(fecha_ingreso) as fecha'))
        ->where('estado', 1)
        ->where('fecha_ingreso', 'like', $fecha . '%')
        ->get();
    $FechaHoraInicio = '';
    if (count($getFechaHoraInicio) > 0)
        $FechaHoraInicio = $getFechaHoraInicio[0]->fecha . ':00';

    $getLastFechaClasificacion = DB::table('detalle_clasificacion_verde')
        ->select(DB::raw('max(fecha_ingreso) as fecha'))
        ->where('estado', 1)
        ->where('fecha_ingreso', 'like', $fecha . '%')
        ->get();
    $LastFechaClasificacion = '';
    if ($getLastFechaClasificacion[0]->fecha != '')
        $LastFechaClasificacion = $getLastFechaClasificacion[0]->fecha . ':00';

    if ($LastFechaClasificacion != '' && $FechaHoraInicio != '') {
        $r = difFechas($LastFechaClasificacion, $FechaHoraInicio);
        return round($r->h + ($r->i / 60), 2);
    }
    return 0;
}

function getCantidadHorasTrabajoBlanco($fecha)
{
    $getFechaHoraInicio = DB::table('inventario_frio')
        ->select(DB::raw('min(fecha_registro) as fecha'))
        ->where('estado', 1)
        ->where('fecha_ingreso', 'like', $fecha . '%')
        ->get();
    $FechaHoraInicio = '';
    if (count($getFechaHoraInicio) > 0)
        $FechaHoraInicio = $getFechaHoraInicio[0]->fecha;

    $getLastFechaClasificacion = DB::table('inventario_frio')
        ->select(DB::raw('max(fecha_registro) as fecha'))
        ->where('estado', 1)
        ->where('fecha_ingreso', 'like', $fecha . '%')
        ->get();
    $LastFechaClasificacion = '';
    if ($getLastFechaClasificacion[0]->fecha != '')
        $LastFechaClasificacion = $getLastFechaClasificacion[0]->fecha;

    if ($LastFechaClasificacion != '' && $FechaHoraInicio != '') {
        $r = difFechas($LastFechaClasificacion, $FechaHoraInicio);
        return round($r->h + ($r->i / 60), 2);
    }
    return 0;
}

function getNuevaCurva($curva, $inicio)
{
    $curva = explode('-', $curva);
    $new_curva = '';
    if (count($curva) > 1) {    // son dos semanas o más
        $configuracion = getConfiguracionEmpresa();
        $new_curva = $inicio;
        $dif = $curva[0] - $inicio;
        $curva[0] = $inicio;
        for ($i = count($curva) - 1; $i >= 0; $i--) {
            $last = $curva[$i] + $dif;
            $curva[$i] = $last;
            if ($last > $configuracion->proy_maximo_cosecha_fin) {  // supera el maximo permitido para la ultima semana
                $exc = $last - $configuracion->proy_maximo_cosecha_fin;
                if ($exc >= $configuracion->proy_minimo_cosecha) {  // hay que agregar una semana al final
                    $curva[$i] = $configuracion->proy_maximo_cosecha_fin;
                    array_push($curva, $exc);
                } else {    // no hay que agregar ninguna semana
                    $curva[$i] = $last;
                }
                break;
            } else if ($last < $configuracion->proy_minimo_cosecha) { // hay que hacer cero la ultima semana
                $dif = $last;
                $curva[$i] = 0;
            } else {
                break;
            }
        }
        foreach ($curva as $pos => $v) {
            if ($pos > 0 && $v > 0)
                $new_curva .= '-' . $v;
        }
    }
    return $new_curva;
}

function getRamosXCajaModificado($idDetPed,$idDetEspEmp){
    return DetalleEspecificacionEmpaqueRamosCaja::where([
       ['id_detalle_pedido',$idDetPed],
       ['id_detalle_especificacionempaque',$idDetEspEmp]
    ])->orderBy('id_detalle_especificacionempaque_ramos_x_caja','desc')->select('cantidad')->first();
}
