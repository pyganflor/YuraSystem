<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Jobs\CicloUpdateCampo;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Jobs\RestaurarProyeccion;
use yura\Jobs\ResumenSemanaCosecha;
use yura\Modelos\Ciclo;
use yura\Modelos\Monitoreo;
use yura\Modelos\Variedad;

class ProyectarInicioCosecha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyectar:inicio_cosecha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para proyectaar el inicio de cosecha de los modulos en rango de 6 a 11 semanas, segun las alturas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ini = date('Y-m-d H:i:s');
        Log::info('<<<<< ! >>>>> Ejecutando comando "proyectar:inicio_cosecha" <<<<< ! >>>>>');

        $poda_siembra = ['P', 'S'];
        $num_semanas = 18;
        $min_semanas = 6;
        foreach (getVariedades() as $var) {
            foreach ($poda_siembra as $ps) {
                /* Consultar data */
                $query = Ciclo::where('estado', 1)
                    ->where('activo', 1)
                    ->where('id_variedad', $var->id_variedad)
                    ->orderBy('fecha_inicio', 'desc')
                    ->where('poda_siembra', $ps)
                    ->get();    // ciclos activos

                $ciclos = [];
                foreach ($query as $item) {
                    $monitoreos = Monitoreo::where('estado', 1)
                        ->where('id_ciclo', $item->id_ciclo)
                        ->where('num_sem', '<=', $num_semanas)
                        ->orderBy('num_sem')
                        ->get();
                    $ini_curva = '';
                    if ($item->getTallosCosechados(15) > 0)
                        $ini_curva = $item->semana_poda_siembra;
                    $mon_actual = '';
                    for ($i = count($monitoreos) - 1; $i >= 0; $i--) {
                        if ($monitoreos[$i]->altura > 0) {
                            $mon_actual = $monitoreos[$i];
                            break;
                        }
                    }
                    array_push($ciclos, [
                        'ciclo' => $item,
                        'monitoreos' => $monitoreos,
                        'ini_curva' => $ini_curva,
                        'mon_actual' => $mon_actual,
                        'data' => [
                            'last_sem' => '',
                            'ini_curva' => '',
                            'monitoreos' => [],
                            'crec_sem' => [],
                            'crec_dia' => [],
                        ]
                    ]);
                }

                /* Obtener promedios */
                $array_prom = [];
                for ($i = 1; $i <= $num_semanas; $i++) {
                    array_push($array_prom, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                }

                $prom_ini_curva = [
                    'valor' => 0,
                    'cantidad' => 0,
                ];
                $matriz = [];
                foreach ($ciclos as $pos => $item) {
                    $modulo = $item['ciclo']->modulo;
                    $cant_mon = 1;
                    if ($item['ini_curva'] > 0) {
                        $prom_ini_curva['valor'] += $item['ini_curva'];
                        $prom_ini_curva['cantidad']++;
                    }
                    $mon_actual = $item['mon_actual'] != '' ? $item['mon_actual'] : '';
                    //$item['data']['last_sem'] = $mon_actual != '' ? $mon_actual->num_sem : '';
                    $last_sem = $mon_actual != '' ? $mon_actual->num_sem : '';
                    //$item['data']['ini_curva'] = $item['ini_curva'];
                    $ini_curva = $item['ini_curva'];

                    $data_monitoreos = [];
                    $data_crec_sem = [];
                    $data_crec_dia = [];
                    $ant = 0;
                    foreach ($item['monitoreos'] as $pos_mon => $mon) {
                        $val = $mon->altura;
                        $crec_sem = round($val - $ant, 2);
                        $crec_dia = round($crec_sem / 7, 2);
                        $ant = $val;

                        //array_push($item['data']['monitoreos'], $mon->altura);
                        array_push($data_monitoreos, $mon->altura);
                        //array_push($item['data']['crec_sem'], $crec_sem);
                        array_push($data_crec_sem, $crec_sem);
                        //array_push($item['data']['crec_dia'], $crec_dia);
                        array_push($data_crec_dia, $crec_dia);

                        if ($mon->altura > 0) {
                            $array_prom[$cant_mon - 1]['valor'] += $mon->altura;
                            $array_prom[$cant_mon - 1]['positivos']++;
                        }
                        $cant_mon++;
                    }

                    for ($i = $cant_mon; $i <= $num_semanas; $i++) {
                        //array_push($item['data']['monitoreos'], '');
                        array_push($data_monitoreos, '');

                        $cant_mon++;
                    }

                    array_push($matriz, [
                        'last_sem' => $last_sem,
                        'ini_curva' => $ini_curva,
                        'monitoreos' => $data_monitoreos,
                        'crec_sem' => $data_crec_sem,
                        'crec_dia' => $data_crec_dia,
                    ]);
                }

                $array_crec_sem = [];
                $array_crec_dia = [];
                $array_crec_sem_dia = [];

                $sem_prom_ini_curva = '';
                if ($prom_ini_curva['cantidad'] > 0)
                    $sem_prom_ini_curva = round($prom_ini_curva['valor'] / $prom_ini_curva['cantidad']);
                $ant = 0;

                $array_prom_sem = [];
                foreach ($array_prom as $pos_sem => $item) {
                    $val = $item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0;
                    array_push($array_prom_sem, $val);
                    array_push($array_crec_sem, round($val - $ant, 2));
                    $ant = $val;
                }

                foreach ($array_crec_sem as $pos_sem => $item) {
                    //array_push($array_crec_sem, $item > 0 ? $item : 0);
                    array_push($array_crec_dia, round($item > 0 ? $item / 7 : 0, 2));
                }

                foreach ($array_crec_dia as $pos_sem => $item) {
                    array_push($array_crec_sem_dia, $item);
                }

                /* Proyectar ciclos */
                $proy_sem_prom_ini_curva = [
                    'valor' => 0,
                    'cantidad' => 0,
                ];
                dump('Se van a proyectar estos módulos: (' . $var->siglas . ' - ' . $ps . ')');
                foreach ($ciclos as $pos => $item) {
                    $id_ciclo = $item['ciclo']->id_ciclo;
                    $mon_actual = $item['mon_actual'] != '' ? $item['mon_actual'] : '';
                    $last_sem = $matriz[$pos]['last_sem'];
                    if ($last_sem >= $min_semanas && $last_sem <= 11 && $item['ciclo']->fecha_cosecha == '') { // se trate de un ciclo en el rango de semanas que interesan
                        //$valor = $mon_actual->altura;
                        $valor = $matriz[$pos]['monitoreos'][$last_sem - 1];
                        $crec_sem = $matriz[$pos]['crec_sem'][$last_sem - 1];
                        $crec_dia = $matriz[$pos]['crec_dia'][$last_sem - 1];


                        $prom_sem = $array_prom_sem[$last_sem - 1];
                        $crec_sem_prom = $array_crec_sem[$last_sem - 1];
                        $crec_dia_prom = $array_crec_dia[$last_sem - 1];

                        $dif_dia = $crec_dia - $crec_dia_prom;
                        $dif_sem = $crec_sem - $crec_sem_prom;
                        $dif_dia = abs($dif_dia);
                        $dif_sem = abs($dif_sem);

                        $resultado = $dif_sem > 0 ? ($dif_dia * 7) / $dif_sem : 0;
                        $resultado = intval($resultado);
                        $direccion = getSigno($valor - $prom_sem);
                        $sem_prom_ini_curva = intval($sem_prom_ini_curva);

                        if ($direccion >= 0) {  // adelantar en el tiempo
                            $nuevo_inicio_cosecha = $sem_prom_ini_curva - $resultado;
                        } else {    // atrasar en el tiempo
                            $nuevo_inicio_cosecha = $sem_prom_ini_curva + $resultado;
                        }

                        dump($item['ciclo']->modulo->nombre . ' -> ini:' . $nuevo_inicio_cosecha);

                        /* Actualizar inicio de cosecha */
                        if ($nuevo_inicio_cosecha > 11) {
                            /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA FINAL ====================== */
                            $semana_desde = $item['ciclo']->semana();
                            $semana_fin = getLastSemanaByVariedad($var->id_variedad);

                            CicloUpdateCampo::dispatch($id_ciclo, 'SemanaCosecha', $nuevo_inicio_cosecha)
                                ->onQueue('proy_cosecha/actualizar_semana_cosecha');

                            if ($semana_desde != '') {
                                ProyeccionUpdateSemanal::dispatch($semana_desde->codigo, $semana_fin->codigo, $var->id_variedad, $item['ciclo']->id_modulo, 0)
                                    ->onQueue('proy_cosecha/actualizar_semana_cosecha');

                                //RestaurarProyeccion::dispatch($item['ciclo']->id_modulo)->onQueue('proy_cosecha/actualizar_semana_cosecha');
                            }

                            /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                            ResumenSemanaCosecha::dispatch($semana_desde->codigo, $semana_fin->codigo, $var->id_variedad)
                                ->onQueue('proy_cosecha/actualizar_semana_cosecha');

                            dump('> 11 semanas: ok');
                        }

                        $proy_sem_prom_ini_curva['valor'] += $nuevo_inicio_cosecha;
                        $proy_sem_prom_ini_curva['cantidad']++;
                    }
                }

                /* Calcular inicio de cosecha proyectado */
                $num_sem_proy = $proy_sem_prom_ini_curva['cantidad'] > 0 ? intval(round($proy_sem_prom_ini_curva['valor'] / $proy_sem_prom_ini_curva['cantidad'])) : 0;
                if ($ps == 'P')
                    $var->proy_inicio_cosecha_poda = $num_sem_proy;
                else
                    $var->proy_inicio_cosecha_siembra = $num_sem_proy;
                $var->save();
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyectar:inicio_cosecha" <<<<< * >>>>>');
    }
}