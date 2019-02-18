<?php

namespace yura\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CorreoFactura extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $correoCliente;
    public $nombreCliente;
    public $nombreArchivo;
    public $numeroComprobante;

    public function __construct($correoCliente,$nombreCliente,$nombreArchivo,$numeroComprobante)
    {
        $this->correoCliente     = $correoCliente;
        $this->nombreCliente     = $nombreCliente;
        $this->nombreArchivo     = $nombreArchivo;
        $this->numeroComprobante = $numeroComprobante;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {                       //$correoCliente
        return $this->from("pruebas-c26453@inbox.mailtrap.io")
            ->view('adminlte.gestion.mails.correo_factura')
            ->attach(env('PATH_XML_AUTORIZADOS').$this->nombreArchivo.'.xml',[
                'as' => $this->nombreArchivo.'.xml'
            ])->attach(env('PDF_FACTURAS').$this->nombreArchivo.'.pdf',[
                'as' => $this->nombreArchivo.'.pdf'
            ]);
    }
}
