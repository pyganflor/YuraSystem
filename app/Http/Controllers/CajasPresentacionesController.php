<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Submenu;
use yura\Modelos\Empaque;
use yura\Modelos\DetalleEmpaque;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use yura\Modelos\Variedad;

class CajasPresentacionesController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.caja_presentacion.incio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo' => 'Cajas y presentaciones', 'subtitulo' => 'módulo administración']
        ]);
    }

    public function buscar_empaque(Request $request)
    {
        //dd(Empaque::select('nombre','id_empaque','tipo')->where('tipo','P')->orWhere('tipo','C')->get());
        return view('adminlte.gestion.caja_presentacion.partials.listado', [
            'empaques' => Empaque::select('nombre','id_empaque','tipo')->where('tipo','P')->orWhere('tipo','C')->get()
        ]);
    }

    public function form_add_empaque(Request $request){
        return view('adminlte.gestion.caja_presentacion.partials.form_add_empaque',[
            'empaque' => Empaque::where('id_empaque',$request->id_empaque)->select('nombre','id_empaque')->first()
        ]);
    }

    public function store_empaque(Request $request){

        $valida = Validator::make($request->all(), [
            'nombre' => 'required'
        ]);
        $msg = '';
        if (!$valida->fails()) {

            if(empty($request->id_empaque)){
                $objEmpaque = new Empaque;
                $objEmpaque->id_configuracion_empresa = 1;
                $objEmpaque->tipo = $request->tipo;
                $letra = "I";
                $accion = "Inserción";
            }else{
                $objEmpaque = Empaque::find($request->id_empaque);
                $letra = "U";
                $accion = "Actualización";
            }
            $objEmpaque->nombre = $request->nombre;
            $msg='';

            if($objEmpaque->save()) {
                $model = Empaque::all()->last();
                bitacora('empaque', empty($request->id_empaque) ? $model->id_empaque : $request->id_empaque , $letra, $accion.' satisfactoria de una nueva agencia de carga');
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el emapaque '. $objEmpaque->nombre .'  exitosamente</p>'
                    . '</div>';
            } else {
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
        }else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg .= '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return $msg;
    }

    public function update_estado_empaque(Request $request){
        $objEmpaque = DetalleEmpaque::find($request->id_empaque);
        $objEmpaque->update(['estado',$request->estado == 1 ? 0 : 1]);
        $request->estado == 1 ? $accion = "Desactivados" : $accion = "Activados";
        return '<div class="alert alert-success text-center">
        <p> Los detalles del empaque han sido '.$accion.' con exito</p>
         </div>';
    }

    public function exportar_detalle_empaque(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja_detalle_empaque($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="codigos_DAE.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $opResult = array(
            'status' => 1,
            'data'=>"data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
        );
        echo json_encode($opResult);
    }

    public function excel_hoja_detalle_empaque($objPHPExcel, $request){

        $dataEmpaque = Empaque::join('detalle_empaque as de','empaque.id_empaque','de.id_empaque')
            ->join('variedad as v','de.id_variedad','v.id_variedad')
            ->join('clasificacion_ramo as cr','de.id_clasificacion_ramo','cr.id_clasificacion_ramo')
            ->select('v.nombre as nombre_variedad','cr.nombre as nombre_clasificacion_ramo','empaque.nombre as nombre_empaque','de.cantidad as cantidad_empaque')->get();
        $cantRegistros = count($dataEmpaque);
        if ($cantRegistros > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Detalles empaque');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:D1');
            $objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('DETALLE DE EMPAQUE');

            $objSheet->getCell('A2')->setValue('Nombre de empaque');
            $objSheet->getCell('B2')->setValue('Clasificación ramo');
            $objSheet->getCell('C2')->setValue('Variedad');
            $objSheet->getCell('D2')->setValue('Cantidad');

            $objSheet->getStyle('A2:D2')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A2:D2')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A2:D2')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            //--------------------------- LLENAR LA TABLA ---------------------------------------------

            if($cantRegistros > 0){
                for ($i = 0; $i < $cantRegistros; $i++) {
                    $objSheet->getCell('A' . ($i + 3))->setValue($dataEmpaque[$i]->nombre_empaque);
                    $objSheet->getCell('B' . ($i + 3))->setValue($dataEmpaque[$i]->nombre_clasificacion_ramo);
                    $objSheet->getCell('C' . ($i + 3))->setValue($dataEmpaque[$i]->nombre_variedad);
                    $objSheet->getCell('D' . ($i + 3))->setValue($dataEmpaque[$i]->cantidad_empaque);
                }
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);

        } else {
            return '<div>No se han seleccionado paises</div>';
        }
    }

    public function form_file_detalle_empaque(){
        return view('adminlte.gestion.caja_presentacion.partials.form_upload_detalle_empaque');
    }

    public function importar_detalle_empaque(Request $request){

        $valida = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if (!$valida->fails()) {
            $msg = '';
            $document = PHPExcel_IOFactory::load($request->file);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);
            dd($activeSheetData);
            for ($i = 3; $i <= count($activeSheetData); $i++) {

                $dataDetalleEmpaque = Empaque::join('detalle_empaque as de','empaque.id_empaque','de.id_empaque')
                    ->join('variedad as v','de.id_variedad','v.id_variedad')
                    ->join('clasificacion_ramo as cr','de.id_clasificacion_ramo','cr.id_clasificacion_ramo')
                    ->where([
                        ['empaque.nombre',$activeSheetData[$i]['A']],
                        ['cr.nombre',$activeSheetData[$i]['B']],
                        ['v.nombre',$activeSheetData[$i]['C']]
                    ])->select('de.id_detalle_empaque','empaque.id_empaque','cr.id_clasificacion_ramo','v.id_variedad','v.nombre as nombre_variedad','cr.nombre as nombre_clasificacion_ramo','empaque.nombre as nombre_empaque')->first();

                if(count($dataDetalleEmpaque) > 0){
                    $objDetalleEmpaque = DetalleEmpaque::find($dataDetalleEmpaque->id_detalle_empaque);
                    $objDetalleEmpaque->update(["cantidad"=>$activeSheetData[$i]['D']]);

                    $objEmpaque = Empaque::find($dataDetalleEmpaque->id_empaque);
                    $objEmpaque->update(["nombre"=> $activeSheetData[$i]['A']]);

                    $objVariedad = Variedad::find($dataDetalleEmpaque->id_variedad);
                    $objVariedad->update(["nombre"=> $activeSheetData[$i]['C']]);

                    $objClasificacionRamo = ClasificacionRamo::find($dataDetalleEmpaque->id_clasificacion_ramo);
                    $objClasificacionRamo->update(["nombre"=>$activeSheetData[$i]['B']]);

                }else{

                    $objEmpaque = new Empaque;
                    $objEmpaque->nombre = $activeSheetData[$i]['A'];
                    $objEmpaque->id_configuracion_empresa = 1;
                    $objEmpaque->save();
                    $modelEmpaque = Empaque::all()->last();

                    $objVariedad = new Variedad;
                    $objVariedad->nombre = $activeSheetData[$i]['C'];
                    $objVariedad->siglas = 'GLX';


                    $objDetalleEmpaque = new DetalleEmpaque;
                    $objDetalleEmpaque->id_empaque = $modelEmpaque->id_empaque;

                }

                /*if($activeSheetData[$i]['A'] !== null && $activeSheetData[$i]['B'] !== null && $activeSheetData[$i]['C'] !== null && $activeSheetData[$i]['D'] !== null) {
                    $existPais = Pais::where('codigo',$activeSheetData[$i]['A'])->count();
                    if($existPais > 0){
                        if(is_numeric($activeSheetData[$i]['E'])){
                            $existRegistro = CodigoDae::where([
                                ['codigo_pais',$activeSheetData[$i]['A']],
                                ['codigo_dae',$activeSheetData[$i]['D']],
                                ['mes',$activeSheetData[$i]['E']],
                                ['anno',$activeSheetData[$i]['F']]
                            ]);
                            $existRegistro->count() > 0 ? $objCodigoDae = CodigoDae::find($existRegistro->first()->id_codigo_dae) : $objCodigoDae = new CodigoDae;

                            $objCodigoDae->codigo_pais = $activeSheetData[$i]['A'];
                            $objCodigoDae->dae         = $activeSheetData[$i]['C'];
                            $objCodigoDae->codigo_dae  = $activeSheetData[$i]['D'];
                            $objCodigoDae->mes         = $activeSheetData[$i]['E'];
                            $objCodigoDae->anno        = $activeSheetData[$i]['F'];

                            if($objCodigoDae->save()){
                                $model = CodigoDae::all()->last();
                                $msg .= '<div class="alert alert-success text-center">' .
                                    '<p> Se ha guardado el código DAE para el país ' .$activeSheetData[$i]['B'] . '  exitosamente</p>'
                                    . '</div>';
                                bitacora('codigo_dae', $model->id_codigo_dae, 'I', 'Inserción satisfactoria de un nuevo codigo dae');
                            }else{
                                $msg .= '<div class="alert alert-danger text-center">' .
                                    '<p> Hubo un error al guardar el código DAE para el país ' .$activeSheetData[$i]['B'] . '  intente nuevamente</p>'
                                    . '</div>';
                            }
                        }else{
                            $msg .= '<div class="alert alert-danger text-center">' .
                                '<p> EL campo MES del código dae ' .$activeSheetData[$i]['D'] . ' no es númerico, debe estar entre el 01 y el 12, correspondiendo a los meses del año verifiquelo y cargue nuevamente el archivo excel</p>'
                                . '</div>';
                        }
                    }else{
                        $msg .= '<div class="alert alert-danger text-center">' .
                            '<p> EL codigo ' .$activeSheetData[$i]['A'] . ' no corresponde al país '.$activeSheetData[$i]['B'].', Exporte nuevamente el archivo excel con este pais y no modifique ningún dato de la columna CÓDIGO PAÍS</p>'
                            . '</div>';
                    }
                }else{
                    $msg .= '<div class="alert alert-danger text-center">' .
                        '<p> EL codigo DAE ' .$activeSheetData[$i]['C'] . ' correspondiente al país '. $activeSheetData[$i]['B'].' no pudo ser guardardo ya que hubo un campo vacío en alguna de las columnas de este registro en el archivo excel cargado, verifiquelo e intente cargarlo nuevamente</p>'
                        . '</div>';
                }*/
            }
        }
        else {
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
        return  $msg;
    }
}
