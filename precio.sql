create table precio
(
  id_precio                        int auto_increment
    primary key,
  id_cliente                       int                                  not null,
  id_detalle_especificacionempaque int                                  not null,
  cantidad                         varchar(50)                          not null,
  estado                           tinyint(1) default 1                 not null,
  fecha_registro                   datetime   default CURRENT_TIMESTAMP not null,
  constraint FK_Precio_Cliente
    foreign key (id_cliente) references cliente (id_cliente)
      on update cascade,
  constraint FK_Precio_DetalleEspecificacionEmpaque
    foreign key (id_detalle_especificacionempaque) references detalle_especificacionempaque (id_detalle_especificacionempaque)
      on update cascade
)
  collate = utf8_bin;

create index FK_Precio_ClasificacionRamo
  on precio (id_detalle_especificacionempaque);

create index FK_Precio_Variedad
  on precio (id_cliente);


