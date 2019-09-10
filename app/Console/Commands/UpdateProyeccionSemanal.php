<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Modulo;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;

class UpdateProyeccionSemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:update_semanal {semana_desde=0} {semana_hasta=0} {variedad=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar la tabla proyeccion_modulo_semana con los datos de la semana actual';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "proyeccion:update_semanal" <<<<< ! >>>>>');


        $sem_parametro_desde = $this->argument('semana_desde');
        $sem_parametro_hasta = $this->argument('semana_hasta');
        $var_parametro = $this->argument('variedad');

        if ($sem_parametro_desde <= $sem_parametro_hasta) {

            if ($sem_parametro_desde != 0)
                $semana_desde = Semana::All()->where('estado', 1)->where('codigo', $sem_parametro_desde)->first();
            else
                $semana_desde = getSemanaByDate(date('Y-m-d'));
            if ($sem_parametro_hasta != 0)
                $semana_hasta = Semana::All()->where('estado', 1)->where('codigo', $sem_parametro_hasta)->first();
            else
                $semana_hasta = getSemanaByDate(date('Y-m-d'));

            Log::info('SEMANA PARAMETRO DESDE: ' . $sem_parametro_desde . ' => ' . $semana_desde->codigo);
            Log::info('SEMANA PARAMETRO HASTA: ' . $sem_parametro_hasta . ' => ' . $semana_hasta->codigo);
            if ($var_parametro != 0)
                Log::info('VARIEDAD PARAMETRO: ' . $var_parametro . ' => ' . getVariedad($var_parametro)->siglas);
            else
                Log::info('VARIEDAD PARAMETRO: ' . $var_parametro . ' => TODAS');

            $array_semanas = [];
            $semanas = [];
            for ($i = $semana_desde->codigo; $i <= $semana_hasta->codigo; $i++) {
                $semana = Semana::All()
                    ->where('estado', 1)
                    ->where('codigo', $i)->first();
                if ($semana != '')
                    if (!in_array($semana->codigo, $array_semanas)) {
                        array_push($array_semanas, $semana->codigo);
                        array_push($semanas, $semana);
                    }
            }

            $modulos = Modulo::All()->where('estado', 1)->where('area', '>', 0);
            $variedades = Variedad::All()->where('estado', 1);
            if ($var_parametro != 0)
                $variedades = $variedades->where('id_variedad', $var_parametro);

            Log::info('<!> Se han encontrado ' . count($modulos) * count($variedades) * count($semanas) . ' casos a procesar <!>');
            foreach ($modulos as $mod) {
                foreach ($variedades as $pos_var => $var) {
                    foreach ($semanas as $pos_sem => $semana) {
                        $data = $mod->getDataBySemana($semana, $var->id_variedad, '2000-01-01', 'I', 'T');

                        $proy = ProyeccionModuloSemana::All()->where('estado', 1)
                            ->where('id_modulo', $mod->id_modulo)
                            ->where('semana', $semana->codigo)
                            ->where('id_variedad', $var->id_variedad)
                            ->first();
                        if ($proy == '')
                            $proy = new ProyeccionModuloSemana();

                        $proy->id_modulo = $mod->id_modulo;
                        $proy->id_variedad = $var->id_variedad;
                        $proy->semana = $semana->codigo;
                        $proy->tipo = $data['tipo'];
                        $proy->info = $data['info'];
                        $proy->cosechados = $data['cosechado'];
                        $proy->proyectados = $data['proyectados'];
                        $proy->tabla = $data['tabla'];

                        if (in_array($data['tipo'], ['S', 'P', 'T', 'Y'])) {
                            if ($data['tabla'] == 'C') {
                                $proy->plantas_iniciales = $data['ciclo']->plantas_iniciales;
                                $proy->plantas_actuales = $data['ciclo']->plantas_actuales();
                                $proy->fecha_inicio = $data['ciclo']->fecha_inicio;
                                $proy->activo = $data['ciclo']->activo;
                                $proy->area = $data['ciclo']->area;
                                $proy->tallos_planta = $data['ciclo']->conteo;
                                $proy->curva = $data['ciclo']->curva;
                                $proy->poda_siembra = $data['ciclo']->poda_siembra;
                                $proy->desecho = $data['ciclo']->desecho;
                                $proy->semana_poda_siembra = $data['ciclo']->semana_poda_siembra;
                                $proy->tallos_ramo = 0; // CALCULAR CALIBRE ACTUAL
                            } else if ($data['tabla'] == 'P') {
                                $proy->plantas_iniciales = $data['proy']->plantas_iniciales;
                                $proy->plantas_actuales = $data['proy']->plantas_iniciales;
                                $proy->fecha_inicio = $data['proy']->fecha_inicio;
                                $proy->activo = 0;
                                $proy->area = $mod->area;
                                $proy->tallos_planta = $data['proy']->tallos_planta;
                                $proy->curva = $data['proy']->curva;
                                $proy->poda_siembra = $data['proy']->poda_siembra;
                                $proy->desecho = $data['proy']->desecho;
                                $proy->semana_poda_siembra = $data['proy']->semana_poda_siembra;
                                $proy->tallos_ramo = $data['proy']->tallos_ramo;
                            }
                        }
                        $proy->save();
                        Log::info('Se ha actualizado el Módulo:' . $mod->id_modulo . ', Semana:' . $semana->codigo . ', Variedad:' . $var->id_variedad);
                    }
                }
            }
        } else {
            Log::info('<*> La semana "desde" no puede ser mayor a la semana "hasta" <*>');
        }
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyeccion:update_semanal" <<<<< * >>>>>');
    }
}