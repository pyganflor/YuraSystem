<div style="width: 90%;margin: 0 auto">
    <p style="font-size: 12pt">
        De: {{getConfiguracionEmpresa()->razon_social}}
    </p>
    <p style="font-size: 12pt">
        Para: <b>{{$nombreCliente}}</b>
    </p>
    <p style="font-size: 12pt">
        Le informamos que hemos emitido la factura N# {{"001-".getDetallesClaveAcceso($numeroComprobante,'PUNTO_ACCESO')."-".getDetallesClaveAcceso($numeroComprobante,'SECUENCIAL')}}, la misma se encuentra adjuntada en el presente correo en su formato .XML y .PDF,
    </p>
</div>
