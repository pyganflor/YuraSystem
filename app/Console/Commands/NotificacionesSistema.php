<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Notificacion;
use yura\Modelos\UserNotification;

class NotificacionesSistema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:sistema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear las notificaciones';

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
        $notificaciones = Notificacion::All()->where('estado', 1)->where('tipo', 'S');
        foreach ($notificaciones as $not) {
            $funcion = $not->nombre;
            $this->$funcion($not);
        }
    }

    public function flores_pasadas_cuarto_frio($not)
    {
        $sum = DB::table('inventario_frio')
            ->select(DB::raw('sum(disponibles) as cant'))
            ->where('estado', '=', 1)
            ->where('basura', '=', 0)
            ->where('disponibilidad', '=', 1)
            ->where('disponibles', '>', 0)
            ->where('fecha_ingreso', '<', opDiasFecha('-', 5, date('Y-m-d')))
            ->get()[0]->cant;

        $models = UserNotification::All()
            ->where('estado', 1)
            ->where('id_notificacion', $not->id_notificacion);
        foreach ($models as $m) {   // desactivar las anteriores
            $m->estado = 0;
            $m->save();
        }
        if ($sum > 0) { // crear las nuevas notificaciones
            foreach ($not->usuarios as $not_user) {
                $model = new UserNotification();
                $model->id_notificacion = $not->id_notificacion;
                $model->id_usuario = $not_user->id_usuario;
                $model->titulo = 'Flores pasadas en cuarto frío';
                $model->texto = 'Hay ' . $sum . ' ramos en cuarto frío con más de 5 días';
                $model->url = 'cuarto_frio';
                $model->save();
            }
        }
    }
}