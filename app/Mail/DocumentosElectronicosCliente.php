<?php

namespace yura\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use yura\Modelos\ConfiguracionEmpresa;

class DocumentosElectronicosCliente extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    //public $factura_cliente;
    //public $factura_sri;
    public $request;
    public $comprobante;
    //public $csv_etiqueta;
    //public $dist_cajas;
    //public $guia_remision;
    //public $packing_list;
    public $correoEmpresa;

    public function __construct($request,$comprobante,$correoEmpresa)
    {
        $this->request = $request;
        //$this->factura_cliente  = $factura_cliente;
        //$this->factura_sri = $factura_sri;
        $this->comprobante = $comprobante;
        //$this->csv_etiqueta = $csv_etiqueta;
        //$this->dist_cajas = $dist_cajas;
        //$this->guia_remision = $guia_remision;
        //$this->packing_list = $packing_list;
        $this->correoEmpresa=  $correoEmpresa;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $inicialesEmpresa = explode('-',getConfiguracionEmpresa()->codigo_etiqueta_empresa)[0];
        $correo = $this->from($this->correoEmpresa)
            ->view('adminlte.gestion.mails.documento_electronico_cliente')
            ->subject($inicialesEmpresa." - FACT invoice ".$this->comprobante->secuencial ." ". $this->comprobante->envio->pedido->cliente->detalle()->nombre);

        if($this->request['factura_cliente'] == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."fact_cliente_".$this->comprobante->secuencial.'.pdf',[
                'as' => $inicialesEmpresa ." - "."FACTinvoice".$this->comprobante->secuencial.'.pdf']);

        if($this->request['factura_sri'] == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."fact_sri_".$this->comprobante->secuencial.'.pdf',[
                'as' =>$inicialesEmpresa ." - "."FACTinvoiceSRI".$this->comprobante->secuencial.'.pdf'
            ]);

        if($this->request['csv_etiqueta'] == "true")
            $correo->attach(env('ETIQUETAS_FACTURAS_TEMPORAL')."label_fact_".$this->comprobante->secuencial.'.csv',[
                'as' =>$inicialesEmpresa ." - "."labels_".$this->comprobante->secuencial.'.csv'
            ]);

        if($this->request['dist_cajas'] == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."dist_cajas_".$this->comprobante->secuencial.'.pdf',[
                'as' =>$inicialesEmpresa ." - "."distri_cajas".$this->comprobante->secuencial.'.pdf'
            ]);

        if($this->request['guia_remision'] == "true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."guia_factura_".$this->comprobante->secuencial.".pdf",[
                'as' =>$inicialesEmpresa ." - "."guia_".$this->comprobante->secuencial.'.pdf'
            ]);

        if($this->request['packing_list'] ==="true")
            $correo->attach(env('PDF_FACTURAS_TEMPORAL')."packing_list".$this->comprobante->secuencial.".pdf",[
                'as' =>$inicialesEmpresa ." - "."FACTpacklist".$this->comprobante->secuencial.'.pdf'
            ]);

        $correo->with([
            'comprobante'=>$this->comprobante
        ]);

    }
}
