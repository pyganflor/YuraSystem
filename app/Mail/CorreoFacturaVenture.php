<?php

namespace yura\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CorreoFacturaVenture extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $factura_cliente;
    public $factura_sri;
    public $secuencial;
    public $csv_etiqueta;
    public $dist_cajas;
    public $guia_remision;

    public function __construct($factura_cliente,$factura_sri,$secuencial,$csv_etiqueta,$dist_cajas,$guia_remision)
    {
        $this->factura_cliente  = $factura_cliente;
        $this->factura_sri = $factura_sri;
        $this->secuencial = $secuencial;
        $this->csv_etiqueta = $csv_etiqueta;
        $this->dist_cajas = $dist_cajas;
        $this->guia_remision = $guia_remision;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
                                //getConfiguracionEmpresa()->correo
        $correo = $this->from("pruebas-c26453@inbox.mailtrap.io")
            ->view('adminlte.gestion.mails.correo_factura_venture');

        if($this->factura_cliente == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."fact_cliente_".$this->secuencial.'.pdf',[
                'as' => "fact_cliente_".$this->secuencial.'.pdf']);

        if($this->factura_sri == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."fact_sri_".$this->secuencial.'.pdf',[
                'as' =>"fact_sri_".$this->secuencial.'.pdf'
            ]);

        if($this->csv_etiqueta == "true")
            $correo->attach(env('ETIQUETAS_FACTURAS_TEMPORAL')."label_fact_".$this->secuencial.'.csv',[
                'as' =>"label_fact_".$this->secuencial.'.csv'
            ]);

        if($this->dist_cajas == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."dist_cajas_".$this->secuencial.'.pdf',[
                'as' =>"dist_cajas_".$this->secuencial.'.pdf'
            ]);

        if($this->guia_remision == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."guia_factura_".$this->secuencial.".pdf",[
                'as' =>"guia_".$this->secuencial.'.pdf'
            ]);

        $correo->with(['factura'=>$this->secuencial]);

    }
}
