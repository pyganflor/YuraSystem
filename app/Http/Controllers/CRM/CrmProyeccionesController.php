<?php

namespace yura\Http\Controllers\CRM;

use Carbon\Carbon;
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
            'indicador' => Indicador::whereIn('nombre',['DP1','DP2','DP3','DP4','DP5','DP6','DP7','DP8','DP9'])->select('valor')->get(),
            'semana'=>getSemanaByDate(now()->addDays(7)->toDateString())->codigo
        ]);
    }

    public function desgloseIndicador(Request $request){

        $intervalo = Proyecciones::intervalosTiempo();
        switch ($request->param){
            case 'venta':
                $data = $this->dataVenta($intervalo);
                $first ='Cajas';
                $iconFirst='fa-cube';
                $second='Dinero';
                $iconSecond='fa-usd';
                break;
            case 'venta a 3 meses':
                $data = $this->desgloseVenta3Meses();
                $first ='Dinero a 3 próximos meses';
                $iconFirst='fa-usd';
                $second=false;
                $iconSecond=false;
                break;
            default:
                $data = $this->dataCosecha($intervalo);
                $first ='Cajas';
                $iconFirst='fa-cube';
                $second='Tallos';
                $iconSecond='fa-pagelines';
                break;
        }

        return view('adminlte.crm.proyecciones.partials.modal_cosechado',[
            'data'=>$data,
            'tabla'=>$request->param,
            'first'=>$first,
            'iconFirst'=>$iconFirst,
            'second'=>$second,
            'iconSecond'=>$iconSecond
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

    public function desgloseVenta4Semanas(Request $request){
        $intervalo = Proyecciones::intervalosTiempo();
        $dataGeneral =ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$intervalo['primeraSemanaFutura'],$intervalo['cuartaSemanaFutura']])
            ->select(
                'codigo_semana',
                'id_variedad',
                DB::raw('sum(cajas_equivalentes) as cajas_equivalentes'),
                DB::raw('sum(valor) as valor'))
            ->groupBy('codigo_semana','id_variedad')->get();

        $dataAgrupada=[];
        foreach ($dataGeneral as $data)
            $dataAgrupada[$data->id_variedad][$data->codigo_semana]= $request->opcion == 'cajas' ?  $data->cajas_equivalentes : $data->valor;
        $data=[];
        foreach ($dataAgrupada as $idVariedad => $semana) {
            $data[]= [
                'label'=>getVariedad($idVariedad)->nombre,
                'data'=> $semana,
            ];
        }
        return $data;
    }

    public function desgloseVenta3Meses(){

        $indicadorDP5 = Indicador::where('nombre','DP5')->select('valor')->first();
        $data= explode("|",$indicadorDP5->valor);

        return [
            explode(":",$data[0])[0] =>explode(":",$data[0])[1],
            explode(":",$data[1])[0] =>explode(":",$data[1])[1],
            explode(":",$data[2])[0] =>explode(":",$data[2])[1],
        ];

        // return Proyecciones::proyeccionVentaFutura3Meses(true);
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

        $dataGeneral =ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$intervalo['primeraSemanaFutura'],$intervalo['cuartaSemanaFutura']])
            ->select(
                'codigo_semana',
                'id_variedad',
                DB::raw('sum(cajas_equivalentes) as cajas_equivalentes'),
                DB::raw('sum(valor) as valor'))
            ->groupBy('codigo_semana','id_variedad')->get();

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
        return $data;
    }

    public function chartInicio(Request $request){

        $fechaFutura =Carbon::parse(now())->addDays(7)->addMonths($request->rango)->toDateString();
        $primeraSemanaFutura = Proyecciones::intervalosTiempo()['primeraSemanaFutura'];
        $ultimaSemanaFutura = getSemanaByDate($fechaFutura)->codigo;
        $data=[];

        $dataProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$primeraSemanaFutura,$ultimaSemanaFutura])
            ->select('id_variedad','codigo_semana',
                DB::raw('sum(cajas_equivalentes) as cajas_equivalentes'),
                DB::raw('sum(valor) as valor')
            )->groupBy('codigo_semana','id_variedad');

        if(isset($request->variedad) && $request->variedad!="T")
            $dataProyeccionVentaSemanalReal->where('id_variedad',$request->variedad);

        $dataProyeccionVentaSemanalReal =$dataProyeccionVentaSemanalReal->get();

        foreach ($dataProyeccionVentaSemanalReal as $proyeccionVentaSemanalReal) {
            $dataAgrupada[$proyeccionVentaSemanalReal->id_variedad][$proyeccionVentaSemanalReal->codigo_semana]=[
                'valor'=>number_format($proyeccionVentaSemanalReal->valor,2,".",""),
                'cajas'=>number_format($proyeccionVentaSemanalReal->cajas_equivalentes,2,".","")
            ];
        }
        foreach ($dataAgrupada as $idVariedad => $semana) {
            $data[]= [
                'variedad'=>getVariedad($idVariedad)->nombre,
                'data'=> $semana,
            ];

        }

        return $data;

    }
}
