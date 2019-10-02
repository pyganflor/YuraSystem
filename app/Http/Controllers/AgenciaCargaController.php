<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use PHPUnit\Util\RegularExpressionTest;
use yura\Modelos\Rol;
use yura\Modelos\Submenu;
use yura\Modelos\AgenciaCarga;
use yura\Modelos\ClienteAgenciaCarga;
use yura\Modelos\CodigoVentureAgenciaCarga;
use Validator;
use DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;

class AgenciaCargaController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.agencias_carga.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'roles' => Rol::All(),
            'text' => ['titulo'=>'Configuración de agencias de carga','subtitulo'=>'módulo de administración']
        ]);
    }

    public function listAgenciasCarga(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('agencia_carga as ac');

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->Where('ac.nombre', 'like', '%' . $bus . '%')
                ->orWhere('ac.codigo', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('id_agencia_carga', 'desc')->paginate(20);

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.agencias_carga.forms.partials.list_agencia_carga',$datos);
    }

    public function createAgenciaCarga(Request $request){
        $request->id_agencia_carga != '' ? $dataAgencia = AgenciaCarga::find($request->id_agencia_carga): $dataAgencia = '';
        return view('adminlte.gestion.agencias_carga.forms.partials.add_agencia_carga',[
            'dataAgencia'=>$dataAgencia,
            'empresas' => getConfiguracionEmpresa(null, true)
        ]);
    }

    public function  storeAgenciaCarga(Request $request){

        $valida = Validator::make($request->all(), [
            'nombre' => 'required',
            'identificacion' => 'required',
        ]);

        if (!$valida->fails()) {

            empty($request->id_agencia_carga) ? $objAgenciaCarga = new AgenciaCarga : $objAgenciaCarga = AgenciaCarga::find($request->id_agencia_carga);
            $objAgenciaCarga->nombre = $request->nombre;
            $objAgenciaCarga->identificacion = $request->identificacion;
            $msg='';

            if($objAgenciaCarga->save()) {
                $model = AgenciaCarga::all()->last();
                bitacora('agencia_carga', $model->id_agencia_carga, 'I', 'Inserción satisfactoria de una nueva agencia de carga');

                if($request->has('id_cliente') && $request->id_cliente != "false"){
                    $objClienteAgenciaCarga = new ClienteAgenciaCarga;
                    $objClienteAgenciaCarga->id_cliente       = $request->id_cliente;
                    $objClienteAgenciaCarga->id_agencia_carga = $model->id_agencia_carga;

                    if($objClienteAgenciaCarga->save()){
                        $model_cliente_agencia_carga = ClienteAgenciaCarga::all()->last();
                        bitacora('cliente_agenciacarga', $model_cliente_agencia_carga->id_cliente_agencia_carga, 'I', 'Inserción satisfactoria de la asignación de una agencia de carga a un cliente');
                    }
                }

                if(isset($request->codigo_venture) && count($request->codigo_venture) > 0){
                    $datos = CodigoVentureAgenciaCarga::where('id_agencia_carga', $model->id_agencia_carga)->get();

                    foreach($request->codigo_venture as $codigoVenture){
                        $objCodigoVentureAgenciaCarga = new CodigoVentureAgenciaCarga;
                        $objCodigoVentureAgenciaCarga->id_agencia_carga = empty($request->id_agencia_carga) ? $model->id_agencia_carga : $request->id_agencia_carga;
                        $objCodigoVentureAgenciaCarga->id_configuracion_empresa = $codigoVenture['id_configuracion_empresa'];
                        $objCodigoVentureAgenciaCarga->codigo = $codigoVenture['codigo_venture'];
                        $objCodigoVentureAgenciaCarga->save();
                    }
                }

                if(isset($datos) && $datos->count() > 0)
                    foreach($datos as $d)
                        CodigoVentureAgenciaCarga::destroy($d->id_codigo_venture_agencia_carga);

                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado la agencia de carga '. $objAgenciaCarga->nombre .'  exitosamente</p>'
                    . '</div>';
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
        }else {
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

    public function actualizarEstadoAgenciaCarga(Request $request)
    {
        $model = AgenciaCarga::find($request->id_agencia_carga);
        if ($model != '') {
            $model->estado = $model->estado == 1 ? 0 : 1;
            if ($model->save()) {
                bitacora('agencia_carga', $model->id_agencia_carga, 'U', 'Actualización satisfactoria del estado de la agencia de carga'. $model->nombre);

                return [
                    'success' => true,
                    'estado' => $model->estado == 1 ? true : false,
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

    public function exportarAgenciasCarga(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excelAgenciasCarga($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte agencias de carga.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function excelAgenciasCarga($objPHPExcel, $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('agencia_carga as ac');
        //dd($listado);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->Where('ac.nombre', 'like', '%' . $bus . '%')
                ->orWhere('ac.codigo', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('ac.nombre', 'asc')->get();

        if (count($listado) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Agencias carga');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:B1');
            $objSheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:B1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Listado de agencias de carga');

            $objSheet->getCell('A3')->setValue('Nombre ');
            $objSheet->getCell('B3')->setValue('Código');


            $objSheet->getStyle('A3:B3')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A3:B3')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A3:B3')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            //--------------------------- LLENAR LA TABLA ---------------------------------------------
            for ($i = 0; $i < sizeof($listado); $i++) {

                $objSheet->getStyle('A' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objSheet->getStyle('B' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $objSheet->getCell('A' . ($i + 4))->setValue($listado[$i]->nombre);
                $objSheet->getCell('B' . ($i + 4))->setValue($listado[$i]->codigo);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);

        } else {
            return '<div>No se han encontrado coincidencias para exportar</div>';
        }
    }
}
