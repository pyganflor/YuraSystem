<?php

namespace yura\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CorreoErrorEnvioComprobanteElectronico extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $claveacceso;
    public $sub_carpeta;

    public function __construct($claveacceso,$sub_carpeta)
    {
        $this->claveacceso = $claveacceso;
        $this->sub_carpeta = $sub_carpeta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){

        if(file_exists(env('PATH_XML_RECHAZADOS').$this->sub_carpeta.$this->claveacceso.'.xml')){
            $archivo = env('PATH_XML_RECHAZADOS').$this->sub_carpeta.$this->claveacceso.'.xml';
        }else{
            $archivo = env('PATH_XML_NO_AUTORIZADOS').$this->sub_carpeta.$this->claveacceso.'.xml';
        }
        return $this->from("pruebas-c26453@inbox.mailtrap.io")
            ->view('adminlte.gestion.mails.correo_error_envio_comprobante',[
                'clave_acceso' => $this->claveacceso,
            ])->attach($archivo,[
                'as' => $archivo
            ]);
    }
}
