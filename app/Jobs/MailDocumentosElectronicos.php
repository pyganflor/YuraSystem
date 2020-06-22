<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Mail\DocumentosElectronicosCliente;
use yura\Mail\DocumentosElectronicosAgenciaCarga;
use Illuminate\Support\Facades\Mail;

class MailDocumentosElectronicos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $correos;
    public $request;
    public $comprobante;
    public $correoEmpresa;

    public function __construct($correos,$request,$comprobante,$correoEmpresa)
    {
        $this->correos = $correos;
        $this->request = $request;
        $this->comprobante = $comprobante;
        $this->correoEmpresa = $correoEmpresa;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $correoEmpresa = $this->correoEmpresa;
        //dump($this->correoEmpresa);
        //$this->correos['agencias'][] = $this->correoEmpresa;
        //$this->correos['cliente'][] = $this->correoEmpresa;
        $mail = Mail::to($this->correoEmpresa);
        $copia=[];
        if($this->request['cliente'] == "true")
            if(isset($this->correos['cliente']) && count($this->correos['cliente'])>0)
            foreach($this->correos['cliente'] as $cliente)
                $copia[]=$cliente;

        if($this->request['agencia_carga'] == "true")
            if(isset($this->correos['agencias']) && count($this->correos['agencias'])>0)
                foreach($this->correos['agencias'] as $agencia)
                    $copia[]=$agencia;

        if(count($copia)>0)
            $mail->cc($copia);

        $mail->send(new DocumentosElectronicosAgenciaCarga($this->request,$this->comprobante,$this->correoEmpresa));

        if($this->request['factura_sri'] == "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."fact_sri_".$this->comprobante->secuencial.'.pdf'))
            unlink(env('PDF_FACTURAS_TEMPORAL')."fact_sri_".$this->comprobante->secuencial.'.pdf');

        if($this->request['factura_cliente'] == "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."fact_cliente_".$this->comprobante->secuencial.'.pdf'))
            unlink(env('PDF_FACTURAS_TEMPORAL')."fact_cliente_".$this->comprobante->secuencial.'.pdf');

        if($this->request['csv_etiqueta'] == "true" && file_exists(env('ETIQUETAS_FACTURAS_TEMPORAL').'label_fact_'.$this->comprobante->secuencial.'.csv'))
            unlink(env('ETIQUETAS_FACTURAS_TEMPORAL')."label_fact_".$this->comprobante->secuencial.'.csv');

        if($this->request['dist_cajas'] === "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."dist_cajas_".$this->comprobante->secuencial.'.pdf'))
            unlink(env('PDF_FACTURAS_TEMPORAL')."dist_cajas_".$this->comprobante->secuencial.'.pdf');

        if($this->request['guia_remision'] === "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."guia_factura_".$this->comprobante->secuencial.".pdf"))
            unlink(env('PDF_FACTURAS_TEMPORAL')."guia_factura_".$this->comprobante->secuencial.".pdf");

        if($this->request['packing_list'] === "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."packing_list".$this->comprobante->secuencial.".pdf"))
            unlink(env('PDF_FACTURAS_TEMPORAL')."packing_list".$this->comprobante->secuencial.".pdf");

        Info("documentos electronicos enviados a: ". (isset($this->correos['cliente'][0]) ? $this->correos['cliente'][0] : "nadie") . " y a ".(isset($this->correos['agencias'][0]) ? $this->correos['agencias'][0] : "niguna agencia"));
    }
}
