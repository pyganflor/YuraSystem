<div style="width: 90%;margin: 0 auto">
    <p style="font-size: 12pt">
        {{strtoupper(getConfiguracionEmpresa()->razon_social)}}
    </p>
    <p style="font-size: 12pt">
        Le informamos que ha ocurrido un inconveniente al enviar un comprobante electrónico al SRI para su aprobación, a continuación se detalla la descripción del comprobante enviado:
    </p>
        <ul>
            <li><b>Clave de acceso: </b> {{$clave_acceso}}</li>
            <li><b>Numero de comprobante: </b> {{getDetallesClaveAcceso($clave_acceso,'SERIE').getDetallesClaveAcceso($clave_acceso,'SECUENCIAL')}}</li>
        </ul>
    <p style="font-size: 12pt">
        Se ha adjuntado a este correo el archivo {{$clave_acceso.".xml"}}, dentro y al final de este se encontrará especificado el error por el cual el SRI no aprobó el documento electrónico
    </p>
</div>
