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
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;

class CajasPresentacionesController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.caja_presentacion.incio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo' => 'Cajas y presentaciones', 'subtitulo' => 'módulo clientes']
        ]);
    }

    public function buscar_empaque(Request $request)
    {
        //dd(Empaque::select('nombre','id_empaque','tipo')->where('tipo','P')->orWhere('tipo','C')->get());
        return view('adminlte.gestion.caja_presentacion.partials.listado', [
            'empaques' => Empaque::select('nombre','id_empaque','tipo','estado')->where('tipo','P')->orWhere('tipo','C')->orderBy('estado','desc')->get()
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
                $objEmpaque->id_configuracion_empresa = getConfiguracionEmpresa(null,false)->id_configuracion_empresa;
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
        $msg = '<div class="alert alert-danger text-center">
                <p> Hubo un error al actualizar el estado del empaque, intente nuevamente </p>
                 </div>';
        $objEmpaque = Empaque::find($request->id_empaque);
        $objEmpaque->estado = $request->estado == 1 ? 0 : 1;
        $request->estado == 1 ? $accion = "desactivado" : $accion = "activado";
        if($objEmpaque->save()){
            $objDetalleEmpaque = DetalleEmpaque::where('id_empaque',$request->id_empaque);
            $objDetalleEmpaque->update(["estado" => ($request->estado == 1 ? 0 : 1)]);
                $msg = '<div class="alert alert-success text-center">
                <p> El empaque ha sido '.$accion.' con exito</p>
                 </div>';
        }

        return $msg;
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
        header('Content-Disposition:inline;filename="detalles_empaques.xlsx"');
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
            ->join('unidad_medida as um','cr.id_unidad_medida','um.id_unidad_medida')
            ->select('v.siglas as siglas_variedad','cr.nombre as nombre_clasificacion_ramo','empaque.nombre as nombre_empaque','de.cantidad as cantidad_empaque','um.siglas as siglas_unidad_medida')->get();
        $cantRegistros = count($dataEmpaque);
        if ($cantRegistros > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Detalles empaque');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:D1');
            $objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->mergeCells('H1:I1');
            $objSheet->getStyle('H1:I1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('H1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('H1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->getStyle('K1:K2')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('K1:K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('K1:K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->getStyle('F1:F2')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('F1:F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('F1:F2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->getStyle('H2:I2')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('H2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('H2:I2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->getCell('H1')->setValue('VARIEDADES DISPONIBLES');
            $objSheet->getCell('H2')->setValue('Siglas');
            $objSheet->getCell('I2')->setValue('Nombre');

            $objSheet->getCell('F1')->setValue('EMPAQUES');
            $objSheet->getCell('F2')->setValue('Nombre');

            $objSheet->getCell('K1')->setValue('MEDIDAS DISPONIBLES');
            $objSheet->getCell('K2')->setValue('Siglas');

            $objSheet->getCell('A1')->setValue('DETALLE DE EMPAQUE');

            $objSheet->getCell('A2')->setValue('Nombre de empaque');
            $objSheet->getCell('B2')->setValue('Clasificación ramo');
            $objSheet->getCell('C2')->setValue('Variedad (siglas)');
            $objSheet->getCell('D2')->setValue('Cantidad de ramos');

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
            $variedades = Variedad::select('siglas','nombre')->get();
            foreach ($variedades as $key => $variedad) {
                $objSheet->getCell('H' . ($key + 3))->setValue($variedad->siglas);
                $objSheet->getCell('I' . ($key + 3))->setValue($variedad->nombre);
            }

            $empaques = Empaque::select('nombre')->where('tipo','C')->get();
            foreach ($empaques as $key => $empaque) {
                $objSheet->getCell('F' . ($key + 3))->setValue($empaque->nombre);
            }

            $medidas = UnidadMedida::select('siglas')->where('tipo','P')->get(); //->where('tipo','P') PARA PYGANFLOR QUE USA SOLO GRAMOS
            foreach ($medidas as $key => $medida) {
                $objSheet->getCell('K' . ($key + 3))->setValue($medida->siglas);
            }

            foreach ($dataEmpaque as $x => $empaque) {
                $objSheet->getCell('A' . ($x + 3))->setValue($empaque->nombre_empaque);
                $objSheet->getCell('B' . ($x + 3))->setValue($empaque->nombre_clasificacion_ramo."|".$empaque->siglas_unidad_medida);
                $objSheet->getCell('C' . ($x + 3))->setValue($empaque->siglas_variedad);
                $objSheet->getCell('D' . ($x + 3))->setValue($empaque->cantidad_empaque);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
            $objSheet->getColumnDimension('F')->setAutoSize(true);
            $objSheet->getColumnDimension('H')->setAutoSize(true);
            $objSheet->getColumnDimension('I')->setAutoSize(true);
            $objSheet->getColumnDimension('K')->setAutoSize(true);


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
            for ($i = 3; $i <= count($activeSheetData); $i++) {
                if(Variedad::where('siglas',$activeSheetData[$i]['C'])->count() == 0 ) {
                    $msg .= '<div class="alert alert-danger text-center">' .
                        '<p> La variedad ' . $activeSheetData[$i]['C'] . ' que se encuentra fila N# '. $i.' del archivo excel no ha sido creada aún</p>'
                        . '</div>';
                }else{

                    if(isset(explode("|",$activeSheetData[$i]['B'])[1])){
                        $unidadMedida = UnidadMedida::where('siglas',explode("|",$activeSheetData[$i]['B'])[1])->first();
                        if($unidadMedida == null){
                            $msg .= '<div class="alert alert-danger text-center">' .
                                '<p> La unidad de medida ' . explode("|",$activeSheetData[$i]['B'])[1] . ' que se encuentra fila N# '. $i.' del archivo excel no ha sido creada aún</p>'
                                . '</div>';
                        }else{

                            $dataDetalleEmpaque = Empaque::join('detalle_empaque as de','empaque.id_empaque','de.id_empaque')
                                ->join('variedad as v','de.id_variedad','v.id_variedad')
                                ->join('clasificacion_ramo as cr','de.id_clasificacion_ramo','cr.id_clasificacion_ramo')
                                ->where([
                                    ['empaque.nombre',$activeSheetData[$i]['A']],
                                    ['cr.nombre',explode("|",$activeSheetData[$i]['B'])[0]],
                                    ['v.siglas',$activeSheetData[$i]['C']]
                                ])->select('de.id_detalle_empaque','empaque.id_empaque','cr.id_clasificacion_ramo','v.id_variedad','v.nombre as nombre_variedad','cr.nombre as nombre_clasificacion_ramo','empaque.nombre as nombre_empaque')->first();

                            if($dataDetalleEmpaque != null){

                                $objDetalleEmpaque = DetalleEmpaque::find($dataDetalleEmpaque->id_detalle_empaque);
                                $objDetalleEmpaque->update(["cantidad"=>$activeSheetData[$i]['D']]);
                                $msg .= '<div class="alert alert-success text-center">' .
                                    '<p> La cantidad del empaque ' . $activeSheetData[$i]['A'] ." ". explode("|",$activeSheetData[$i]['B'])[0]. " ". explode("|",$activeSheetData[$i]['B'])[1] ." ". $activeSheetData[$i]['C'].' fue modificada con éxito</p>'
                                    . '</div>';

                            }else{

                                $existEmpaque = Empaque::where('nombre',$activeSheetData[$i]['A'])->select('id_empaque')->first();
                                if($existEmpaque != null){
                                    $id_empaque = $existEmpaque->id_empaque;
                                }else{
                                    $objEmpaque = new Empaque;
                                    $objEmpaque->nombre = $activeSheetData[$i]['A'];
                                    $objEmpaque->id_configuracion_empresa = getConfiguracionEmpresa(null,false)->id_configuracion_empresa;
                                    $objEmpaque->save();
                                    $id_empaque = Empaque::all()->last()->id_empaque;
                                }

                                $existClasificacionRamo = ClasificacionRamo::where('nombre',explode("|",$activeSheetData[$i]['B'])[0])->first();

                                if($existClasificacionRamo != null){
                                    $idClasificacionRamo = $existClasificacionRamo->id_clasificacion_ramo;
                                }else {
                                    $objClasificacionRamo = new ClasificacionRamo;
                                    $objClasificacionRamo->id_unidad_medida = $unidadMedida->id_unidad_medida;
                                    $objClasificacionRamo->nombre = explode("|", $activeSheetData[$i]['B'])[0];
                                    $objClasificacionRamo->id_configuracion_empresa = getConfiguracionEmpresa(null,false)->id_configuracion_empresa;
                                    $objClasificacionRamo->save();
                                    $idClasificacionRamo = ClasificacionRamo::all()->last()->id_clasificacion_ramo;
                                }

                                $dataVariedad = Variedad::where('siglas',$activeSheetData[$i]['C'])->select('id_variedad')->first();

                                $existDetalleEmpaque = DetalleEmpaque::where([
                                    ['id_empaque',$id_empaque],
                                    ['id_variedad',$dataVariedad->id_variedad],
                                    ['id_clasificacion_ramo',$idClasificacionRamo],
                                    ['cantidad',$activeSheetData[$i]['D']]
                                ])->count();

                                if($existDetalleEmpaque == 0) {
                                    $objDetalleEmpaque = new DetalleEmpaque;
                                    $objDetalleEmpaque->id_empaque = $id_empaque;
                                    $objDetalleEmpaque->id_clasificacion_ramo = $idClasificacionRamo;
                                    $objDetalleEmpaque->id_variedad = $dataVariedad->id_variedad;
                                    $objDetalleEmpaque->cantidad = $activeSheetData[$i]['D'];
                                    $objDetalleEmpaque->save();
                                    $msg .= '<div class="alert alert-success text-center">' .
                                        '<p> El empaque ' . $activeSheetData[$i]['A'] . " " . explode("|", $activeSheetData[$i]['B'])[0] . " " . explode("|", $activeSheetData[$i]['B'])[1] . " " . $activeSheetData[$i]['C'] . ' fue agregado con éxito</p>'
                                        . '</div>';
                                }else{
                                    $msg .= '<div class="alert alert-danger text-center">' .
                                                '<p> El detalle empaque '." ".$activeSheetData[$i]['A']." ".explode("|", $activeSheetData[$i]['B'])[0]." ".$activeSheetData[$i]['C'] .' ya esta creado </p>'
                                            .'</div>';
                                }
                            }
                        }
                    }else{
                        $msg .= '<div class="alert alert-danger text-center">' .
                                '<p> La clasificacion ramo que se encuentra fila N# '. $i.' del archivo excel debe cumplir el formato `Clasificación|unidad de medida`</p>'
                            . '</div>';
                    }
                }
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

    public function detalle_empaque(Request $request){
        return view('adminlte.gestion.caja_presentacion.partials.form_detalle_empaque',[
            'dataDetalleEmpaque' => Empaque::join('detalle_empaque as de','empaque.id_empaque','de.id_empaque')
                ->join('variedad as v','de.id_variedad','v.id_variedad')
                ->join('clasificacion_ramo as cr','de.id_clasificacion_ramo','cr.id_clasificacion_ramo')
                ->join('unidad_medida as um','cr.id_unidad_medida','um.id_unidad_medida')
                ->where('empaque.id_empaque',$request->id_empaque)
                ->select('de.id_detalle_empaque','de.cantidad','empaque.id_empaque','cr.id_clasificacion_ramo','v.id_variedad','um.id_unidad_medida','um.siglas as siglas_unidad_medida','v.nombre as nombre_variedad','cr.nombre as nombre_clasificacion_ramo','empaque.nombre as nombre_empaque')->get(),
            'variedades' => Variedad::get(),
            'nombreEmpaque' =>  Empaque::where('id_empaque',$request->id_empaque)->select('nombre')->first(),
            //'clasificacionRamo' => ClasificacionRamo::select('clasificacion_ramo.nombre','clasificacion_ramo.id_clasificacion_ramo')->distinct()->get()
        ]);
    }

    public function store_detalle_empaque(Request $request){

        $valida = Validator::make($request->all(), [
            'arrData' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            foreach($request->arrData as $data){

                $dataDetalleEmpaque = DetalleEmpaque::where('id_detalle_empaque',$data['id_detalle_empaque'])->first();

                $variedad = DetalleEmpaque::find($data['id_detalle_empaque']);
                $variedad->update([
                    'id_variedad'=>$data['id_variedad'],
                    'cantidad' => $data['cantidad_ramos']
                ]);

            }



            $dataDetalleEmpaque = Empaque::join('detalle_empaque as de','empaque.id_empaque','de.id_empaque')
                ->join('variedad as v','de.id_variedad','v.id_variedad')
                ->join('clasificacion_ramo as cr','de.id_clasificacion_ramo','cr.id_clasificacion_ramo')
                ->where([
                    ['empaque.id_empaque',$request->id_empaque],
                    ['cr.id_clasificacion_ramo',$request->id_clasificacion_ramo],
                    ['v.siglas',$request->id_variedad]
                ])->select('de.id_detalle_empaque','empaque.id_empaque','cr.id_clasificacion_ramo','v.id_variedad','v.nombre as nombre_variedad','cr.nombre as nombre_clasificacion_ramo','empaque.nombre as nombre_empaque')->first();

            if($dataDetalleEmpaque != null){

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
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return  $msg;
    }

    public function delete_detalle_empaque(Request $request){

        $detalleEmpaque = DetalleEmpaque::destroy($request->id_detalle_empaque);
        return '<div class="alert alert-success text-center">' .
            '<p> Se ha eliminado el detalle de empaque exitosamente</p>'
            . '</div>';
    }
}
