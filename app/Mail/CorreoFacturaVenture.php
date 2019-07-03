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
    public $correos;
    public function __construct($factura_cliente,$factura_sri,$secuencial)
    {
        $this->factura_cliente  = $factura_cliente;
        $this->factura_sri = $factura_sri;
        $this->secuencial = $secuencial;
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

        $correo->with(['factura'=>$this->secuencial]);

    }
}
