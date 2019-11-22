<?php

namespace yura\Http\Controllers\CRM;

use DB;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Indicador;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ResumenSemanaCosecha;
use yura\Modelos\Submenu;
use yura\Http\Controllers\Indicadores\Proyecciones;

class CrmProyeccionesController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.crm.proyecciones.inicio',[
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Dashboard','subtitulo'=>'Proyecciones'],
            'indicador' => Indicador::whereIn('nombre',['DP1','DP2','DP3','DP4','DP5','DP6','DP7','DP8','DP9'])->select('valor')->get()
        ]);
    }

    public function desgloseIndicador(Request $request){

        $intervalo = Proyecciones::intervalosTiempo();
        switch ($request->param){
            case 'venta':
                $data = $this->dataVenta($intervalo);
                break;
            default:
                $data = $this->dataCosecha($intervalo);
                break;
        }

        return view('adminlte.crm.proyecciones.partials.modal_cosechado',[
            'data'=>$data,
            'tabla'=>$request->param
        ]);


    }

    public function desgloseCosecha4Semanas(Request $request){

        $intervalo = Proyecciones::intervalosTiempo();
        $dataGeneral = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalo['primeraSemanaFutura'],[$intervalo['cuartaSemanaFutura']]])->get();
        $dataAgrupada=[];

        foreach ($dataGeneral as $data)
            $dataAgrupada[$data->id_variedad][$data->codigo_semana]= $request->opcion == 'cajas' ?  $data->cajas_proyectadas : $data->tallos_proyectados;
        $data=[];
        foreach ($dataAgrupada as $idVariedad => $semana) {
            $data[]= [
                'label'=>getVariedad($idVariedad)->nombre,
                'data'=> $semana,
            ];
        }
        return $data;
    }

    public function desgloseCosechaVenta4Semanas(Request $request){
        //return view();
    }

    public function dataCosecha($intervalo){
        $dataGeneral = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalo['primeraSemanaFutura'],[$intervalo['cuartaSemanaFutura']]])->get();
        $dataAgrupada=[];

        foreach ($dataGeneral as $data)
            $dataAgrupada[$data->id_variedad][$data->codigo_semana]= ['cajas'=>$data->cajas_proyectadas ,'tallos'=> $data->tallos_proyectados];

        $data=[];
        foreach ($dataAgrupada as $idVariedad => $semana) {
            $data[]= [
                'variedad'=>getVariedad($idVariedad)->nombre,
                'data'=> $semana,
            ];
        }
        return $data;
    }

    public function dataVenta($intervalo){

        $dataGeneral =ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$intervalo['primeraSemanaFutura'],$intervalo['cuartaSemanaFutura']])->get();
        dump($dataGeneral);
        $dataAgrupada=[];
        foreach ($dataGeneral as $data)
            $dataAgrupada[$data->id_variedad][$data->codigo_semana]= ['cajas'=>$data->cajas_equivalentes ,'dinero'=> $data->valor];

        $data=[];
        foreach ($dataAgrupada as $idVariedad => $semana) {
            $data[]= [
                'variedad'=>getVariedad($idVariedad)->nombre,
                'data'=> $semana,
            ];
        }
        dump($data);
        return $data;
    }
}
