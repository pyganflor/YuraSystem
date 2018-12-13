<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\Rol;
use yura\Modelos\AgenciaTransporte;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;
use Validator;
use DB;

class AgenciaTransporteController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.postcocecha.agencia_transporte.incio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'roles' => Rol::All(),
            'text' => ['titulo'=>'Configuración de agencias de transporte','subtitulo'=>'módulo de administración']
        ]);
    }

    public function list_agencias_transporte(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('agencia_transporte as at');
        //dd($listado);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->Where('at.nombre', 'like', '%' . $bus . '%')
                ->orWhere('at.tipo_agencia', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('at.nombre', 'asc')->paginate(20);

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.postcocecha.agencia_transporte.partials.list_agencias_transporte',$datos);
    }

    public function crear_agencias_transporte(Request $request){

        $request->id_agencia_transporte != '' ? $dataAgencia = AgenciaTransporte::find($request->id_agencia_transporte) : $dataAgencia = '';

        return view('adminlte.gestion.postcocecha.agencia_transporte.partials.forms.form_agencia_transporte',['dataAgencia'=>$dataAgencia]);
    }

    public function  store_agencias_transporte(Request $request){

        $valida = Validator::make($request->all(), [
            'nombre'                => 'required',
            'agencia_transporte' => 'required',
        ]);

        if (!$valida->fails()) {

            //dd($request->all());

            empty($request->id_agencia_transporte ) ? $objAgenciaTransporte = new AgenciaTransporte : $objAgenciaTransporte = AgenciaTransporte::find($request->id_agencia_transporte);

            $objAgenciaTransporte->nombre       = $request->nombre;
            $objAgenciaTransporte->tipo_agencia = $request->agencia_transporte;
            $msg='';

            if($objAgenciaTransporte->save()) {
                $model = AgenciaTransporte::all()->last();
                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado la agencia de transporte '. $objAgenciaTransporte->nombre .'  exitosamente</p>'
                    . '</div>';
                bitacora('agencia_tranposrte', $model->id_agencia_transporte, 'I', 'Inserción satisfactoria de una nueva agencia de transporte');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
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

    public function update_agencias_transporte(Request $request)
    {
        $model = AgenciaTransporte::find($request->id_agencia_transporte);
        if ($model != '') {
            $model->estado = $model->estado == 1 ? 0 : 1;
            if ($model->save()) {
                bitacora('agencia_transporte', $model->id_agencia_transporte, 'U', 'Actualización satisfactoria del estado de la agencia de transporte'. $model->nombre);

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

    public function excel_agencias_transporte(Request $request)
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

        $listado = DB::table('agencia_transporte as at');
        //dd($listado);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->Where('at.nombre', 'like', '%' . $bus . '%')
                ->orWhere('at.tipo_agencia', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('at.nombre', 'asc')->paginate(20);

        if (count($listado) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Agencias Transporte');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:B1');
            $objSheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:B1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Listado de agencias de transporte');

            $objSheet->getCell('A3')->setValue('Nombre ');
            $objSheet->getCell('B3')->setValue('Tipo de Agencia');


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
                $objSheet->getCell('B' . ($i + 4))->setValue($listado[$i]->tipo_agencia);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);

        } else {
            return '<div>No se han encontrado coincidencias para exportar</div>';
        }
    }
}
