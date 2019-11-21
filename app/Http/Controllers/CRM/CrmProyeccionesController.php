<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Indicador;
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

        return view('adminlte.crm.proyecciones.partials.modal_cosechado');
    }

    public function desgloseTallos4Semanas(){

        $intervalo = Proyecciones::intervalosTiempo();
        $dataGeneral = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalo['primeraSemanaFutura'],[$intervalo['cuartaSemanaFutura']]])->get();
        $dataAgrupada=[];

        foreach ($dataGeneral as $data)
            $dataAgrupada[$data->id_variedad][$data->codigo_semana]=$data->tallos_proyectados;

        $data=[];
        foreach ($dataAgrupada as $idVariedad => $semana) {
            $data[]= [
                'label'=>getVariedad($idVariedad)->nombre,
                'data'=> 0,
                'borderColor'=> 'black',
                'borderWidth'=> 2,
                'fill'=> false,
            ];
        }
        return $data;
    }
}
