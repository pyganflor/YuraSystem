<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
                foreach ($ciclos as $pos => $item) {
                    $modulo = $item['ciclo']->modulo;
                    $semana = $item['ciclo']->semana();
                    $cant_mon = 1;
                    if ($item['ini_curva'] > 0) {
                        $prom_ini_curva['valor'] += $item['ini_curva'];
                        $prom_ini_curva['cantidad']++;
                    }
                    $mon_actual = $item['mon_actual'] != '' ? $item['mon_actual'] : '';

                    $ant = 0;
                    foreach ($item['monitoreos'] as $pos_mon => $mon) {
                        $val = $mon->altura;
                        $crec_sem = round($val - $ant, 2);
                        $crec_dia = round($crec_sem / 7, 2);
                        $ant = $val;

                        if ($mon->altura > 0) {
                            $array_prom[$cant_mon - 1]['valor'] += $mon->altura;
                            $array_prom[$cant_mon - 1]['positivos']++;
                        }
                        $cant_mon++;
                    }

                    for ($i = $cant_mon; $i <= $num_semanas; $i++) {
                        $cant_mon++;
                    }
                }

                $array_crec_sem = [];
                $array_crec_dia = [];

                $sem_prom_ini_curva = '';
                if ($prom_ini_curva['cantidad'] > 0)
                    $sem_prom_ini_curva = round($prom_ini_curva['valor'] / $prom_ini_curva['cantidad']);
                $ant = 0;

                foreach ($array_prom as $pos_sem => $item) {
                    $val = $item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0;
                    array_push($array_crec_sem, round($val - $ant, 2));
                    $ant = $val;
                }

                foreach ($array_crec_sem as $pos_sem => $item) {
                    array_push($array_crec_dia, round($item > 0 ? $item / 7 : 0, 2));
                }

                /* Proyectar ciclos */
                $proy_sem_prom_ini_curva = [
                    'valor' => 0,
                    'cantidad' => 0,
                ];
                foreach ($ciclos as $pos => $item) {
                    $id_ciclo = $item['ciclo']->id_ciclo;
                    if ($id_ciclo == 1169) {
                        $mon_actual = $item['mon_actual'] != '' ? $item['mon_actual'] : '';
                        $last_sem = $mon_actual != '' ? $mon_actual->num_sem : '';
                        if ($last_sem >= $min_semanas && $last_sem <= 11) { // se trate de un ciclo en el rango de semanas que interesan
                            $valor = $mon_actual->altura;
                            dd($valor);
                        }
                    }
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyectar:inicio_cosecha" <<<<< * >>>>>');
    }
}