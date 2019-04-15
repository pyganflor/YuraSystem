create table despacho
(
    id_despacho            int auto_increment
        primary key,
    id_transportista       int                                  not null,
    id_camion              int                                  not null,
    id_conductor           int                                  not null,
    fecha_despacho         date                                 not null,
    sello_salida           varchar(30)                          not null,
    semana                 varchar(10)                          not null,
    rango_temp             varchar(20)                          null,
    n_viaje                int                                  not null,
    hora_salida            varchar(10)                          null,
    temp                   varchar(20)                          null,
    kilometraje            varchar(20)                          null,
    sellos                 varchar(300)                         not null,
    fecha_registro         datetime   default CURRENT_TIMESTAMP not null,
    horario                varchar(50)                          null,
    resp_ofi_despacho      varchar(100)                         not null,
    id_resp_ofi_despacho   varchar(20)                          not null,
    aux_cuarto_fri         varchar(100)                         not null,
    id_aux_cuarto_fri      varchar(20)                          null,
    guardia_turno          varchar(100)                         not null,
    id_guardia_turno       varchar(20)                          not null,
    asist_comercial_ext    varchar(100)                         not null,
    id_asist_comrecial_ext varchar(20)                          not null,
    resp_transporte        varchar(100)                         not null,
    id_resp_transporte     varchar(20)                          not null,
    n_despacho             varchar(20)                          not null,
    sello_adicional        varchar(20)                          null,
    estado                 tinyint(1) default 1                 not null,
    constraint FK_camion_despacho
        foreign key (id_camion) references camion (id_camion)
            on update cascade,
    constraint FK_conductor_despacho
        foreign key (id_conductor) references conductor (id_conductor)
            on update cascade,
    constraint FK_transportista_despacho
        foreign key (id_transportista) references transportista (id_transportista)
            on update cascade
);


