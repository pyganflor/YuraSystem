<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Ciclo;

class RecalcularCurvas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curva_cosecha:recalcular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para recalcular las curvas de cosecha';

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
        $semana_pasada = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
        $ciclos = DB::table('proyeccion_modulo_semana')
            ->select('modelo')->distinct()
            ->where('estado', 1)
            ->where('tabla', 'C')
            ->where('semana', $semana_pasada->codigo)
            ->where('cosechados', '>', 0)
            ->get();
        foreach ($ciclos as $c) {
            $ciclo = Ciclo::find($c->modelo);
            if ($ciclo->modulo->nombre == '8') {      // quitar
                $sem_ini = $ciclo->semana();
                $num_sem = intval(difFechas($semana_pasada->fecha_inicial, $sem_ini->fecha_inicial)->days / 7) + 1;
                if ($ciclo->activo == 1 && $num_sem >= $ciclo->semana_poda_siembra - 2) {   // esta activo y es una semana minima 2 antes del inicio de cosecha
                    $configuracion = getConfiguracionEmpresa();
                    $modulo = $ciclo->modulo;
                    $getTallosProyectados = $ciclo->getTallosProyectados();
                    if ($num_sem < $ciclo->semana_poda_siembra) {   // se trata de una semana antes del inicio de cosecha
                        $cosechado = DB::table('desglose_recepcion as dr')
                            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
                            ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cant'))
                            ->where('dr.estado', 1)
                            ->where('dr.id_modulo', $modulo->id_modulo)
                            ->where('r.estado', 1)
                            ->where('r.fecha_ingreso', '<=', $semana_pasada->fecha_final)
                            ->where('r.fecha_ingreso', '>=', opDiasFecha('+', 35, $ciclo->fecha_inicio))
                            ->get()[0]->cant;
                        $porc_cosechado = intval(($cosechado * 100) / $getTallosProyectados);
                        if ($porc_cosechado >= $configuracion->proy_minimo_cosecha) {   // hay que mover una semana antes la curva
                            $new_curva = getNuevaCurva($ciclo->curva, $porc_cosechado);
                            dump('mover antes a la semana: ' . $num_sem);
                            dd($new_curva);
                        }
                    } else {    // se trata de una semana de curva o posterior
                        $pos_sem = $num_sem - $ciclo->semana_poda_siembra;
                        if ($pos_sem == 0) {  // primera semana de la curva
                            $cosechado = DB::table('desglose_recepcion as dr')
                                ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
                                ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cant'))
                                ->where('dr.estado', 1)
                                ->where('dr.id_modulo', $modulo->id_modulo)
                                ->where('r.estado', 1)
                                ->where('r.fecha_ingreso', '<=', $semana_pasada->fecha_final)
                                ->where('r.fecha_ingreso', '>=', opDiasFecha('+', 35, $ciclo->fecha_inicio))
                                ->get()[0]->cant;
                            $porc_cosechado = intval(($cosechado * 100) / $getTallosProyectados);
                            if ($porc_cosechado < $configuracion->proy_minimo_cosecha) {    // hay que mover una semana despues
                                dd('hay que mover una semana despues');
                            } else {    // recalcular solamente
                                $new_curva = getNuevaCurva($ciclo->curva, $porc_cosechado);
                                dump('recalcular solamente');
                                dd($new_curva);
                            }
                            dd('primera semana de la curva: ');
                        } else if ($pos_sem < count(explode('-', $ciclo->curva)) - 1) {   // semana numero "$pos_sem" de la curva
                            dd('semana numero: ' . $pos_sem . ' de la curva');
                        } else {    // ultima semana de la curva
                            dd('ultima semana de la curva');
                        }
                    }
                    dd('modulo=' . $modulo->nombre, 'total=' . $getTallosProyectados);
                }
            }
        }
    }
}