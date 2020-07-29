create table `binary`
(
    id        int auto_increment
        primary key,
    parent_id int            null,
    position  tinyint        null,
    path      varchar(12288) not null,
    level     int            not null
);

create index binary__path_index
    on `binary` (path);

INSERT INTO `binary` (parent_id, position, path, level) VALUES (null, null, '1', 1);