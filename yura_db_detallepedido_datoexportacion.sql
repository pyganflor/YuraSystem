create table detallepedido_datoexportacion
(
  id_detallepedido_datoexportacion int auto_increment
    primary key,
  id_detalle_pedido                int not null,
  id_dato_exportacion              int not null
);
