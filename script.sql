create table usf_languages
(
  id          smallint(6) auto_increment
    primary key,
  code2       varchar(2)   not null,
  code3       varchar(3)   null,
  name        varchar(100) not null,
  native_name varchar(100) null,
  is_active   tinyint      null comment '0',
  constraint languages_code2_uindex
    unique (code2),
  constraint languages_code3_uindex
    unique (code3),
  constraint languages_name_uindex
    unique (name)
);

create table usf_sessions
(
  id         int auto_increment
    primary key,
  token      varchar(32)                         not null,
  useragent  varchar(256)                        null,
  ip         varchar(15)                         null,
  start_time timestamp default CURRENT_TIMESTAMP not null,
  end_time   timestamp default CURRENT_TIMESTAMP not null,
  data       text                                null,
  user       int       default 0                 null,
  active     tinyint   default 1                 null,
  constraint usf_sessions_token_uindex
    unique (token)
);


