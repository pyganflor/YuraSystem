<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Modulo extends Model
{
    protected $table = 'modulo';
    protected $primaryKey = 'id_modulo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'nombre',   // unico
        'fecha_registro',
        'estado',
        'area', // float
        'descripcion',
        'id_sector',
    ];

    public function sector()
    {
        return $this->belongsTo('\yura\Modelos\Sector', 'id_sector');
    }

    public function ciclos()
    {
        return $this->hasMany('\yura\Modelos\Ciclo', 'id_modulo');
    }

    public function proyecciones()
    {
        return $this->hasMany('\yura\Modelos\ProyeccionModulo', 'id_modulo');
    }

    public function cicloActual()
    {
        foreach ($this->ciclos as $c) {
            if ($c->activo == 1)
                return $c;
        }
        return '';
    }

    public function lotes()
    {
        return $this->hasMany('\yura\Modelos\Lote', 'id_modulo');
    }

    public function lotes_activos()
    {
        return $this->hasMany('\yura\Modelos\Lote', 'id_modulo')->where('estado', '=', 1);
    }

    public function getPodaSiembraActual()
    {
        if ($this->cicloActual() != '') {
            if ($this->cicloActual()->poda_siembra == 'S')
                return 0;
            else {
                $ciclos = Ciclo::All()
                    ->where('id_modulo', $this->id_modulo)
                    ->where('estado', 1)
                    ->sortByDesc('fecha_inicio');

                $r = 0;
                foreach ($ciclos as $c) {
                    if ($c->poda_siembra == 'P')
                        $r++;
                    else
                        break;
                }
                return $r;
            }
        } else
            return '';
    }

    public function getPodaSiembraByCiclo($ciclo)
    {
        if (Ciclo::find($ciclo)->estado == 1) {
            $ciclos = Ciclo::All()
                ->where('id_modulo', $this->id_modulo)
                ->where('estado', 1)
                ->where('fecha_inicio', '<=', Ciclo::find($ciclo)->fecha_inicio)
                ->sortByDesc('fecha_inicio');

            $r = 0;
            foreach ($ciclos as $c) {
                if ($c->poda_siembra == 'P')
                    $r++;
                else
                    break;
            }
            return $r;
        } else
            return '';
    }

    public function getCicloByFecha($fecha)
    {
        $ciclo = DB::table('ciclo')
            ->select('id_ciclo as id')
            ->where('id_modulo', '=', $this->id_modulo)
            ->where('estado', '=', 1)
            ->where('fecha_inicio', '<', $fecha)
            ->orderByDesc('fecha_inicio')
            ->first();

        if ($ciclo != '')
            return Ciclo::find($ciclo->id);
        return '';
    }

    public function getProyeccionByDate($fecha, $variedad)
    {
        $r = ProyeccionModulo::All()->where('estado', 1)
            ->where('fecha_inicio', '<=', $fecha)
            ->where('id_modulo', $this->id_modulo)
            ->where('id_variedad', $variedad)
            ->sortBy('fecha_inicio')
            ->last();

        return $r;
    }

    public function getDataBySemana($semana, $variedad, $desde, $opcion, $detalle)
    {
        $opcion = 'A';  // setear automaticamente a plantas actuales
        if ($semana != '') {
            $tallos_proyectados = 0;
            /* ----------------------------- calcular cosecha real ----------------------------- */
            $cosecha = getTallosCosechadosByModSemVar($this->id_modulo, $semana->codigo, $variedad);

            $ciclo_ini = $this->ciclos->where('estado', 1)
                ->where('fecha_inicio', '>=', $semana->fecha_inicial)->where('fecha_inicio', '<=', $semana->fecha_final)
                ->where('id_variedad', $variedad)->first();
            if ($ciclo_ini != '') { // esa semana inició un ciclo
                $data = [
                    'tipo' => $ciclo_ini->poda_siembra,
                    'info' => $ciclo_ini->poda_siembra . '-' . $this->getPodaSiembraByCiclo($ciclo_ini->id_ciclo),
                    'cosechado' => $cosecha,
                    'proyectados' => $tallos_proyectados,
                    'modelo' => $ciclo_ini->id_ciclo,
                    'ciclo' => $ciclo_ini,
                    'proy' => '',
                    'tabla' => 'C',
                ];
            } else {
                $ciclo_last = $this->ciclos->where('estado', 1)
                    ->where('fecha_inicio', '<=', $semana->fecha_inicial)
                    ->where('id_variedad', $variedad)->sortBy('fecha_inicio')->last();

                $data = [
                    'tipo' => 'V',  // vacio
                    'info' => '',
                    'cosechado' => $cosecha,
                    'proyectados' => $tallos_proyectados,
                    'modelo' => $ciclo_last != '' ? $ciclo_last->id_ciclo : null,
                    'ciclo' => $ciclo_last != '' ? $ciclo_last : '',
                    'proy' => '',
                    'tabla' => 'C',
                ];
                if ($ciclo_last != '') {    // existe un ciclo real
                    if ($ciclo_last->fecha_inicio >= $desde) {
                        if ($ciclo_last->activo == 1 || ($ciclo_last->activo == 0 && $ciclo_last->fecha_fin >= $semana->fecha_inicial)) {
                            $fecha_inicio = getSemanaByDate($ciclo_last->fecha_inicio)->fecha_inicial;
                            $num_semana = (intval(difFechas($semana->fecha_inicial, $fecha_inicio)->days / 7) + 1);
                            $num_sem_cosecha = count(explode('-', $ciclo_last->curva)) - 1;
                            if (intval($num_semana) <= intval($ciclo_last->semana_poda_siembra + $num_sem_cosecha) || $ciclo_last->activo == 0) {  // aun esta dentro de lo programado
                                if ($num_semana >= $ciclo_last->semana_poda_siembra && $num_semana <= intval($ciclo_last->semana_poda_siembra + $num_sem_cosecha)) {
                                    $tipo = 'T';    // semana de cosecha
                                    /* --------------------------- calcular cosecha proyectada ------------------------- */
                                    $pos_semana_cosecha = intval($num_semana - $ciclo_last->semana_poda_siembra);
                                    $desecho = 100 - $ciclo_last->desecho;
                                    if ($opcion == 'I') // plantas iniciales
                                        $cosecha_totales = round((($ciclo_last->plantas_iniciales * $ciclo_last->conteo) * $desecho) / 100, 2);
                                    else    // plantas actuales
                                        $cosecha_totales = round((($ciclo_last->plantas_actuales() * $ciclo_last->conteo) * $desecho) / 100, 2);
                                    $tallos_proyectados = round(($cosecha_totales * explode('-', $ciclo_last->curva)[$pos_semana_cosecha]) / 100, 2);
                                } else
                                    $tipo = 'I';    // informacion

                                $data = [
                                    'tipo' => $tipo,  // semana de cosecha o informacion
                                    'info' => $num_semana . 'º',
                                    'cosechado' => $cosecha,
                                    'proyectados' => $tallos_proyectados,
                                    'modelo' => $ciclo_last->id_ciclo,
                                    'ciclo' => $ciclo_last,
                                    'proy' => '',
                                    'tabla' => 'C',
                                ];
                            } else {    // ya pasó de lo programado
                                /* ========== BUSCAR PROYECCION =========== */
                                $proy_ini = $this->proyecciones->where('estado', 1)
                                    ->where('id_semana', $semana->id_semana)
                                    ->where('id_variedad', $variedad)->first();

                                if ($proy_ini != '') {
                                    $data = [
                                        'tipo' => 'Y',  // inicio de una proyeccion
                                        'info' => $proy_ini->tipo,
                                        'cosechado' => $cosecha,
                                        'proyectados' => $tallos_proyectados,
                                        'modelo' => $proy_ini->id_proyeccion_modulo,
                                        'ciclo' => '',
                                        'proy' => $proy_ini,
                                        'tabla' => 'P',
                                    ];
                                } else {    // BUSCAR ULTIMA PROYECCION
                                    $proy_last = $this->getProyeccionByDate($semana->fecha_final, $variedad);

                                    if ($proy_last != '') {
                                        if ($proy_last->tipo != 'C') {  // indica no cerrar modulo
                                            $fecha_inicio = $proy_last->semana->fecha_inicial;
                                            $num_semana = (intval(difFechas($semana->fecha_inicial, $fecha_inicio)->days / 7) + 1);
                                            $num_sem_cosecha = count(explode('-', $proy_last->curva)) - 1;
                                            if (intval($num_semana) <= intval($proy_last->semana_poda_siembra + $num_sem_cosecha)) {  // aun esta dentro de lo programado
                                                if ($num_semana >= $proy_last->semana_poda_siembra) {
                                                    $tipo = 'T';    // semana de cosecha

                                                    /* --------------------------- calcular cosecha proyectada ------------------------- */
                                                    $pos_semana_cosecha = intval($num_semana - $proy_last->semana_poda_siembra);
                                                    $desecho = 100 - $proy_last->desecho;
                                                    $cosecha_totales = round((($proy_last->plantas_iniciales * $proy_last->tallos_planta) * $desecho) / 100, 2);
                                                    $tallos_proyectados = round(($cosecha_totales * explode('-', $proy_last->curva)[$pos_semana_cosecha]) / 100, 2);
                                                } else
                                                    $tipo = 'I';    // informacion

                                                $data = [
                                                    'tipo' => $tipo,
                                                    'info' => $num_semana . 'º',
                                                    'cosechado' => $cosecha,
                                                    'proyectados' => $tallos_proyectados,
                                                    'modelo' => $proy_last->id_proyeccion_modulo,
                                                    'ciclo' => '',
                                                    'proy' => $proy_last,
                                                    'tabla' => 'P',
                                                ];
                                            } else {
                                                $data = [
                                                    'tipo' => 'F',  // fin de proyeccion
                                                    'info' => '-',
                                                    'cosechado' => $cosecha,
                                                    'proyectados' => $tallos_proyectados,
                                                    'modelo' => null,
                                                    'ciclo' => '',
                                                    'proy' => '',
                                                    'tabla' => '',
                                                ];
                                            }
                                        } else {
                                            $data = [
                                                'tipo' => 'X',  // cerrado
                                                'info' => '*',
                                                'cosechado' => $cosecha,
                                                'proyectados' => $tallos_proyectados,
                                                'modelo' => null,
                                                'ciclo' => '',
                                                'proy' => '',
                                                'tabla' => '',
                                            ];
                                        }
                                    } else {
                                        $data = [
                                            'tipo' => 'F',  // fin de ciclo
                                            'info' => '-',
                                            'cosechado' => $cosecha,
                                            'proyectados' => $tallos_proyectados,
                                            'modelo' => null,
                                            'ciclo' => '',
                                            'proy' => '',
                                            'tabla' => '',
                                        ];
                                    }
                                }
                            }
                        } else {
                            $data = [
                                'tipo' => 'F',  // fin de ciclo
                                'info' => '-',
                                'cosechado' => $cosecha,
                                'proyectados' => $tallos_proyectados,
                                'modelo' => null,
                                'ciclo' => '',
                                'proy' => '',
                                'tabla' => '',
                            ];

                            /* ========== BUSCAR PROYECCION =========== */
                            $proy_ini = $this->proyecciones->where('estado', 1)
                                ->where('id_semana', $semana->id_semana)
                                ->where('id_variedad', $variedad)->first();

                            if ($proy_ini != '') {
                                $data = [
                                    'tipo' => 'Y',  // inicio de una proyeccion
                                    'info' => $proy_ini->tipo,
                                    'cosechado' => $cosecha,
                                    'proyectados' => $tallos_proyectados,
                                    'modelo' => $proy_ini->id_proyeccion_modulo,
                                    'ciclo' => '',
                                    'proy' => $proy_ini,
                                    'tabla' => 'P',
                                ];
                            } else {    // BUSCAR ULTIMA PROYECCION
                                $proy_last = $this->getProyeccionByDate($semana->fecha_final, $variedad);

                                if ($proy_last != '') {
                                    if ($proy_last->tipo != 'C') {  // indica no cerrar modulo
                                        $fecha_inicio = $proy_last->semana->fecha_inicial;
                                        $num_semana = (intval(difFechas($semana->fecha_inicial, $fecha_inicio)->days / 7) + 1);
                                        $num_sem_cosecha = count(explode('-', $proy_last->curva)) - 1;
                                        if (intval($num_semana) <= intval($proy_last->semana_poda_siembra + $num_sem_cosecha)) {  // aun esta dentro de lo programado
                                            if ($num_semana >= $proy_last->semana_poda_siembra) {
                                                $tipo = 'T';    // semana de cosecha

                                                /* --------------------------- calcular cosecha proyectada ------------------------- */
                                                $pos_semana_cosecha = intval($num_semana - $proy_last->semana_poda_siembra);
                                                $desecho = 100 - $proy_last->desecho;
                                                $cosecha_totales = round((($proy_last->plantas_iniciales * $proy_last->tallos_planta) * $desecho) / 100, 2);
                                                $tallos_proyectados = round(($cosecha_totales * explode('-', $proy_last->curva)[$pos_semana_cosecha]) / 100, 2);
                                            } else
                                                $tipo = 'I';    // informacion

                                            $data = [
                                                'tipo' => $tipo,
                                                'info' => $num_semana . 'º',
                                                'cosechado' => $cosecha,
                                                'proyectados' => $tallos_proyectados,
                                                'modelo' => $proy_last->id_proyeccion_modulo,
                                                'ciclo' => '',
                                                'proy' => $proy_last,
                                                'tabla' => 'P',
                                            ];
                                        } else {
                                            $data = [
                                                'tipo' => 'F',  // fin de proyeccion
                                                'info' => '-',
                                                'cosechado' => $cosecha,
                                                'proyectados' => $tallos_proyectados,
                                                'modelo' => null,
                                                'ciclo' => '',
                                                'proy' => '',
                                                'tabla' => '',
                                            ];
                                        }
                                    } else {
                                        $data = [
                                            'tipo' => 'X',  // cerrado
                                            'info' => '*',
                                            'cosechado' => $cosecha,
                                            'proyectados' => $tallos_proyectados,
                                            'modelo' => null,
                                            'ciclo' => '',
                                            'proy' => '',
                                            'tabla' => '',
                                        ];
                                    }
                                } else {
                                    $data = [
                                        'tipo' => 'F',  // fin de ciclo
                                        'info' => '-',
                                        'cosechado' => $cosecha,
                                        'proyectados' => $tallos_proyectados,
                                        'modelo' => null,
                                        'ciclo' => '',
                                        'proy' => '',
                                        'tabla' => '',
                                    ];
                                }
                            }
                        }
                    }
                } else {    // no existe un ciclo pasado
                    /* ========== BUSCAR PROYECCION =========== */
                    $proy_ini = $this->proyecciones->where('estado', 1)
                        ->where('id_semana', $semana->id_semana)
                        ->where('id_variedad', $variedad)->first();

                    if ($proy_ini != '') {
                        $data = [
                            'tipo' => 'Y',  // inicio de una proyeccion
                            'info' => $proy_ini->tipo,
                            'cosechado' => $cosecha,
                            'proyectados' => $tallos_proyectados,
                            'modelo' => $proy_ini->id_proyeccion_modulo,
                            'ciclo' => '',
                            'proy' => $proy_ini,
                            'tabla' => 'P',
                        ];
                    } else {    // BUSCAR ULTIMA PROYECCION
                        $proy_last = $this->getProyeccionByDate($semana->fecha_final, $variedad);

                        if ($proy_last != '') {
                            if ($proy_last->tipo != 'C') {  // indica no cerrar modulo
                                $fecha_inicio = $proy_last->semana->fecha_inicial;
                                $num_semana = (intval(difFechas($semana->fecha_inicial, $fecha_inicio)->days / 7) + 1);
                                $num_sem_cosecha = count(explode('-', $proy_last->curva)) - 1;
                                if (intval($num_semana) <= intval($proy_last->semana_poda_siembra + $num_sem_cosecha)) {  // aun esta dentro de lo programado
                                    if ($num_semana >= $proy_last->semana_poda_siembra) {
                                        $tipo = 'T';    // semana de cosecha

                                        /* --------------------------- calcular cosecha proyectada ------------------------- */
                                        $pos_semana_cosecha = intval($num_semana - $proy_last->semana_poda_siembra);
                                        $desecho = 100 - $proy_last->desecho;
                                        $cosecha_totales = round((($proy_last->plantas_iniciales * $proy_last->tallos_planta) * $desecho) / 100, 2);
                                        $tallos_proyectados = round(($cosecha_totales * explode('-', $proy_last->curva)[$pos_semana_cosecha]) / 100, 2);
                                    } else
                                        $tipo = 'I';    // informacion

                                    $data = [
                                        'tipo' => $tipo,
                                        'info' => $num_semana . 'º',
                                        'cosechado' => $cosecha,
                                        'proyectados' => $tallos_proyectados,
                                        'modelo' => $proy_last->id_proyeccion_modulo,
                                        'ciclo' => '',
                                        'proy' => $proy_last,
                                        'tabla' => 'P',
                                    ];
                                } else {
                                    $data = [
                                        'tipo' => 'F',  // fin de proyeccion
                                        'info' => '-',
                                        'cosechado' => $cosecha,
                                        'proyectados' => $tallos_proyectados,
                                        'modelo' => null,
                                        'ciclo' => '',
                                        'proy' => '',
                                        'tabla' => '',
                                    ];
                                }
                            } else {
                                $data = [
                                    'tipo' => 'X',  // cerrado
                                    'info' => '*',
                                    'cosechado' => $cosecha,
                                    'proyectados' => $tallos_proyectados,
                                    'modelo' => null,
                                    'ciclo' => '',
                                    'proy' => '',
                                    'tabla' => '',
                                ];
                            }
                        } else {
                            $data = [
                                'tipo' => 'F',  // fin de ciclo
                                'info' => '-',
                                'cosechado' => $cosecha,
                                'proyectados' => $tallos_proyectados,
                                'modelo' => null,
                                'ciclo' => '',
                                'proy' => '',
                                'tabla' => '',
                            ];
                        }
                    }
                }
            }
        } else {
            $data = [
                'tipo' => 'V',  // vacio
                'info' => '',
                'cosechado' => 0,
                'proyectados' => 0,
                'modelo' => null,
                'ciclo' => '',
                'proy' => '',
                'tabla' => 'C',
            ];
        }
        return $data;
    }

    public function getProyeccionesByRango($semana_desde, $semana_hasta, $variedad)
    {
        return DB::table('proyeccion_modulo_semana')
            ->select('*')
            ->where('id_modulo', '=', $this->id_modulo)
            ->where('id_variedad', '=', $variedad)
            ->where('semana', '>=', $semana_desde)
            ->where('semana', '<=', $semana_hasta)
            ->orderBy('semana')
            ->get();
    }

    public function getLastCosecha()
    {
        $query = DB::table('desglose_recepcion as dr')
            ->select(DB::raw('max(r.fecha_ingreso) as fecha'))
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->where('dr.estado', 1)
            ->where('r.estado', 1)
            ->where('dr.id_modulo', $this->id_modulo)
            ->where('r.fecha_ingreso', '<=', date('Y-m-d'))
            ->get();
        if (count($query) > 0)
            if ($query[0] != '')
                return difFechas(date('Y-m-d'), substr($query[0]->fecha, 0, 10))->days;
        return 0;
    }
}