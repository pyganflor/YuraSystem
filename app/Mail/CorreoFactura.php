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
    public $preFactura;

    public function __construct($correoCliente,$nombreCliente,$nombreArchivo,$numeroComprobante,$preFactura)
    {
        $this->correoCliente     = $correoCliente;
        $this->nombreCliente     = $nombreCliente;
        $this->nombreArchivo     = $nombreArchivo;
        $this->numeroComprobante = $numeroComprobante;
        $this->preFactura        = $preFactura;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tipo_comprobante = getDetallesClaveAcceso($this->nombreArchivo,'TIPO_COMPROBANTE');
        $sub_carpeta = getSubCarpetaArchivo(false,$tipo_comprobante);
        ($this->preFactura)
            ? $paht_env = 'PATH_XML_FIRMADOS'
            : $paht_env = 'PATH_XML_AUTORIZADOS';
                                //$correoCliente
        return $this->from("pruebas-c26453@inbox.mailtrap.io")
            ->view('adminlte.gestion.mails.correo_factura')
            ->attach(env($paht_env).$sub_carpeta.$this->nombreArchivo.'.xml',[
                'as' => $this->nombreArchivo.'.xml'
            ])->attach(env('PDF_FACTURAS').$this->nombreArchivo.'.pdf',[
                'as' => $this->nombreArchivo.'.pdf'
            ]);
    }
}
