create table cliente_datoexportacion
(
  id_cliente_datoexportacion int auto_increment
    primary key,
  id_dato_exportacion        int not null,
  id_cliente                 int not null,
  constraint FK_cliente_datoexportacion
    foreign key (id_cliente) references cliente (id_cliente)
      on update cascade,
  constraint FK_dato_exportacion_cliente_datoexportacion
    foreign key (id_dato_exportacion) references dato_exportacion (id_dato_exportacion)
      on update cascade
);


