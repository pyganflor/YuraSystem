<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\CodigoDae;
use yura\Modelos\Pais;
use yura\Modelos\Submenu;
use DB;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;

class CodigoDaeController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.configuracion_facturacion.codigo_dae.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Código DAE', 'subtitulo' => 'módulo de parametros de facturación']
            ]);
    }

    public function buscar_codigo_dae(Request $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = CodigoDae::where('codigo_dae.estado', 1)
            ->join('pais as p', 'codigo_dae.codigo_pais', 'p.codigo');

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->Where('codigo_dae.codigo_pais', 'ilike', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('codigo_dae.anno', 'asc')->orderBy('codigo_dae.mes', 'desc')->paginate(30);

        $datos = [
            'listado' => $listado
        ];
        return view('adminlte.gestion.configuracion_facturacion.codigo_dae.partials.listado', $datos);
    }

    public function seleccionar_pais()
    {
        return view('adminlte.gestion.configuracion_facturacion.codigo_dae.partials.lista_paises', [
            'dataPaises' => Pais::orderBy('nombre','asc')->get()
        ]);
    }

    public function busqueda_pais_modal(Request $request)
    {
        $dataPais = Pais::where('nombre', 'like', $request->nombre . '%')->get();

        $html = '';
        foreach ($dataPais as $pais) {
            $html .= "<div class='col-md-4'>
                <input type='checkbox' id='codigo_pais_" . $pais->codigo . "' name='codigo_pais_" . $pais->codigo . "' onclick='selected(this)' value='" . $pais->codigo . "'>
                " . $pais->nombre . "
            </div>";
        }
        return $html;
    }
    public function pais(Request $request)

    {
        return Pais::where('codigo', $request->codigo)->first();
    }

    public function exportar_paises(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja_paises($objPHPExcel, $request);

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
            'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        echo json_encode($opResult);
    }

    public function excel_hoja_paises($objPHPExcel, $request)
    {

        if (count($request['arreglo']) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'codigos DAE');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:F1');
            $objSheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Código DAE por país');

            $objSheet->getCell('A2')->setValue('CÓDIGO PAÍS');
            $objSheet->getCell('B2')->setValue('NOMBRE PAIS');
            $objSheet->getCell('C2')->setValue('DAE');
            $objSheet->getCell('D2')->setValue('CÓDIGO DAE');
            $objSheet->getCell('E2')->setValue('MES');
            $objSheet->getCell('F2')->setValue('AÑO');

            $objSheet->getStyle('A2:F2')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A2:F2')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A2:F2')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            //--------------------------- LLENAR LA TABLA ---------------------------------------------
            for ($i = 0; $i < sizeof($request['arreglo']); $i++) {
                $objSheet->getCell('A' . ($i + 3))->setValue(substr($request['arreglo'][$i], 0, 16));
                $objSheet->getCell('B' . ($i + 3))->setValue(Pais::where('codigo', $request['arreglo'][$i])->select('nombre')->first()->nombre);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
            $objSheet->getColumnDimension('E')->setAutoSize(true);
            $objSheet->getColumnDimension('F')->setAutoSize(true);

        } else {
            return '<div>No se han seleccionado paises</div>';
        }
    }

    public function form_file_codigo_dae()
    {
        return view('adminlte.gestion.configuracion_facturacion.codigo_dae.form.upload_codigo_dae',
            [
                'empresas' => getConfiguracionEmpresa(null,true)
            ]);
    }

    public function importar_codigo_dae(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'file' => 'required',
            'id_configuracion_empresa' =>  'required'
        ],[
            'id_configuracion_empresa.required'=> 'Debe seleccionar la empresa a la que pertencen los códigos dae a subir',
            'file.required' => 'Debe seleccionar el archivo excel descargado con los códigos dae'
        ]);

        if (!$valida->fails()) {
            $msg = '';
            $document = PHPExcel_IOFactory::load($request->file);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            for ($i = 3; $i <= count($activeSheetData); $i++) {
                if ($activeSheetData[$i]['A'] !== null && $activeSheetData[$i]['B'] !== null && $activeSheetData[$i]['C'] !== null && $activeSheetData[$i]['D'] !== null && $activeSheetData[$i]['E'] !== null && $activeSheetData[$i]['F'] !== null) {
                    $existPais = Pais::where('codigo', $activeSheetData[$i]['A'])->count();
                    if ($existPais > 0) {
                        if (is_numeric($activeSheetData[$i]['E'])) {
                            $existRegistro = CodigoDae::where([
                                ['codigo_pais', $activeSheetData[$i]['A']],
                                ['codigo_dae', $activeSheetData[$i]['D']],
                                ['mes', $activeSheetData[$i]['E']],
                                ['anno', $activeSheetData[$i]['F']]
                            ]);
                            $existRegistro->count() > 0 ? $objCodigoDae = CodigoDae::find($existRegistro->first()->id_codigo_dae) : $objCodigoDae = new CodigoDae;

                            $objCodigoDae->codigo_pais = $activeSheetData[$i]['A'];
                            $objCodigoDae->dae = $activeSheetData[$i]['C'];
                            $objCodigoDae->codigo_dae = $activeSheetData[$i]['D'];
                            $objCodigoDae->mes = $activeSheetData[$i]['E'];
                            $objCodigoDae->anno = $activeSheetData[$i]['F'];
                            $objCodigoDae->id_configuracion_empresa = $request->id_configuracion_empresa;
                            if ($objCodigoDae->save()) {
                                $model = CodigoDae::all()->last();
                                $msg .= '<div class="alert alert-success text-center">' .
                                    '<p> Se ha guardado el código DAE para el país ' . $activeSheetData[$i]['B'] . '  exitosamente</p>'
                                    . '</div>';
                                bitacora('codigo_dae', $model->id_codigo_dae, 'I', 'Inserción satisfactoria de un nuevo codigo dae');
                            } else {
                                $msg .= '<div class="alert alert-danger text-center">' .
                                    '<p> Hubo un error al guardar el código DAE para el país ' . $activeSheetData[$i]['B'] . '  intente nuevamente</p>'
                                    . '</div>';
                            }
                        } else {
                            $msg .= '<div class="alert alert-danger text-center">' .
                                '<p> EL campo MES del código dae ' . $activeSheetData[$i]['D'] . ' no es númerico, debe estar entre el 01 y el 12, correspondiendo a los meses del año verifiquelo y cargue nuevamente el archivo excel</p>'
                                . '</div>';
                        }
                    } else {
                        $msg .= '<div class="alert alert-danger text-center">' .
                            '<p> EL codigo ' . $activeSheetData[$i]['A'] . ' no corresponde al país ' . $activeSheetData[$i]['B'] . ', Exporte nuevamente el archivo excel con este pais y no modifique ningún dato de la columna CÓDIGO PAÍS</p>'
                            . '</div>';
                    }
                } else {
                    $msg .= '<div class="alert alert-danger text-center">' .
                        '<p> EL codigo DAE ' . $activeSheetData[$i]['C'] . ' correspondiente al país ' . $activeSheetData[$i]['B'] . ' no pudo ser guardardo ya que hubo un campo vacío en alguna de las columnas de este registro en el archivo excel cargado, verifiquelo e intente cargarlo nuevamente</p>'
                        . '</div>';
                }
            }
        } else {
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
        return $msg;
    }

    public function descactivar_codigo(Request $request)
    {

        $objCodigoDae = CodigoDae::find($request->id_codigo);
        $objCodigoDae->estado = 0;
        if ($objCodigoDae->save()) {
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha actualizado exitosamente el estado del código</p>'
                . '</div>';
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Hubo un error al actualizar el estado, intente nuevamente</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];


    }
}
