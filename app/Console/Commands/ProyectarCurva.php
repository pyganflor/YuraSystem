<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Jobs\RestaurarProyeccion;
use yura\Jobs\ResumenSemanaCosecha;
use yura\Jobs\CicloUpdateCampo;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Modelos\Ciclo;

class ProyectarCurva extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyectar:curva';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para proyectaar la curva de los modulos en rango de 6 a 11 semanas, segun las temperaturas';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "proyectar:curva" <<<<< ! >>>>>');

        $poda_siembra = ['P', 'S'];
        foreach (getVariedades() as $var) {
            foreach ($poda_siembra as $ps) {
                dump($var->siglas . ', ' . $ps);
                /* Consultar data */
                $sem_desde = getSemanaByDate(opDiasFecha('-', 70, date('Y-m-d')));
                $sem_pasada = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
                $query = Ciclo::where('estado', 1)
                    ->where('activo', 0)
                    ->where('id_variedad', $var->id_variedad)
                    ->where('poda_siembra', $ps)
                    ->where('fecha_fin', '>=', $sem_desde->fecha_inicial)
                    ->where('fecha_fin', '<=', $sem_pasada->fecha_final)
                    ->orderBy('semana_poda_siembra')
                    ->get();
                $ciclos = [];
                $max_dia = 0;
                $min_temp = count($query) > 0 ? $query[0]->getTemperaturaByFecha($query[0]->fecha_cosecha) : 0;     // **
                $max_temp = 0;
                $temp_prom = 0;
                foreach ($query as $item) {
                    $sem_curva = getSemanaByDate(opDiasFecha('+', ($item->semana_poda_siembra * 7), $item->fecha_inicio));
                    if ($sem_curva->codigo >= $sem_desde->codigo && $sem_curva->codigo <= $sem_pasada->codigo) {
                        $cosechas = DB::table('proyeccion_modulo_semana')
                            ->where('estado', 1)
                            ->where('tabla', 'C')
                            ->where('modelo', $item->id_ciclo)
                            ->where('cosechados', '>', 0)
                            ->where('semana', '>=', getSemanaByDate(opDiasFecha('-', 21, $sem_curva->fecha_inicial))->codigo)
                            ->where('tipo', 'T')
                            ->get();
                        if (count(explode('-', $item->curva)) == count($cosechas)) {
                            $total_cosechado = DB::table('proyeccion_modulo_semana')
                                ->select(DB::raw('sum(cosechados) as cant'))
                                ->where('estado', 1)
                                ->where('tabla', 'C')
                                ->where('modelo', $item->id_ciclo)
                                ->where('cosechados', '>', 0)
                                ->where('semana', '>=', getSemanaByDate(opDiasFecha('-', 21, $sem_curva->fecha_inicial))->codigo)
                                ->where('tipo', 'T')
                                ->get()[0]->cant;
                            $acumulado = $item->fecha_cosecha != '' ? $item->getTemperaturaByFecha($item->fecha_cosecha) : 0;
                            array_push($ciclos, [
                                'ciclo' => $item,
                                'cosechas' => $cosechas,
                                'total_cosechado' => $total_cosechado,
                                'acumulado' => $acumulado,
                            ]);
                            if (count(explode('-', $item->curva)) > $max_dia)
                                $max_dia = count(explode('-', $item->curva));
                            if ($min_temp > $acumulado)
                                $min_temp = $acumulado;
                            if ($max_temp < $acumulado)
                                $max_temp = $acumulado;
                            $temp_prom += $acumulado;
                        }
                    }
                }
                $temp_prom = count($ciclos) > 0 ? round($temp_prom / count($ciclos), 2) : 0;

                /* Procesar data */
                $array_prom = [];
                $array_prom_minimos = [];
                $array_prom_maximos = [];

                for ($i = 1; $i <= $max_dia; $i++) {
                    array_push($array_prom, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                    array_push($array_prom_minimos, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                    array_push($array_prom_maximos, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                }

                $semanas_prom = 0;
                foreach ($ciclos as $c) {
                    $modulo = $c['ciclo']->modulo;
                    $semanas_prom += count($c['cosechas']);

                    foreach ($c['cosechas'] as $pos => $v) {
                        $porcent = $c['total_cosechado'] > 0 ? round(($v->cosechados * 100) / $c['total_cosechado']) : 0;

                        $exist = true;
                        if ($porcent > 0) {
                            $array_prom[$pos]['valor'] += $porcent;
                            $array_prom[$pos]['positivos']++;
                            if ($c['acumulado'] >= $min_temp && $c['acumulado'] < $temp_prom) {    // por debajo del promedio
                                $array_prom_minimos[$pos]['valor'] += $porcent;
                                $array_prom_minimos[$pos]['positivos']++;
                            } else if ($c['acumulado'] > $temp_prom && $c['acumulado'] <= $max_temp) {  // por encima del promedio
                                $array_prom_maximos[$pos]['valor'] += $porcent;
                                $array_prom_maximos[$pos]['positivos']++;
                            }
                        }
                    }
                }

                /* Calcular promedio */
                $suma_total = 0;
                $array_prom_new = [];
                foreach ($array_prom as $pos => $v) {
                    $valor = $v['positivos'] > 0 ? round($v['valor'] / $v['positivos']) : 0;
                    $suma_total += $valor;
                    if ($pos == count($array_prom) - 1) {
                        if ($suma_total > 100) {
                            $new_valor = $valor - ($suma_total - 100);
                        } else if ($suma_total < 100) {
                            $new_valor = $valor + (100 - $suma_total);
                        }
                        array_push($array_prom_new, $new_valor >= 5 ? $new_valor : 0);
                        if ($new_valor < 5) {
                            $array_prom_new[$pos - 1] += $new_valor;
                        }
                    } else {
                        array_push($array_prom_new, $valor);
                    }
                }
                $curva_prom = count($array_prom_new) > 0 ? $array_prom_new[0] : '';
                foreach ($array_prom_new as $pos => $v)
                    if ($v > 0 && $pos > 0)
                        $curva_prom .= '-' . $v;

                /* Esstimaciones minimas */
                $suma_total = 0;
                $array_prom_minimos_new = [];
                foreach ($array_prom_minimos as $pos => $v) {
                    $valor = $v['positivos'] > 0 ? round($v['valor'] / $v['positivos']) : 0;
                    $suma_total += $valor;
                    if ($pos == count($array_prom) - 1) {
                        if ($suma_total > 100) {
                            $new_valor = $valor - ($suma_total - 100);
                        } else if ($suma_total < 100) {
                            $new_valor = $valor + (100 - $suma_total);
                        }
                        array_push($array_prom_minimos_new, $new_valor >= 5 ? $new_valor : 0);
                        if ($new_valor < 5) {
                            $array_prom_minimos_new[$pos - 1] += $new_valor;
                        }
                    } else {
                        array_push($array_prom_minimos_new, $valor);
                    }
                }
                $curva_prom_min = count($array_prom_minimos_new) > 0 ? $array_prom_minimos_new[0] : '';
                foreach ($array_prom_minimos_new as $pos => $v)
                    if ($v > 0 && $pos > 0)
                        $curva_prom_min .= '-' . $v;

                /* Estimaciones maximas */
                $suma_total = 0;
                $array_prom_maximos_new = [];
                foreach ($array_prom_maximos as $pos => $v) {
                    $valor = $v['positivos'] > 0 ? round($v['valor'] / $v['positivos']) : 0;
                    $suma_total += $valor;
                    if ($pos == count($array_prom) - 1) {
                        if ($suma_total > 100) {
                            $new_valor = $valor - ($suma_total - 100);
                        } else if ($suma_total < 100) {
                            $new_valor = $valor + (100 - $suma_total);
                        }
                        array_push($array_prom_maximos_new, $new_valor >= 5 ? $new_valor : 0);
                        if ($new_valor < 5) {
                            $array_prom_maximos_new[$pos - 1] += $new_valor;
                        }
                    } else {
                        array_push($array_prom_maximos_new, $valor);
                    }
                }
                $curva_prom_max = count($array_prom_maximos_new) > 0 ? $array_prom_maximos_new[0] : '';
                foreach ($array_prom_maximos_new as $pos => $v)
                    if ($v > 0 && $pos > 0)
                        $curva_prom_max .= '-' . $v;

                /* Guardar proy_curva en variedad */
                if ($ps == 'P')
                    $var->proy_curva_poda = $curva_prom;
                else
                    $var->proy_curva_siembra = $curva_prom;
                $var->save();

                /* Guardar proy_curva en los ciclos en un rango de 6 a 11 semanas */
                $ciclos = Ciclo::where('estado', 1)
                    ->where('activo', 1)
                    ->where('id_variedad', $var->id_variedad)
                    ->orderBy('fecha_inicio', 'desc')
                    ->where('poda_siembra', $ps)
                    ->get();    // ciclos activos
                foreach ($ciclos as $c) {
                    $num_sem = round(difFechas($c->fecha_fin, $c->fecha_inicio)->days / 7);
                    $num_sem = intval($num_sem);
                    if ($num_sem >= 6 && $num_sem <= 11 && $c->fecha_cosecha == '') {
                        dump($c->modulo->nombre . ' - ' . $num_sem . ', rango de temperatura promedio: ' . $curva_prom);
                        $nueva_curva = $curva_prom;

                        if ($nueva_curva != '') {
                            /* Actualizar inicio de cosecha */
                            /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA FINAL ====================== */
                            $semana_desde = $c->semana();
                            $semana_fin = getLastSemanaByVariedad($var->id_variedad);

                            CicloUpdateCampo::dispatch($c->id_ciclo, 'Curva', $nueva_curva)
                                ->onQueue('proy_cosecha/actualizar_curva');

                            if ($semana_desde != '') {
                                ProyeccionUpdateSemanal::dispatch($semana_desde->codigo, $semana_fin->codigo, $var->id_variedad, $c->id_modulo, 0)
                                    ->onQueue('proy_cosecha/actualizar_curva');

                                //RestaurarProyeccion::dispatch($c->id_modulo)->onQueue('proy_cosecha/actualizar_semana_cosecha');
                            }

                            /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                            ResumenSemanaCosecha::dispatch($semana_desde->codigo, $semana_fin->codigo, $var->id_variedad)
                                ->onQueue('proy_cosecha/actualizar_curva');
                            dump('ok');
                        }
                    }
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyectar:curva" <<<<< * >>>>>');

    }
}