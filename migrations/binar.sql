create table binar
(
    id        int auto_increment
        primary key,
    parent_id int            null,
    position  tinyint        null,
    path      varchar(12288) not null,
    level     int            not null
);

create index binar__path_index
    on binar (path);