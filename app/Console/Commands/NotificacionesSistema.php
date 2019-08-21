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
    protected $description = 'Crear las notificaciones de tipo Sistema';

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
            $m->delete();
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

    public function pedidos_sin_empaquetar($not)
    {
        $sum = count(DB::table('pedido')
            ->select('*')
            ->where('estado', '=', 1)
            ->where('empaquetado', '=', 0)
            ->where('variedad', '!=', '')
            ->where('fecha_pedido', '<', date('Y-m-d'))
            ->get());

        $models = UserNotification::All()
            ->where('estado', 1)
            ->where('id_notificacion', $not->id_notificacion);
        foreach ($models as $m) {   // desactivar las anteriores
            $m->delete();
        }
        if ($sum > 0) { // crear las nuevas notificaciones
            foreach ($not->usuarios as $not_user) {
                $model = new UserNotification();
                $model->id_notificacion = $not->id_notificacion;
                $model->id_usuario = $not_user->id_usuario;
                $model->titulo = 'Pedidos pasados sin empaquetar';
                $model->texto = 'Hay ' . $sum . ' pedido(s) sin empaquetar de días pasados';
                $model->url = 'pedidos';
                $model->save();
            }
        }
    }

    public function botar_flores_cuarto_frio($not)
    {
        $sum = DB::table('inventario_frio')
            ->select(DB::raw('sum(disponibles) as cant'))
            ->where('estado', '=', 1)
            ->where('basura', '=', 0)
            ->where('disponibilidad', '=', 1)
            ->where('disponibles', '>', 0)
            ->where('fecha_ingreso', '<=', opDiasFecha('-', 9, date('Y-m-d')))
            ->get()[0]->cant;

        $models = UserNotification::All()
            ->where('estado', 1)
            ->where('id_notificacion', $not->id_notificacion);
        foreach ($models as $m) {   // desactivar las anteriores
            $m->delete();
        }
        if ($sum > 0) { // crear las nuevas notificaciones
            foreach ($not->usuarios as $not_user) {
                $model = new UserNotification();
                $model->id_notificacion = $not->id_notificacion;
                $model->id_usuario = $not_user->id_usuario;
                $model->titulo = 'Flores pasadas en cuarto frío';
                $model->texto = 'Hay ' . $sum . ' ramos en cuarto frío con 9 o más días';
                $model->url = 'cuarto_frio';
                $model->save();
            }
        }
    }

    public function clasificacion_verde_sin_cerrar($not)
    {
        $query = DB::table('clasificacion_verde')
            ->select('*')
            ->where('estado', '=', 1)
            ->where('activo', '=', 1)
            ->where('fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')))
            ->get();

        $models = UserNotification::All()
            ->where('estado', 1)
            ->where('id_notificacion', $not->id_notificacion);
        foreach ($models as $m) {   // desactivar las anteriores
            $m->delete();
        }
        if (count($query) > 0) { // crear las nuevas notificaciones
            foreach ($query as $q) {
                foreach ($not->usuarios as $not_user) {
                    $model = new UserNotification();
                    $model->id_notificacion = $not->id_notificacion;
                    $model->id_usuario = $not_user->id_usuario;
                    $model->titulo = 'Clasificación Verde por terminar';
                    $model->texto = 'La clasificación Verde del día: ' . $q->fecha_ingreso . ' no se ha terminado';
                    $model->url = 'clasificacion_verde';
                    $model->save();
                }
            }
        }
    }
}