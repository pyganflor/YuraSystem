create table dato_exportacion
(
  id_dato_exportacion int auto_increment
    primary key,
  nombre              varchar(30)          not null,
  estado              tinyint(1) default 1 not null,
  constraint datos_exportacion_nombre_uindex
    unique (nombre)
);


