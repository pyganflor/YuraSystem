<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Http\Controllers\Indicadores\Area;
use yura\Http\Controllers\Indicadores\Campo;
use yura\Http\Controllers\Indicadores\Postcosecha;
use yura\Http\Controllers\Indicadores\Venta;
use yura\Http\Controllers\Indicadores\ProyeccionesVenta;

class UpdateIndicador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indicador:update {indicador=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para actualizar los indicadores de los reportes del sistema';

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
     * @argument D => dashboard
     * @argument DP => dashboard de proyección
     */
    public function handle()
    {
        $ini = date('Y-m-d H:i:s');
        Log::info('<<<<< ! >>>>> Ejecutando comando "indicador:update" <<<<< ! >>>>>');

        $indicador_par = $this->argument('indicador');

        if ($indicador_par === '0' || $indicador_par === 'D1') {  // Calibre (-7 días)
            Postcosecha::calibre_7_dias_atras();
            Log::info('INDICADOR: "Calibre (-7 dias)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D2') {  // Tallos clasificados (-7 días)
            Postcosecha::tallos_clasificados_7_dias_atras();
            Log::info('INDICADOR: "Tallos clasificados (-7 dias)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D3' || $indicador_par === 'D4') {
            // Precio promedio por ramo (-7 días) - Dinero ingresado (-7 días)
            Venta::dinero_y_precio_x_ramo_7_dias_atras();
            Log::info('INDICADOR: "Precio promedio por ramo (-7 días) - Dinero ingresado (-7 días)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D5' || $indicador_par === 'D6') { // Rendimiento (-7 días) - Desecho (-7 días)
            Postcosecha::rendimiento_desecho_7_dias_atras();
            Log::info('INDICADOR: "Rendimiento (-7 días) - Desecho (-7 días)"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP1') {
            ProyeccionesVenta::sumCajasFuturas4Semanas();
            Log::info('INDICADOR: "Cajas cosechadas +4 semanas"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP2') {
            ProyeccionesVenta::sumTallosFuturos4Semanas();
            Log::info('INDICADOR: "Tallos cosechados +4 semanas"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP3') {
            ProyeccionesVenta::sumCajasVendidas();
            Log::info('INDICADOR: "Cajas vendidas a futuro +4 semanas"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP4') {
            ProyeccionesVenta::sumDineroGeneradoVentas();
            Log::info('INDICADOR: "Dinero generado ventas a futuro +4 semanas"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP5') {
            ProyeccionesVenta::proyeccionVentaFutura3Meses();
            Log::info('INDICADOR: "Dinero generado ventas a futuro mes 1|mes 2|mes 3"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP6') {
            ProyeccionesVenta::sumTallosCosechadosFuturo1Semana();
            Log::info('INDICADOR: "Tallos cosechados a futuro +1 semana"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP7') {
            ProyeccionesVenta::sumCajasVendidasFuturas1Semana();
            Log::info('INDICADOR: "Cajas vendidas futuro +1 semana"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP8') {
            ProyeccionesVenta::sumCajasCosechadasFuturas1Semana();
            Log::info('INDICADOR: "Cajas cosechadas a futuro +1 semana"');
        }

        if ($indicador_par === '0' || $indicador_par === 'DP9') {
            ProyeccionesVenta::sumDineroGeneradoFuturo1Semana();
            Log::info('INDICADOR: "Dinero generado en ventas a futuro +1 semana"');
        }

        if ($indicador_par === '0' || $indicador_par === 'D7') { // Área en producción (-4 meses)
            Area::area_produccion_4_meses_atras();
            Log::info('INDICADOR: "Área en producción (-4 meses)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D8') { // Ramos/m2/año (-4 meses)
            Area::ramos_m2_anno_4_meses_atras();
            Log::info('INDICADOR: "Ramos/m2/año (-4 meses)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D9') { // Venta $/m2/año (-4 meses)
            Log::info('inicio INDICADOR: "Venta $/m2/año (-4 meses)"');
            Venta::dinero_m2_anno_4_meses_atras();
            Log::info('fin INDICADOR: "Venta $/m2/año (-4 meses)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D10') { // Venta $/m2/año (-1 año)
            Venta::dinero_m2_anno_1_anno_atras();
            Log::info('INDICADOR: "Venta $/m2/año (-1 año)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D11') { // Tallos cosechados (-7 días)
            Campo::tallos_cosechados_7_dias_atras();
            Log::info('INDICADOR: "Tallos cosechados (-7 días)"');
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyeccion:update_semanal" <<<<< * >>>>>');
    }
}
